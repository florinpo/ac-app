<?php

/**
 * Class for render Content based on Content list
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.html
 */
class ProductManageBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_manage';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $queryParameter = 'q';
    public $sphinx_index = 'products_sale';
    public $pageSize = 10;
    private $_cs;

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            if (Yii::app()->user->hasState('current_user')) {
                $current_user = Yii::app()->user->getState('current_user');
                if ($current_user['user_type'] != ConstantDefine::USER_COMPANY) {
                    user()->setFlash('error', t('site', 'This page is dedicated for companies only'));
                    app()->controller->redirect(array('page/render', 'slug' => 'notification'));
                } else {
                    $this->renderContent();
                }
            }
        } else {
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            $this->registerLayout();

            $search = new SiteSearchForm;
            $company = User::model()->findByPk(user()->id);
            $shop = $company->cshop;
            $search->keyword = isset($_GET['q']) ? str_replace('-', ' ', encode($_GET['q'], '-', false)) : '';
//            if (isset($_POST['SiteSearchForm'])) {
//                $search->unsetAttributes();
//                $search->keyword = $_POST['SiteSearchForm']['keyword'];
//                $stringSearch = encode($search->keyword, '-', false);
//                //Yii::app()->controller->redirect(array('render', 'slug' => plaintext($_GET['slug']), 'q' => $stringSearch));
//            }

            $sort_type = isset($_GET['type']) ? $_GET['type'] : 'none';

            //$status = isset($_GET['status']) ? $_GET['status'] : ConstantDefine::PRODUCT_STATUS_ACTIVE;
            if ($sort_type == 'active') {
                $status = ConstantDefine::PRODUCT_STATUS_ACTIVE;
            } else if ($sort_type == 'pending') {
                $status = ConstantDefine::PRODUCT_STATUS_PENDING;
            } else if ($sort_type == 'reqedit') {
                $status = ConstantDefine::PRODUCT_STATUS_REDIT;
            } 


            $criteria = new CDbCriteria;
            if(isset($status)) {
                
                $criteria->addCondition('t.status=' . $status);
            } 
          
            $criteria->addCondition('t.shop_id=:shopId');
            
            $criteria->order = 't.update_time DESC';
            $criteria->params = array(
                ':shopId' => $shop->id,
            );


            $sort_date = isset($_GET['date']) ? $_GET['date'] : 'update-desc';


            if ($sort_date == 'create-asc') {
                $criteria->order = 't.create_time ASC';
            } else if ($sort_date == 'create-desc') {
                $criteria->order = 't.create_time DESC';
            } else {
                $criteria->order = 't.update_time DESC';
            }


            if ($this->getQuery() != null) {

                //SphinxSearch criteria
                $searchCriteria = new stdClass();
                $searchCriteria->select = '*';
                $searchCriteria->query = '@(name,tags) ' . $this->getQuery() . '';
                $searchCriteria->from = $this->sphinx_index;
                $searchCriteria->paginator = null;

                $sphinx = app()->search;
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

            $dataProvider = new CActiveDataProvider('ProductSale', array(
                        'criteria' => $criteria,
                        'pagination' => array(
                            'pageVar' => 'page',
                            'pageSize' => 3
                        //'pageSize' => app()->settings->get('system', 'page_size')
                        )
                    ));

            // for Custom pagination
            $total = $dataProvider->getTotalItemCount();
            $pages = $dataProvider->getPagination();

            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'search' => $search,
                'dataProvider' => $dataProvider,
                'shop' => $shop,
                'total' => $total,
                'pages' => $pages,
                'pageSize' => 3
            ));
        } else {
            echo '';
        }
    }

    public function registerLayout() {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();

        $this->registerConfig();
        $this->_cs->registerCssFile($this->layout_asset . '/css/products/products-manage.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.infieldlabel.min.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.jgrowl.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.ibutton.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/products/products-manage.js', CClientScript::POS_END);
    }

    public function registerConfig() {
        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }

        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $tagsUrl = app()->createUrl('productsale/tagsautocomplete');
        $getChildrenUrl = app()->createUrl('productsale/getchildren');
        $productControllerUrl = app()->createUrl('productsale');

        $closeLabel = t('site', 'hide all notifications');
        $yesLabel = t('site', 'Si');
        $noLabel = t('site', 'No');

        // dialog labels
        $cancelLabel = t("site", "Cancela");
        $cancelDialogLabel = t("site", "Cancel");
        $okDialogLabel = t("site", "Yes, delete");

        $deleteConfirmTxt = t('site', 'products have been selected for deletion. Do you want to proceed with this operation ?');
        $deleteConfirmTitle = t('site', 'Delete confirmation');

// set vars for javascript
        $js = <<<EOD
\$.productsManage = {
     confirmDelete:1,
     notification: '{$notification}',
     notificationHeader: '{$notificationHeader}',
     notificationCloseLabel:'{$closeLabel}',
     productControllerUrl: '{$productControllerUrl}',
     csrf:'{$csrf}',
     cancelLabel: '{$cancelLabel}',
     cancelDialogLabel: '{$cancelDialogLabel}',
     yesLabel: '{$yesLabel}',
     noLabel: '{$noLabel}',
     okDialogLabel: '{$okDialogLabel}',
     deleteConfirmTxt: '{$deleteConfirmTxt}',
     deleteConfirmTitle: '{$deleteConfirmTitle}'
};
EOD;
        $this->_cs->registerScript('productsmanage-js', $js, CClientScript::POS_HEAD);
    }

    public function getQuery() {
        return isset($_REQUEST[$this->queryParameter]) ? $_REQUEST[$this->queryParameter] : null;
    }

    public function getStringStype() {
        $types = array(
            '1' => t('site', 'Newest'),
            '2' => t('site', 'Oldest'),
            '3' => t('site', 'With Price')
        );
        return $types;
    }

    public function countProducts($status) {
        $company = User::model()->findByPk(user()->id);
        $shop = $company->cshop;
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.shop_id=:shopId AND t.status=' . $status);
        $criteria->params = array(
            ':shopId' => $shop->id,
        );
        $products = ProductSale::model()->findAll($criteria);
        $total = count($products);
        return $total;
    }

    public function validate() {

        return true;
    }

    public function params() {
        return array(
        );
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

}

?>