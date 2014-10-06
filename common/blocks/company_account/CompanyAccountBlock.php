<?php

/**
 * Class for render Member Company Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class CompanyAccountBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_account';
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
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            if (!user()->isGuest) {
                $user = User::model()->findByPk(user()->id);
                $profile = $user->cprofile;
                $model = new MemberProfileForm;
                //Set basic info for Current Company
                if ($user) {
                    $model->firstname = $profile->firstname;
                    $model->lastname = $profile->lastname;
                    $model->companyposition = $profile->companyposition;
                    $model->companyname = $profile->companyname;
                    $model->companytype = $profile->companytype;
                    $model->domain_id = $profile->domain_id;
                    $model->vat_code = $profile->vat_code;
                    $model->region_id = $profile->region_id;
                    $model->province_id = $profile->province_id;
                    $model->location = $profile->location;
                    $model->adress = $profile->adress;
                    $model->postal_code = $profile->postal_code;
                    $model->phone = $profile->phone;
                    $model->mobile = $profile->mobile;
                    $model->fax = $profile->fax;
                    $model->website = $profile->website;
                } else {
                    throw new CHttpException('503', 'User is not valid');
                }

                // collect user input data
                if (isset($_POST['MemberProfileForm'])) {

                    $model->attributes = $_POST['MemberProfileForm'];
                    // validate user input and redirect to the previous page if valid                            
                    if ($model->validate()) {
                        $profile->firstname = $model->firstname;
                        $profile->lastname = $model->lastname;
                        $profile->companyposition = $model->companyposition;
                        $profile->companyname = $model->companyname;
                        $profile->companytype = $model->companytype;
                        $profile->domain_id = $model->domain_id;
                        $profile->vat_code = $model->vat_code;
                        $profile->region_id = $model->region_id;
                        $profile->province_id = $model->province_id;
                        $profile->location = $model->location;
                        $profile->adress = $model->adress;
                        $profile->postal_code = $model->postal_code;
                        $profile->phone = $model->phone;
                        $profile->mobile = $model->mobile;
                        $profile->fax = $model->fax;
                        $profile->website = $model->website;
                        $profile->save();
                        
                        $user->display_name =  $model->companyname;
                        $user->save(false);
                        
                        user()->setFlash('success', t('site', 'Your member profile has been successfully updated.'));
                        app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
                    }
                }
            }
            // collect user input data
            $this->render(BlockRenderWidget::setRenderOutput($this), array('model' => $model, 'user' => $user));
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