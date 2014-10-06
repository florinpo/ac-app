<?php

/**
 * Class for render User Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.profile
 */
class NotificationConfirmBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'notification_confirm';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        //if (user()->hasFlash('success') || user()->hasFlash('error'))
            $this->renderContent();
//        else
//            app()->controller->redirect(array('page/render', 'slug' => 'dashboard'));
    }

    protected function renderContent($sent = false) {
        if (isset($this->block) && ($this->block != null)) {
            $this->render(BlockRenderWidget::setRenderOutput($this), array());
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

}

?>