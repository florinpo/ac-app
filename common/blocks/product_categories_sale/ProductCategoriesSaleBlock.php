<?php

/**
 * Class for render Member Company Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class ProductCategoriesSaleBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_categories_sale';
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
                if ($current_user['user_type'] != ConstantDefine::USER_COMPANY) {
                    throw new CHttpException(403, Yii::t('error', 'Sorry this page is available for companies only'));
                } else {
                    $this->renderContent();
                }
            }
        } else {
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $model = new ProductSaleCategoryForm;
            $company = User::model()->findByPk(user()->id);
            $shop = $company->cshop;
            if ($product_id != 0)
                $product = ProductSale::model()->find(array('condition' => 'id=:productId AND shopId=:shopId', 'params' => array(':productId' => $product_id, ':shopId' => $shop->id)));
            else
                $product = null;
            $this->render(BlockRenderWidget::setRenderOutput($this), array('model' => $model, 'product' => $product));
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