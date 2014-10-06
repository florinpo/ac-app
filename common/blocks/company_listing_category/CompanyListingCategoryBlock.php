<?php

/**
 * Class for render Company Listing Category * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.company_listing_category */
class CompanyListingCategoryBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_listing_category';
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
            //Start working with Company Listing Category here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);


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

            $slug = 'catalogo-aziende';

            if (isset($_GET['cat']) && isset($_GET['subcat'])) {
                $subcat_id = numFromString($_GET['subcat']);
                if ($subcat_id) {
                    $subcategory = CompanyCats::model()->findByPk($subcat_id);
                    if ($subcategory) {
                        $criteria = new CDbCriteria();
                        $criteria->with = array(
                            'categories' => array(
                                'on' => 'categories.id=:catId',
                                'joinType' => 'INNER JOIN',
                                'params' => array(':catId' => $subcat_id),
                            ),
                            'company',
                            'provinces'
                        );
                        $criteria->together = true;
                        $criteria->join = "INNER JOIN gxc_user_company_profile as cprofile ON (cprofile.companyId = t.companyId)";

                        $criteria->addCondition('company.status=' . ConstantDefine::USER_STATUS_ACTIVE);
                        if (isset($_GET['membership']) == 'premium') {
                            $criteria->addCondition('company.has_membership=1');
                        }

                        if (isset($_GET['from'])) {
                            $domId = numFromString($_GET['from']);
                            $criteria->addCondition('cprofile.companytype=:companytype');
                            $criteria->params += array(
                                ':companytype' => $domId,
                            );
                        }

                        $criteriaS2 = $criteria;
                        if (isset($_GET['province'])) {
                            $provinceId = numFromString($_GET['province']);
                            $criteria->with += array(
                                'provinces' => array(
                                    'on' => 'provinces.id=:provinceId',
                                    'joinType' => 'INNER JOIN',
                                    'params' => array(':provinceId' => $provinceId)
                                )
                            );
                            $criteriaS2->addCondition('t.delivery_type=3');
                        }



                        $s1 = UserCompanyShop::model()->findAll($criteria);
                        $s2 = UserCompanyShop::model()->findAll($criteriaS2);



                        $s = array_merge_recursive_distinct($s1, $s2);
                        $model = new CArrayDataProvider($s,
                                        array('sort' => array(
                                                'defaultOrder' => 'company.has_membership DESC, cprofile.companyname ASC',
                                            ),
                                            'pagination' => array(
                                                'pageVar' => 'page',
                                                'pageSize' => app()->settings->get('system', 'page_size'),
                                            ),
                                ));

//                        $model = new CActiveDataProvider('UserCompanyShop', array(
//                                    'criteria' => $criteria,
//                                    'pagination' => array(
//                                        'pageVar' => 'page',
//                                        'pageSize' => app()->settings->get('system', 'page_size'),
//                                    )
//                                ));
                        $total = $model->getTotalItemCount();
                        // for Custom pagination
                        $pages = new CPagination($total);
                        $pages->setPageSize(app()->settings->get('system', 'page_size'));
                        $pages->applyLimit($criteria);  // the trick is here!
                    } else {
                        throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                    }
                } else {
                    throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                }
            } else if (isset($_GET['cat']) && !isset($_GET['subcat'])) {
                app()->controller->redirect(array('page/render', 'slug' => 'aziende', 'cat' => $_GET['cat']));
            } else if (!isset($_GET['cat']) && !isset($_GET['subcat'])) {
                app()->controller->redirect(array('page/render', 'slug' => 'aziende'));
            }

            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'category' => $subcategory,
                'model' => $model,
                'slug' => $slug,
                'total' => $total,
                'pageSize' => app()->settings->get('system', 'page_size'),
                'pages' => $pages,
                'cList' => $cList,
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