<?php

/**
 * Class for render Company Store Footer * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.company_store_footer */
class CompanyStoreFooterBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_store_footer';
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
            //Start working with Company Store Footer here
            $username = isset($_GET['username']) ? plaintext($_GET['username']) : '';
            $company = User::model()->find(array('condition' => 'username=:username AND user_type=1 AND status=1', 'params' => array(':username' => $username)));
            
            
            $this->render(BlockRenderWidget::setRenderOutput($this), array('company'=>$company));
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