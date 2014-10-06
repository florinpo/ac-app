<?php

/**
 * Class for render Ajax Response
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.reset_avatar
 */
class AjaxBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'ajax';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {

            if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-register-form') {
                $model = new UserRegisterForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                $model = new UserLoginForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'changepass-form') {
                $model = new UserChangePassForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-profile-form') {
                $model = new UserProfileForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-company-register-form1') {
                $model = new UserCompanyRegisterForm('step1');
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-company-register-form2') {
                $model = new UserCompanyRegisterForm('step2');
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'member-profile-form') {
                $model = new MemberProfileForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'company-profile-form') {
                $model = new UserCompanyProfileForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'company-shop-form') {
                $model = new UserCompanyShopForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-form') {
                $model = new ProductSaleForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'proforma-generate-form') {
                $model = new PaymentInfoForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'section-create-form') {
                $model = new ProductSaleSection;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['select-comp-cat'])) {
                $model = new CompanyCategoryForm;
                $model->setScenario('select-cat');
                if (isset($_POST['ajax']) && $_POST['ajax'] === 'cprofile-category-form') {

                    echo CActiveForm::validate($model);
                    Yii::app()->end();
                }
            } 
//            else if (isset($_POST['save-comp-cat'])) {
//                $model = new CompanyCategoryForm;
//                $model->setScenario('save-cat');
//                if (isset($_POST['ajax']) && $_POST['ajax'] === 'cprofile-category-form') {
//
//                    echo CActiveForm::validate($model);
//                    Yii::app()->end();
//                }
//            } else if (isset($_POST['select-product-cat'])) {
//                $model = new ProductSaleCategoryForm;
//                $model->setScenario('select-cat');
//                if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-category-form') {
//                    echo CActiveForm::validate($model);
//                    Yii::app()->end();
//                }
//            } 
            else if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-category-form') {
                $model = new ProductSaleCategoryForm;
                $model->setScenario('select-cat');
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['save-product-cat'])) {
                $model = new ProductSaleCategoryForm;
                $model->setScenario('save-cat');
                if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-category-form') {
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
                }
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'review-form') {
                $model = new ShopReviewForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            } else if (isset($_POST['ajax']) && $_POST['ajax'] === 'comment-form') {
                $model = new ProductSaleCommentForm;
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }


            if (isset($_REQUEST['form-c'])) {
                if (isset($_POST['ajax']) && $_POST['ajax'] === 'message-form-' . $_REQUEST['form-c']) {
                    $model = new MessageForm('reply');
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
                }
            } else {
                if (isset($_POST['ajax']) && $_POST['ajax'] === 'message-form') {
                    $model = new MessageForm('compose');
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
                }
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