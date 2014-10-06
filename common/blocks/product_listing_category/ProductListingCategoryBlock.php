<?php

/**
 * Class for render Product Listing Category * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.product_listing_category */
class ProductListingCategoryBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_listing_category';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $wordsLimit = 20;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Product Listing Category here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);

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

            $slug = 'catalogo-prodotti';

            if (isset($_GET['cat']) && isset($_GET['subcat'])) {
                $subcat_id = numFromString($_GET['subcat']);
                if ($subcat_id) {
                    $subcategory = ProductSaleCategoryList::model()->findByPk($subcat_id);
                    if ($subcategory) {
                        $criteria = new CDbCriteria();

                        $criteria->with = array(
                            'categories' => array(
                                'on' => 'categories.id=:catId',
                                'joinType' => 'INNER JOIN',
                                'params' => array(':catId' => $subcat_id),
                            ),
                            'shop',
                            'company',
                            'cprofile'
                        );
                        $criteria->together = true;

                        $criteriaS2 = new CDbCriteria();
                        $criteriaS2->with = array(
                            'categories' => array(
                                'on' => 'categories.id=:catId',
                                'joinType' => 'INNER JOIN',
                                'params' => array(':catId' => $subcat_id),
                            ),
                            'shop',
                            'company',
                            'cprofile'
                        );
                        $criteriaS2->together = true;

                        $criteria->addCondition('company.status=' . ConstantDefine::USER_STATUS_ACTIVE);
                        $criteriaS2->addCondition('company.status=' . ConstantDefine::USER_STATUS_ACTIVE);
                        
                        $criteria->addCondition('t.status=' . ConstantDefine::PRODUCT_STATUS_ACTIVE);
                        $criteriaS2->addCondition('t.status=' . ConstantDefine::PRODUCT_STATUS_ACTIVE);
                        
                        if (isset($_GET['membership']) == 'premium') {
                            $criteria->addCondition('company.has_membership=1');
                            $criteriaS2->addCondition('company.has_membership=1');
                        }
                        
                        if (isset($_GET['discount']) == 'discount') {
                            $criteria->addCondition('t.discount_price <> 0');
                            $criteriaS2->addCondition('t.discount_price <> 0');
                        }
                        
                        if (isset($_GET['price']) == 'price') {
                            $criteria->addCondition('t.price <> 0');
                            $criteriaS2->addCondition('t.price <> 0');
                        }

                        if (isset($_GET['from'])) {
                            $domId = numFromString($_GET['from']);
                            $criteria->addCondition('cprofile.companytype=:companytype');
                            $criteria->params += array(
                                ':companytype' => $domId,
                            );
                            $criteriaS2->addCondition('cprofile.companytype=:companytype');
                            $criteriaS2->params += array(
                                ':companytype' => $domId,
                            );
                        }
                        
                        if (isset($_GET['province'])) {
                            $provinceId = numFromString($_GET['province']);

                            //$criteriaS2->addCondition('shop.delivery_type=3');
                            $criteria->with += array(
                                'shop.provinces' => array(
                                    'on' => 'provinces.id=:provinceId AND shop.delivery_type<>3',
                                    'joinType' => 'INNER JOIN',
                                    'params' => array(':provinceId' => $provinceId)
                                )
                            );


                            $criteriaS2->addCondition('shop.delivery_type=3');
//                            $s1 = ProductSale::model()->findAll($criteria);
//                            $s2 = ProductSale::model()->findAll($criteriaS2);
//                            $s = array_merge($s1, $s2);
                        }

                        $s1 = ProductSale::model()->findAll($criteria);
                        $s2 = ProductSale::model()->findAll($criteriaS2);
                        $s = array_merge_recursive_distinct($s1, $s2);


                        $dataProvider = new CArrayDataProvider($s,
                                        array('sort' => array(
                                                'defaultOrder' => 't.create_time ASC, company.has_membership DESC',
                                            ),
                                            'pagination' => array(
                                               'pageVar' => 'page',
                                               'pageSize' => app()->settings->get('system', 'page_size'),
                                            ),
                                ));
                        $total = $dataProvider->getTotalItemCount();
                        
                        // for Custom pagination
                        $pages = $dataProvider->getPagination();
                      
                    } else {
                        throw new CHttpException(404, t('error', 'The requested page does not exist.'));
                    }
                } else {
                    throw new CHttpException(404, t('error', 'The requested page does not exist.'));
                }
            } else if (isset($_GET['cat']) && !isset($_GET['subcat'])) {
                app()->controller->redirect(array('page/render', 'slug' => 'prodotti', 'cat' => $_GET['cat']));
            } else if (!isset($_GET['cat']) && !isset($_GET['subcat'])) {
                app()->controller->redirect(array('page/render', 'slug' => 'prodotti'));
            }

            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'category' => $subcategory,
                'dataProvider' => $dataProvider,
                'slug' => $slug,
                'total' => $total,
                'pageSize' => app()->settings->get('system', 'page_size'),
                'pages' => $pages,
                'cList' => $cList
            ));
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