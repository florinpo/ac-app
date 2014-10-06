<?php

/**
 * Class for render Favorite * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.favorite */
class FavoriteBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'favorite';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            if (Yii::app()->user->hasState('current_user')) {
                $current_user = Yii::app()->user->getState('current_user');
                $this->renderContent();
            }
        } else {
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Favorite here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $page = isset($_GET['op']) ? plaintext($_GET['op']) : '';

            if ($page == 'negozi') {
                
                $criteria = new CDbCriteria();
                $criteria->with = array(
                    'favusers' => array(
                        'select' => false,
                        'joinType' => 'INNER JOIN',
                        'together' => true,
                        'condition' => 'favusers.user_id=:userId',
                        'params' => array(':userId' => user()->id),
                    ),
                    'company'
                );

                $criteria->addCondition('company.status=' . ConstantDefine::USER_STATUS_ACTIVE);
                $criteria->join = "INNER JOIN gxc_favorite_shop as favorite ON (favorite.shopId = t.id)";
                $criteria->order = 'favorite.create_time DESC';

                $model = new CActiveDataProvider('UserCompanyShop', array(
                            'criteria' => $criteria,
                            'pagination' => array(
                                'pageVar' => 'page',
                                'pageSize' => app()->settings->get('system', 'page_size')
                            )
                        ));
                $total = $model->getTotalItemCount();
                // for Custom pagination
                $pages = new CPagination($total);
                $pages->setPageSize(app()->settings->get('system', 'page_size'));
                $pages->applyLimit($criteria);  // the trick is here!

                
                $this->render(BlockRenderWidget::setRenderOutput($this).'_shops', array(
                    'model' => $model,
                    'total' => $total,
                    'pageSize' => app()->settings->get('system', 'page_size'),
                    'pages' => $pages
                ));
                
            } else {
                $criteria = new CDbCriteria();
                $criteria->with = array(
                    'favusers' => array(
                        'select' => false,
                        'joinType' => 'INNER JOIN',
                        'together' => true,
                        'condition' => 'favusers.user_id=:userId',
                        'params' => array(':userId' => user()->id),
                    ),
                );

                $criteria->addCondition('t.status=' . ConstantDefine::PRODUCT_STATUS_ACTIVE);
                $criteria->join = "INNER JOIN gxc_favorite_product as favorite ON (favorite.productId = t.id)";
                $criteria->order = 'favorite.create_time DESC';

                $model = new CActiveDataProvider('ProductSale', array(
                            'criteria' => $criteria,
                            'pagination' => array(
                                'pageVar' => 'page',
                                'pageSize' => app()->settings->get('system', 'page_size')
                            )
                        ));
                $total = $model->getTotalItemCount();
                // for Custom pagination
                $pages = new CPagination($total);
                $pages->setPageSize(app()->settings->get('system', 'page_size'));
                $pages->applyLimit($criteria);  // the trick is here!

                $this->render(BlockRenderWidget::setRenderOutput($this).'_products', array(
                    'model' => $model,
                    'total' => $total,
                    'pageSize' => app()->settings->get('system', 'page_size'),
                    'pages' => $pages
                    ));
            }
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