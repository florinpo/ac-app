<?php

/**
 * Class for render Site Search Company Filter * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.site_search_company_filter */
class SiteSearchCompanyFilterBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'site_search_company_filter';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $queryParameter = 'q';
    public $sphinx_index = 'companies';
    public $page_size = 10;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Site Search Company Filter here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            if ($this->getQuery() != null) {


                $membership = !empty($_GET['membership']) ? '1' : '';
                $category = !empty($_GET['cat']) ? numFromString($_GET['cat']) : '';
                $from = !empty($_GET['from']) ? numFromString($_GET['from']) : '';
                $province = !empty($_GET['province']) ? numFromString($_GET['province']) : '';
                

                $search = app()->search;
                $search->setSelect('*');

                $query = '@(companyname,services) ' . $this->getQuery() . '';
                
                $search->setMatchMode(SPH_MATCH_EXTENDED2);
                $search->setArrayResult(true);
                $search->setFilter('status', array('1'));

                $search->ResetGroupBy();
                $search->resetFilters();
                $search->addQuery($query, $this->sphinx_index);

                

                if ($membership != '') {
                    $search->setFilter('has_membership', array($membership));
                }
                if ($category != '') {
                    $search->setFilter('categoryId', array($category));
                }
                if ($from != '') {
                    $search->setFilter('companytype', array($from));
                }

                
                if ($province != '') {
                    
                    $search->setFilter('provinceId', array($province));
                    
                    $search->addQuery($query, $this->sphinx_index);
                    
                    $search->resetFilter('provinceId');
                     //$search->ResetGroupBy();

                    $search->setFilter('delivery_type', array(3));
                   
                }

                //$search->ResetGroupBy();
                $search->SetGroupBy('categoryId', SPH_GROUPBY_ATTR, '@count DESC');
                $search->SetLimits(0, $this->page_size);
                $search->addQuery($query, $this->sphinx_index);

                
                //group by companytype

                $search->SetGroupBy('companytype', SPH_GROUPBY_ATTR, '@count DESC');
                $search->SetLimits(0, $this->page_size);
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