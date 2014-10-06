<?php

/**
 * Class for render company store * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.company_store */
class CompanyStoreHeaderBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_store_header';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $breadcrumbs = array();

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with company store here
            //
            //we check the page page_slug to determine what category model to use
            $page_slug = isset($_GET['page_slug']) ? plaintext($_GET['page_slug']) : '';
            $username = isset($_GET['username']) ? plaintext($_GET['username']) : '';
            $company = User::model()->find(array('condition' => 'username=:username AND user_type=1', 'params' => array(':username' => $username)));

            if ($company) {
               
                $this->render(BlockRenderWidget::setRenderOutput($this), array('company' => $company));
            } else {
                throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
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

}

?>