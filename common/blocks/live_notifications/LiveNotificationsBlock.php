<?php

/**
 * Class for render Live Notification * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.live_notification */
class LiveNotificationsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'live_notifications';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $page_size = 2;

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            $this->renderContent();
        } else {
            user()->setFlash('error', t('cms', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Live Notification here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);

            $user = User::model()->findByPk(user()->id);
            if ($user) {
                $criteria = new CDbCriteria();
                $criteria->condition = 'user_id=:userId';
                $criteria->order = 'create_time DESC';
                $criteria->params = array(':userId' => $user->user_id);

                $itemsCount = Notification::model()->count($criteria);

                $pages = new CPagination($itemsCount);
                $pages->setPageSize($this->page_size);
                $pages->applyLimit($criteria);  // the trick is here!


                $model = Notification::model()->findAll($criteria);

                $dates = $this->notificationDates();
                $items = array();
                foreach ($model as $k => $m) {
                    $items['item-' . $k] = $m->body;
                    foreach ($dates as $j => $date) {
                        if ($date == $m->create_time) {
                            $items['date-'.$j] = $m->create_time;
                        }
                    }
                }


                $this->render(BlockRenderWidget::setRenderOutput($this), array(
                    'model' => $model,
                    //'items' => $items,
                    'dates' =>$dates,
                    'itemsCount' => $itemsCount,
                    'pages' => $pages,
                ));
            } else {
                throw new CHttpException('503', 'User is not valid');
            }
        } else {
            echo '';
        }
    }

    public function validate() {
        return true;
    }

    public function params() {
        return array();
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

    public function notificationDates() {
        $items = Notification::model()->findAll(array(
            'condition' => 'user_id=:userId',
            'params' => array(':userId' => user()->id),
            'order' => 'create_time DESC')
        );
        $arr = array();
        foreach ($items as $item) {
            //$arr[] = date('j M', $notification->create_time);
            $arr[] = $item->create_time;
        }
        return array_values(array_unique($arr, SORT_REGULAR));
    }

}

?>