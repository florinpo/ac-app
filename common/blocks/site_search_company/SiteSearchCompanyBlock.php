<?php

/**
 * Class for render Content based on Content list
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.html
 */
class SiteSearchCompanyBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'site_search_company';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    //Search attributes
    //public $with = array();
    public $queryParameter = 'q';
    public $sphinx_index = 'companies';
    public $page_size = 2;
    public $wordsLimit = 20;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {

            if ($this->getQuery() != null) {

                /* we set the user session display_type items */
                $display_type = isset($_POST['display_type']) ? $_POST['display_type'] : '';
                if ($display_type == 'grid') {
                    user()->setState('display_shops', 'grid');
                    app()->controller->redirect(Yii::app()->request->url);
                    exit(0);
                } else if ($display_type == 'list') {
                    user()->setState('display_shops', 'list');
                    app()->controller->redirect(Yii::app()->request->url);
                    exit(0);
                }

                $cList = user()->getState('display_shops');

                if (!empty($_GET['page'])) {
                    $currentPage = intval($_GET['page']);
                    if (empty($currentPage) || $currentPage < 1) {
                        $currentPage = 1;
                    }
                    $currentOffset = ($currentPage - 1) * $this->page_size;
                } else {
                    $currentPage = 1;
                    $currentOffset = 0;
                }

                $membership = !empty($_GET['membership']) ? '1' : '';
                $category = !empty($_GET['cat']) ? numFromString($_GET['cat']) : '';
                $from = !empty($_GET['from']) ? numFromString($_GET['from']) : '';
                $province = !empty($_GET['province']) ? numFromString($_GET['province']) : '';

                $search = app()->search;
                $search->setSelect('*, @weight-(NOW()-create_time)/86400 AS comp_weight');


                $query = '@(companyname,services) ' . $this->getQuery() . '';

                $search->setFieldWeights(array(
                    'companyname' => 30,
                    'services' => 20,
                    'description' => 10,
                ));
                $search->setMatchMode(SPH_MATCH_EXTENDED2);
                $search->setRankingMode(SPH_RANK_PROXIMITY_BM25);
                $search->setSortMode(SPH_SORT_EXTENDED, "has_membership DESC, comp_weight DESC, @weight DESC");

                $search->setArrayResult(false);
                $search->resetFilters();
                $search->setFilter('status', array('1'));

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

                    $search->setFilter('delivery_type', array(3));
                    //$search->addQuery($query, $this->sphinx_index);
                }



                $search->ResetGroupBy();

                $search->addQuery($query, $this->sphinx_index);
                $search->SetLimits($currentOffset, $this->page_size, max(1000, ceil(($currentOffset + $this->page_size) / 250) * 250)); //current page and number of results
                //$search->resetFilters();
                //$search->addQuery($query, $this->sphinx_index);

                $results = $search->RunQueries();

                $items = array();
                foreach ($results as $resultset) {
                    if (isset($resultset['matches'])) {
                        foreach ($resultset['matches'] as $docid => $matchinfo) {
                            $items[$docid] = $matchinfo;
                        }
                    }
                }

                $finalresults = array_slice($items, $currentOffset, $this->page_size);

                $resultCount = count($items);
                $pages = new CPagination($resultCount);
                $pages->pageSize = $this->page_size;
                $numPages = ceil($resultCount / $this->page_size);

                $this->render(BlockRenderWidget::setRenderOutput($this), array(
                    'results' => $results,
                    'finalresults' => $finalresults,
                    'items' => $items,
                    'resultCount' => $resultCount,
                    'pages' => $pages,
                    'currentPage' => $currentPage,
                    'numPages' => $numPages,
                    'cList' => $cList
                ));
            }
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