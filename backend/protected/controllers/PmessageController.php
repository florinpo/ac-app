<?php

class PmessageController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array('label' => Yii::t('AdminNotification', 'Inbox {messages}', array('{messages}' => (PrivateMessage::model()->unreadMessages(user()->id) > 0) ? "(" . PrivateMessage::model()->unreadMessages(user()->id) . ")" : '')),
                'url' => array('inbox'),
                //'active' => ($this->action->id == 'inbox') ? true : false,
                'linkOptions' => array('class' => 'button')
            ),
            array('label' => Yii::t('AdminNotification', 'Sent'), 'url' => array('sent'),
                //'active' => ($this->id == 'bemessage') ? true : false,
                'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminNotification', 'Deleted {messages}', array('{messages}' => (PrivateMessage::model()->deletedMessages(user()->id) > 0) ? "(" . PrivateMessage::model()->deletedMessages(user()->id) . ")" : '')),
                'url' => array('deleted'),
                //'active' => ($this->id == 'bemessage') ? true : false,
                'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminNotification', 'Spam {messages}', array('{messages}' => (PrivateMessage::model()->spamMessages(user()->id) > 0) ? "(" . PrivateMessage::model()->spamMessages(user()->id) . ")" : '')),
                'url' => array('spam'),
                //'active' => ($this->id == 'bemessage') ? true : false,
                'linkOptions' => array('class' => 'button')),
        );
        return true;
    }

    public function actionIndex() {

        $this->redirect(array('inbox'));
    }

    public function actionInbox() {

        $this->render('message_inbox');
    }

    public function actionSent() {

        $this->render('message_sent');
    }

    public function actionSpam() {

        $this->render('message_spam');
    }

    public function actionReply() {
        $this->render('message_reply');
    }

    public function actionCompose() {
        $this->render('message_compose');
    }

    public function actionConfirm() {
        $this->render('message_confirm');
    }

    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->render('message_view');
    }

    public function actionDeleted() {
        $this->render('message_deleted');
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
                        $this->redirect(array($action));
                    }
                    if ($model->sender_id == user()->id) {
                        PrivateMessage::model()->updateByPk($id, array('senderDeleted' => '1'));
                    } else if ($model->receiver_id == user()->id) {
                        PrivateMessage::model()->updateByPk($id, array('receiverDeleted' => '1'));
                    }
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
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
                        $this->redirect(array($action));
                    }
                    if ($model->sender_id == user()->id) {
                         PrivateMessage::model()->updateByPk($id, array('senderMarkDeleted' => '1'));
                    } else if ($model->receiver_id == user()->id) {
                        PrivateMessage::model()->updateByPk($id, array('receiverMarkDeleted' => '1'));
                    }
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
    //delete individual
    public function actionDelete($id, $action) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = PrivateMessage::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array($action));
            }

            if ($model->sender_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('senderDeleted' => '1'));
            } else if ($model->receiver_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('receiverDeleted' => '1'));
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
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
                $this->redirect(array($action));
            }

            if ($model->sender_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('senderMarkDeleted' => '1'));
            } else if ($model->receiver_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('receiverMarkDeleted' => '1'));
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    // mark as spam individual
    public function actionMarkSpam($id, $action) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = PrivateMessage::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array($action));
            }

            if ($model->sender_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('receiverSpammed' => '1', 'senderMarkDeleted' => '0', 'update_time' => time()));
            } else if ($model->receiver_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('senderSpammed' => '1', 'receiverMarkDeleted' => '0', 'update_time' => time()));
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
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
                $this->redirect(array($action));
            }

            if ($model->sender_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('receiverSpammed' => '0', 'senderMarkDeleted' => '0', 'update_time' => time()));
            } else if ($model->receiver_id == user()->id) {
                PrivateMessage::model()->updateByPk($id, array('senderSpammed' => '0', 'receiverMarkDeleted' => '0', 'update_time' => time()));
            }


            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array($action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }


}
