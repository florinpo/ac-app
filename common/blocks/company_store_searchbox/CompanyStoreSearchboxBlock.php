<?php

/**
 * Class for render company store searchbox * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.company_store_searchbox */
class CompanyStoreSearchboxBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_store_searchbox';
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
            //Start working with company store searchbox here

            $username = isset($_GET['username']) ? plaintext($_GET['username']) : '';
            $company = User::model()->find(array('condition' => 'username=:username AND user_type=1 AND status=1', 'params' => array(':username' => $username)));
            if ($company) {
                $search = new SiteSearchForm;
                $search->keyword = isset($_GET['q']) ? str_replace('-', ' ', $_GET['q']) : '';
                if (isset($_POST['SiteSearchForm'])) {
                    $search->unsetAttributes();
                    $search->type = $_POST['SiteSearchForm']['type'];
                    $search->keyword = $_POST['SiteSearchForm']['keyword'];
                    $stringSearch = encode($search->keyword, '-', false);
                    if ($_POST['SiteSearchForm']['type'] == 'cstore') {
                        Yii::app()->controller->redirect(array('site/store', 'username'=>$company->username, 'page_slug' => 'elenco-vendita', 'q' => $stringSearch));
                        //Yii::app()->controller->redirect(FRONT_SITE_URL . '?slug=prodotti-vendita&q='.$stringSearch);
                    } else if ($_POST['SiteSearchForm']['type'] == 'site') {
                        Yii::app()->controller->redirect(array('page/render', 'slug' => 'prodotti-vendita', 'q' => $stringSearch));
                    }
                }
                $this->render(BlockRenderWidget::setRenderOutput($this), array('search' => $search, 'company'=>$company));
            } else {
                throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
            }
        } else {
            echo '';
        }
    }

    public function getContentType($first = true, $company_name) {
        $types = array(
            'cstore' => Yii::t('CompanyStore', 'Only at') . ' ' . $company_name,
            'site' => Yii::t('CompanyStore', 'On') . ' ' . Yii::app()->settings->get('general', 'site_name'),
            );
        if ($first) {
            $result = array('0' => t('-Select type-'));
        } else {
            $result = array();
        }

        foreach ($types as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
    
    public function selectedCtype() {
        $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : 'home';
        $types = ContentType::getTypes();
        foreach ($types as $key => $value) {
            //$result[$key] = $value['slug'];
            if ($slug == $value['slug']) {
                $option = $key;
                return $option;
            }
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