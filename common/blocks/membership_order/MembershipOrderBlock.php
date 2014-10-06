<?php

/**
 * Class for render Membership Order * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.membership_order */
class MembershipOrderBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'membership_order';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Membership Order here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $p_100 = MembershipItem::model()->findByPk(1);
            $p_1500 = MembershipItem::model()->findByPk(2);
            $p_2500 = MembershipItem::model()->findByPk(3);
            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'p_100' => $p_100,
                'p_1500' => $p_1500,
                'p_2500' => $p_2500,
            ));
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