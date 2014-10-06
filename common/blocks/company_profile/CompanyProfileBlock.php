<?php

/**
 * Class for render Member Company Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class CompanyProfileBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_profile';
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
                   user()->setFlash('error', t('site', 'This page is dedicated for companies only'));
                   app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
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

            Yii::import("cms.extensions.xupload.models.XUploadForm");
            $files = new XUploadForm;
            $user = User::model()->findByPk(user()->id);
            $shop = $user->cshop;
            $model = new UserCompanyShopForm;

            //Set basic info for Current Company
            if ($user) {
                $model->description = $shop->description;
                $model->services = $shop->services;
                $model->certificate = $shop->certificate;
                $model->selected_cats = !empty($user->categoryIds) ? implode(',', $user->categoryIds) : '';
            } else {
                throw new CHttpException('503', 'User is not valid');
            }

            // collect user input data
            if (isset($_POST['UserCompanyProfileForm'])) {

                $model->attributes = $_POST['UserCompanyProfileForm'];
                // validate user input and redirect to the previous page if valid                            
                if ($model->validate()) {
                    $shop->description = $model->description;
                    $shop->services = $model->services;
                    $shop->certificate = $model->certificate;
                    $shop->save(false);

                    $user->categories = explode(',', $model->selected_cats);
                    $user->save(false);

                    user()->setFlash('success', t('site', 'Your company profile has been submited to processing.'));
                    app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
                }
            } else {
                //we clear the images from session if the form was no submitted
                if (Yii::app()->user->hasState('images')) {
                    Yii::app()->user->setState('images', null);
                }
            }
        }

        $this->render(BlockRenderWidget::setRenderOutput($this), array(
            'model' => $model,
            'shop' => $shop,
            'user' => $user,
            'files' => $files
        ));
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