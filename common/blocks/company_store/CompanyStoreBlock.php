<?php

/**
 * Class for render company store * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.company_store */
class CompanyStoreBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_store';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $wordsLimit = 50;
    public $itemwLimit = 18;
    public $queryParameter = 'q';
    public $sphinx_index = 'products_sale';
    public $pageSize = 10;
    public $menu = array();

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {



            //Start working with company store here
            $username = isset($_GET['username']) ? plaintext($_GET['username']) : '';
            $company = User::model()->find(array('condition' => 'username=:username AND user_type=1 AND status=1', 'params' => array(':username' => $username)));
            $shop = $company->cshop;

            $this->menu = array(
                array(
                    'label' => t('site', 'Scheda'),
                    'url' => array('site/store', 'username' => $company->username, 'slug' => 'store-view'),
                    'active' => isset($_GET['slug']) && $_GET['slug'] == 'store-view' ? true : false
                ),
                array(
                    'label' => t('site', 'Offerte'),
                    'url' => array('site/store', 'username' => $company->username, 'shop_page' => 'vendita'),
                    'active' => isset($_GET['shop_page']) && $_GET['shop_page'] == 'vendita' && !isset($_GET['prod_id']) ? true : false
                ),
                array(
                    'label' => t('site', 'Recensioni'),
                    'url' => array('site/store', 'username' => $company->username, 'shop_page' => 'recensioni'),
                    'active' => isset($_GET['shop_page']) && $_GET['shop_page'] == 'recensioni' ? true : false
                ),
                array(
                    'label' => t('site', 'Contatti'),
                    'url' => array('site/store', 'username' => $company->username, 'shop_page' => 'contatti'),
                    'active' => isset($_GET['shop_page']) && $_GET['shop_page'] == 'contatti' ? true : false
                )
            );


            $search = new SiteSearchForm;

            if (isset($company) && isset($shop)) {

                if (isset($_GET['shop_page']) && $_GET['shop_page'] == 'vendita' && !isset($_GET['prod_id'])) {

                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t.shopId=:shopId AND t.status=1');
                    $criteria->order = 't.id DESC';
                    $criteria->params = array(
                        ':shopId' => $shop->id,
                    );
                    if ($this->getQuery() != null) {
                        //SphinxSearch criteria
                        $searchCriteria = new stdClass();
                        $searchCriteria->select = '*';
                        $searchCriteria->query = '@(name,tags) ' . $this->getQuery() . '';
                        $searchCriteria->from = $this->sphinx_index;
                        $searchCriteria->paginator = null;

                        $sphinx = Yii::App()->search;
                        $sphinx->setMatchMode(SPH_MATCH_EXTENDED2);
                        $resArray = $sphinx->searchRaw($searchCriteria);

                        $values = array(0);
                        if (!empty($resArray['matches'])) {
                            foreach ($resArray['matches'] as $k => $v)
                                array_push($values, $k);
                        }
                        //var_dump($values); 
                        if (!empty($values)) {
                            $resCriteria = new CDbCriteria();
                            $resCriteria->addInCondition('t.id', $values);
                            $criteria->mergeWith($resCriteria);
                        }
                    }

                    $model = new CActiveDataProvider('ProductSale', array(
                                'criteria' => $criteria,
                                'pagination' => array(
                                    'pageVar' => 'page',
                                    'pageSize' => app()->settings->get('system', 'page_size')
                                //'pageSize' => 2
                                )
                            ));
                    $total = $model->getTotalItemCount();


                    $this->render('common.blocks.company_store.company_store_product_listing', array(
                        'company' => $company,
                        'model' => $model,
                        'total' => $total,
                        'shop' => $shop
                    ));
                } else if (isset($_GET['prod_id']) && !isset($_GET['opttype'])) {

                    $product = ProductSale::model()->find(array(
                        'condition' => 'id=:prodId and shopId=:shopId',
                        'params' => array(':prodId' => (int) $_GET['prod_id'], ':shopId' => $shop->id))
                    );
                    if ($product) {
                        // we add a new item to menu
                        $this->menu[] = array(
                            'label' => t('site', 'Vizualiza offerta'),
                            'url' => array(
                                'site/store', 'username' => $company->username,
                                'shop_page' => 'vendita',
                                'prod_id' => $product->id,
                                'prod_slug' => $product->slug),
                            'active' => isset($_GET['shop_page']) && $_GET['shop_page'] == 'vendita' && isset($_GET['prod_id']) ? true : false
                        );

                        $favOffer = FavoriteProduct::model()->find(array('condition' => 'productId=:productId AND userId=:userId', 'params' => array(':productId' => $product->id, ':userId' => user()->id)));
                        if (user()->isGuest) {
                            $url = app()->createUrl('site/store', array(
                                'username' => $company->username,
                                'shop_page' => 'vendita',
                                'prod_id' => $product->id,
                                'prod_slug' => $product->slug)
                            );
                            Yii::app()->user->setReturnUrl($url);
                        }
                        $favUsers = !empty($product->favusers) ? count($product->favusers) : '0';
                        $region = Region::model()->findByPk($company->cprofile->region_id)->name;
                        $province = Province::model()->findByPk($company->cprofile->province_id)->name;
                        app()->controller->pageTitle = CHtml::encode($product->name . ' - ' . $company->cprofile->companyname . ' (' . $product->id . ')');
                        app()->controller->change_title = true;
                        app()->controller->description = CHtml::encode($product->name . ' - ' . str_trim($product->description, 115, '...') . ' - ID(' . $product->id . '), ' . $company->cprofile->companyname . ', ' . $region . ', ' . $province . ', ' . $company->cprofile->location);

                        // random_products list
                        $random_products = ProductSale::model()->findAll(array(
                            'condition' => 'shopId=:shopId AND status=1 AND id <> :productId',
                            'params' => array(':shopId' => $shop->id, ':productId' => $product->id),
                            'order' => new CDbExpression('RAND()'),
                            'limit' => 8
                                ));


                        /**
                         * Product comments 
                         */
                        $model = new ProductSaleCommentForm;
                        $criteria = new CDbCriteria;
                        $criteria->addCondition('t.product_id=:productId AND t.status=1');
                        $criteria->params = array(
                            ':productId' => $product->id,
                        );
                        $criteria->with = array('user');
                        $criteria->together = true;


                        $sort_opt = isset($_GET['SortForm']['option']) ? $_GET['SortForm']['option'] : 1;

                        $current = 1;
                        if ($sort_opt == 1) {
                            $criteria->order = 't.create_time DESC';
                            $current = $sort_opt;
                        } else if ($sort_opt == 2) {
                            $criteria->order = 't.score DESC';
                            $current = $sort_opt;
                        } else if ($sort_opt == 3) {
                            $criteria->order = 'rating.rate DESC';
                            $current = $sort_opt;
                        } else if ($sort_opt == 4) {
                            $criteria->order = 'rating.rate ASC';
                            $current = $sort_opt;
                        }

                        $dataProvider = new CActiveDataProvider('ProductSaleComment', array(
                                    'criteria' => $criteria,
                                    'pagination' => array(
                                        'pageVar' => 'page',
                                        'pageSize' => 2
                                    )
                                ));

                        $total = $dataProvider->getTotalItemCount();


                        $this->render('common.blocks.company_store.company_store_product_sale_view', array(
                            'product' => $product,
                            'company' => $company,
                            'random_products' => $random_products,
                            'shop' => $shop,
                            'favOffer' => $favOffer,
                            'favUsers' => $favUsers,
                            'dataProvider' => $dataProvider,
                            'model' => $model
                        ));
                    } else {
                        throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                    }
                } else if (isset($_GET['prod_id']) && isset($_GET['opttype'])) {

                    $product = ProductSale::model()->find(array('condition' => 'id=:prodId and companyId=:companyId', 'params' => array(':prodId' => (int) $_GET['prod_id'], ':companyId' => $company->user_id)));

                    if ($product) {
                        if (user()->isGuest) {
                            $url = app()->createUrl('site/store', array(
                                'username' => $company->username,
                                'shop_page' => 'vendita',
                                'prod_id' => $product->id,
                                'prod_slug' => $product->slug)
                            );
                            Yii::app()->user->setReturnUrl($url);
                        }

                        // random_products list
                        $random_products = ProductSale::model()->findAll(array(
                            'condition' => 'companyId=:companyId AND status=1 AND id <> :productId',
                            'params' => array(':companyId' => $company->user_id, ':productId' => $product->id),
                            'order' => new CDbExpression('RAND()'),
                            'limit' => 8)
                        );

                        // Update product views
                        $product->views++;
                        $product->save(false);



                        $this->render('common.blocks.company_store.company_store_product_sale_full_img', array(
                            'product' => $product,
                            'company' => $company,
                            'random_products' => $random_products,
                            'shop' => $shop,
                        ));
                    } else {
                        throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                    }
                } else if (isset($_GET['shop_page']) && $_GET['shop_page'] == 'recensioni') {

                    if (user()->isGuest) {
                        $url = app()->createUrl('site/store', array(
                            'username' => $company->username,
                            'shop_page' => 'recensioni')
                        );
                        Yii::app()->user->setReturnUrl($url);
                    }


                    $model = new ShopReviewForm;
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t.shop_id=:shopId AND t.status=1');
                    $criteria->params = array(
                        ':shopId' => $shop->id,
                    );
                    $criteria->with = array('user', 'rating');
                    $criteria->together = true;


                    $sort_opt = isset($_GET['SortForm']['option']) ? $_GET['SortForm']['option'] : 1;

                    $current = 1;
                    if ($sort_opt == 1) {
                        $criteria->order = 't.create_time DESC';
                        $current = $sort_opt;
                    } else if ($sort_opt == 2) {
                        $criteria->order = 't.score DESC';
                        $current = $sort_opt;
                    } else if ($sort_opt == 3) {
                        $criteria->order = 'rating.rate DESC';
                        $current = $sort_opt;
                    } else if ($sort_opt == 4) {
                        $criteria->order = 'rating.rate ASC';
                        $current = $sort_opt;
                    }

                    $dataProvider = new CActiveDataProvider('ShopReview', array(
                                'criteria' => $criteria,
                                'pagination' => array(
                                    'pageVar' => 'page',
                                    'pageSize' => 2
                                )
                            ));

                    $total = $dataProvider->getTotalItemCount();
                    $this->render('common.blocks.company_store.company_store_reviews', array(
                        'model' => $model,
                        'company' => $company,
                        'shop' => $shop,
                        'current' => $current,
                        'dataProvider' => $dataProvider,
                        'total' => $total
                    ));
                } else {
                    $this->render(BlockRenderWidget::setRenderOutput($this), array('company' => $company, 'shop' => $shop, 'search' => $search));
                }
            } else {
                throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
            }
        } else {
            echo '';
        }
    }

    public function getStringStype() {
        $types = array(
            '1' => t('site', 'Date'),
            '2' => t('site', 'Utilita'),
            '3' => t('site', 'Voto decresente'),
            '4' => t('site', 'Voto cresente'),
        );
        return $types;
    }

    /**
     * for generating the menus as breadcrumbs
     */
    public function getAncestors($model) {
        $ancestors = array();
        $ancestors2 = array();
        if ($model->parent) {
            $ancestors[] = $model->parent;
            foreach ($ancestors as $ancestor) {
                if ($ancestor->parent) {
                    $ancestors2[] = $ancestor->parent;
                }
            }
        }
        if (count($ancestors2) > 0) {
            return $ancestors2;
        } else {
            return $ancestors;
        }
    }

    //public function for recursive lists
    public function getCategoryParents($id = null) {
        $childId = ($id === null) ? $owner->getAttribute($this->id) : $id;
        $model = CompanyCats::model()->findByPk($childId);
        if ($model === null)
            return null;
        $items = array();
        $parents = $this->getAncestors($model);
        foreach ($parents as $parent)
            $items[] = Chtml::link($parent->name, array('page/render', 'slug' => 'aziende', 'cat' => $parent->slug . '-' . $parent->id));
        if ($items !== array()) {
            $items[] = Chtml::link($model->name, array('page/render', 'slug' => 'catalogo-aziende', 'cat' => $parent->slug . '-' . $parent->id, 'subcat' => $model->slug . '-' . $model->id));
        }
        return implode('<span></span>', $items);
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