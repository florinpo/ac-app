<?php

/**
 * Class for render Site Search Product Filter * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.site_search_product_filter */
class SiteSearchProductFilterBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'site_search_product_filter';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $queryParameter = 'q';
    public $sphinx_index = 'products_sale';
    public $page_size = 10;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Site Search Product Filter here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);

            if ($this->getQuery() != null) {
                
                $category = !empty($_GET['cat']) ? numFromString($_GET['cat']) : '';
                $from = !empty($_GET['from']) ? numFromString($_GET['from']) : '';
                $province = !empty($_GET['province']) ? numFromString($_GET['province']) : '';
                $price = !empty($_GET['price']) ? $_GET['price'] : '';
                $discount = !empty($_GET['discount']) ? $_GET['discount'] : '';
                $membership = !empty($_GET['membership']) ? $_GET['membership'] : '';
                $minprice = !empty($_GET['minprice']) ? numFromString($_GET['minprice']) : '0';
                $maxprice = !empty($_GET['maxprice']) ? numFromString($_GET['maxprice']) : '9999999';
                

                $search = app()->search;
                $search->setSelect('*');
                
                $query = '@(name,tags) ' . $this->getQuery() . '';
                $search->setMatchMode(SPH_MATCH_EXTENDED2);
                $search->SetArrayResult(true);
                
                //$search->ResetGroupBy();
                //$search->resetFilters();
                $search->setFilter('status', array('1'));
                $search->setFilter('companystatus', array('1'));
                $search->addQuery($query, $this->sphinx_index);
                
                if ($category != '') {
                    $search->setFilter('categoryId', array($category));
                }
                if ($from != '') {
                    $search->setFilter('companytype', array($from));
                }
                if ($price == 'price') {
                    $search->setFilterRange('price', 1, 9999999);
                }
                if ($discount == 'discount') {
                    $search->setFilterRange('discount_price', 1, 9999999);
                }
                if ($minprice && $maxprice) {
                    $search->setFilterRange('price', $minprice, $maxprice);
                }
                if ($membership != '') {
                    $search->setFilter('has_membership', array(1));
                }

                
                if ($province != '') {
                    
                    $search->setFilter('provinceId', array($province));
                    
                    $search->addQuery($query, $this->sphinx_index);
                    
                    $search->resetFilter('provinceId');
                    $search->ResetGroupBy();

                    $search->setFilter('delivery_type', array(3));
                   
                }

                //$search->ResetFilters(); //we dont want the filter applying on the group by query
                //group by categoryId
                $search->SetGroupBy('categoryId', SPH_GROUPBY_ATTR, '@count DESC');
                $search->SetLimits(0, $this->page_size);
                $search->addQuery($query, $this->sphinx_index);

                //group by companytype
                $search->SetGroupBy('companytype', SPH_GROUPBY_ATTR, '@count DESC');
                $search->SetLimits(0, $this->page_size);
                $search->addQuery($query, $this->sphinx_index);

                $search->ResetGroupBy();
                $search->addQuery($query, $this->sphinx_index);
                $result = $search->RunQueries();
            }

            $this->render(BlockRenderWidget::setRenderOutput($this), array('result' => $result));
        } else {
            echo '';
        }
    }

    public function getQuery() {
        return isset($_REQUEST[$this->queryParameter]) ? $_REQUEST[$this->queryParameter] : null;
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