<?php

/**
 * Class for render Sign up Box
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.signup
 */
class SiteSearchBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'site_search';
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
            $search = new SiteSearchForm;
            $search->keyword = isset($_GET['q']) ? str_replace('-', ' ', $_GET['q']) : '';
            if (isset($_POST['SiteSearchForm'])) {
                $search->unsetAttributes();
                $search->type = $_POST['SiteSearchForm']['type'];
                $search->keyword = $_POST['SiteSearchForm']['keyword'];
                $stringSearch = encode($search->keyword, '-', false);
                if ($_POST['SiteSearchForm']['type'] == 'product') {
                    Yii::app()->controller->redirect(array('page/render', 'slug' => 'cerca-prodotti', 'q' => $stringSearch));
                } else if ($_POST['SiteSearchForm']['type'] == 'company') {
                    Yii::app()->controller->redirect(array('page/render', 'slug' => 'cerca-aziende', 'q' => $stringSearch));
                }
            }
            $this->render(BlockRenderWidget::setRenderOutput($this), array('search' => $search));
        } else {
            echo '';
        }
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