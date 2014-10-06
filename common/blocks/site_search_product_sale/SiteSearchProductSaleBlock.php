<?php

class SiteSearchProductSaleBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'site_search_product_sale';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    //Search attributes
    //public $with = array();
    public $queryParameter = 'q';
    public $sphinx_index = 'products_sale';
    public $page_size = 10;
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
                    user()->setState('display_products', 'grid');
                    app()->controller->redirect(Yii::app()->request->url);
                    exit(0);
                } else if ($display_type == 'list') {
                    user()->setState('display_products', 'list');
                    app()->controller->redirect(Yii::app()->request->url);
                    exit(0);
                }

                $cList = user()->getState('display_products');


                $category = !empty($_GET['cat']) ? numFromString($_GET['cat']) : '';
                $from = !empty($_GET['from']) ? numFromString($_GET['from']) : '';
                $province = !empty($_GET['province']) ? numFromString($_GET['province']) : '';
                $price = !empty($_GET['price']) ? $_GET['price'] : '';
                $discount = !empty($_GET['discount']) ? $_GET['discount'] : '';
                $membership = !empty($_GET['membership']) ? $_GET['membership'] : '';
                $minprice = !empty($_GET['minprice']) ? numFromString($_GET['minprice']) : '0';
                $maxprice = !empty($_GET['maxprice']) ? numFromString($_GET['maxprice']) : '9999999';

//                if (!empty($_GET['minp']) && empty($_GET['maxp'])) {
//                    $minUrl = add_url_param(array('minprice' => 'min-p-' . $_GET['minp']));
//                    app()->controller->redirect($minUrl);
//                    exit(0);
//                }
//                if (!empty($_GET['maxp']) && empty($_GET['minp'])) {
//                    $maxUrl = add_url_param(array('maxprice' => 'max-p-' . $_GET['maxp']));
//                    app()->controller->redirect($maxUrl);
//                     exit(0);
//                }
//                if (!empty($_GET['maxp']) && !empty($_GET['minp'])) {
//                    $minmaxUrl = add_url_param(array('minprice' => 'min-p-' . $_GET['minp'], 'maxprice' => 'max-p-' . $_GET['maxp']));
//                    app()->controller->redirect($minmaxUrl);
//                    exit(0);
//                }

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

                $search = app()->search;
                $search->setSelect('*');
                $search->SetFieldWeights(array(
                    'name' => 100,
                    'tags' => 50,
                    'description' => 10,
                ));

                $query = '@(name,tags) ' . $this->getQuery() . '';
                
                 $search->resetFilters();
                 $search->ResetGroupBy();

                $search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC");
                $search->setMatchMode(SPH_MATCH_EXTENDED2);
                $search->SetArrayResult(false);
                $search->setFilter('status', array('1'));
                $search->setFilter('companystatus', array('1'));
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
                    $search->setFilter('delivery_type', array(3));
                }


                $search->ResetGroupBy();

                $search->addQuery($query, $this->sphinx_index);
                $search->SetLimits($currentOffset, $this->page_size, max(1000, ceil(($currentOffset + $this->page_size) / 250) * 250)); //current page and number of results
                //$search->resetFilters();
                $search->addQuery($query, $this->sphinx_index);

                $results = $search->RunQueries();

                $items = array();
                foreach ($results as $resultset) {
                    if (isset($resultset['matches'])) {
                        foreach ($resultset['matches'] as $docid => $matchinfo) {
                            $items[$docid] = $matchinfo;
                            $items[$docid]['id'] = $docid;
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