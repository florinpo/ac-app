<?php

class MessageController extends FeController {

    /**
     * List of allowd default Actions for the user
     * @return type 
     */
    public function allowedActions() {
        return 'deleteSelected, marksDeleted, delete, markDelete, markSpam, noSpam';
    }

    //marks deleted selected messages
    public function actionMarksDeleted() {
        $ids = isset($_POST['ids']) ? $_POST['ids'] : 0;
        $action = $_POST['action'];
        if (Yii::app()->request->isPostRequest) {
            if ($ids != 0) {
                foreach ($ids as $id) {
                    // we only allow deletion via POST request
                    $model = PrivateMessage::model()->findByPk((int) $id);
                    if ($model === null) {
                        $this->redirect(array('page/render', 'slug' => $action));
                    }
                    if ($model->sender_id == user()->id) {
                        $model->senderMarkDeleted = 1;
                        $model->save(false);
                    } else if ($model->receiver_id == user()->id) {
                        $model->receiverMarkDeleted = 1;
                        $model->save(false);
                    }
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    //delete selected messages
    public function actionDeleteSelected() {
        $ids = isset($_POST['ids']) ? $_POST['ids'] : 0;
        $action = $_POST['action'];
        if (Yii::app()->request->isPostRequest) {
            if ($ids != 0) {
                foreach ($ids as $id) {
                    // we only allow deletion via POST request
                    $model = PrivateMessage::model()->findByPk((int) $id);
                    if ($model === null) {
                        $this->redirect(array('page/render', 'slug' => $action));
                    }
                    if ($model->sender_id == user()->id) {
                        $model->senderDeleted = 1;
                        $model->save(false);
                    } else if ($model->receiver_id == user()->id) {
                        $model->receiverDeleted = 1;
                        $model->save(false);
                    }
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    //mark delete individual
    public function actionMarkDelete($id, $action) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = PrivateMessage::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array('page/render', 'slug' => $action));
            }

            if ($model->sender_id == user()->id) {
                $model->senderMarkDeleted = 1;
                $model->save(false);
            } else if ($model->receiver_id == user()->id) {
                $model->receiverMarkDeleted = 1;
                $model->save(false);
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                if (!$conv->belongsTo(user()->id))
                    continue;
                if (!$conv->delete(user()->id) || !$conv->validate())
                    continue;
                if ($conv->save())
                    $count++;
            }

            if ($count) {
                $message = t('site', ':count messages has been deleted', array(':count' => $count));
                if (isset($_GET['ajax'])) {
                    echo json_encode(array('success' => $message));
                    Yii::app()->end();
                }
                //$this->redirect(array($this->controller->getId().'/'.$box));
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

//    //delete individual
//    public function actionDelete($id, $action) {
//        if (Yii::app()->request->isPostRequest) {
//            // we only allow deletion via POST request
//            $model = PrivateMessage::model()->findByPk((int) $id);
//            if ($model === null) {
//                $this->redirect(array('page/render', 'slug' => $action));
//            }
//
//            if ($model->sender_id == user()->id) {
//                $model->senderDeleted = 1;
//                $model->save(false);
//            } else if ($model->receiver_id == user()->id) {
//                $model->receiverDeleted = 1;
//                $model->save(false);
//            }
//
//
//            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//            if (!isset($_GET['ajax']))
//                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
//        }
//        else
//            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
//    }
    // mark as spam individual
    public function actionMarkSpam($id, $action) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = PrivateMessage::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array('page/render', 'slug' => $action));
            }

            if ($model->sender_id == user()->id) {
                $model->receiverSpammed = 1;
                $model->senderMarkDeleted = 0;
                $model->save(false);
            } else if ($model->receiver_id == user()->id) {
                $model->senderSpammed = 1;
                $model->receiverMarkDeleted = 0;
                $model->save(false);
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    // mark as spam individual
    public function actionNoSpam($id, $action) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = PrivateMessage::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array('page/render', 'slug' => $action));
            }

            if ($model->sender_id == user()->id) {
                $model->receiverSpammed = 0;
                $model->senderMarkDeleted = 0;
                $model->save(false);
            } else if ($model->receiver_id == user()->id) {
                $model->senderSpammed = 0;
                $model->receiverMarkDeleted = 0;
                $model->save(false);
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

}