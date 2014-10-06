<?php

/**
 * Class for render breadcrumbs * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.breadcrumbs */
class BreadcrumbsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'breadcrumbs';
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
            //Start working with breadcrumbs here
            //we check the page slug to determine what category model to use
            $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : '';
            $curPage = Page::model()->find(array(
                'condition' => 'slug=:paramId',
                'params' => array(':paramId' => $slug)));

            switch ($slug) {
                // Aziende breadcrumbs
                case "aziende":
                    if (isset($_GET['cat'])) {
                        $cat_name = $this->getCompanyCat($_GET['cat']);
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Aziende') => array('page/render', 'slug' => $slug),
                                    $cat_name
                                ));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Aziende')));
                    }
                    break;
                case "catalogo-aziende":
                    if (isset($_GET['cat']) && isset($_GET['subcat'])) {
                        $cat_name = $this->getCompanyCat($_GET['cat']);
                        $subcat_name = $this->getCompanyCat($_GET['subcat']);
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Aziende') => array('page/render', 'slug' => $slug),
                                    $cat_name => array('page/render', 'slug' => $slug, 'cat' => $_GET['cat']),
                                    $subcat_name
                                ));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Prodotti')));
                    }
                    break;
                case "cerca-aziende":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                t('site', 'Aziende') => array('page/render', 'slug' => 'aziende')
                            ));
                    break;
                // Prodotti breadcrumbs
                case "prodotti":
                    if (isset($_GET['cat'])) {
                        $cat_name = $this->getProductCat($_GET['cat']);
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Prodotti') => array('page/render', 'slug' => $slug),
                                    $cat_name
                                ));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Prodotti')));
                    }
                    break;
                case "catalogo-prodotti":
                    if (isset($_GET['cat']) && isset($_GET['subcat'])) {
                        $cat_name = $this->getProductCat($_GET['cat']);
                        $subcat_name = $this->getProductCat($_GET['subcat']);
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Prodotti') => array('page/render', 'slug' => $slug),
                                    $cat_name => array('page/render', 'slug' => $slug, 'cat' => $_GET['cat']),
                                    $subcat_name
                                ));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Prodotti')));
                    }
                    break;
                case "cerca-prodotti":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                t('site', 'Prodotti') => array('page/render', 'slug' => 'prodotti')
                            ));
                    break;
            }

            $this->render(BlockRenderWidget::setRenderOutput($this), array());
        } else {
            echo '';
        }
    }

    public function getProductCat($cat) {
        $cat_id = numFromString($cat);
        $category = ProductSaleCategoryList::model()->findByPk($cat_id);
        return ucfirst($category->name);
    }

    public function getCompanyCat($cat) {
        $cat_id = numFromString($cat);
        $category = CompanyCats::model()->findByPk($cat_id);
        return ucfirst($category->name);
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