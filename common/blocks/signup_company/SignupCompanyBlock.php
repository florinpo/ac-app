<?php

/**
 * Class for render Sign up Box
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.signup
 */
class SignupCompanyBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'signup_company';
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
            $model = new UserCompanyRegisterForm;
            // collect user input data
            if (isset($_POST['UserCompanyRegisterForm'])) {
                $model->attributes = $_POST['UserCompanyRegisterForm'];
                // validate user input password
                if ($model->validate()) {

                    $new_comp = new User;
                    $new_comp->username = $model->username;
                    $new_comp->email = $model->email;
                    $new_comp->display_name = $model->companyname;
                    $new_comp->password = $model->password;
                    $new_comp->display_name = Yii::app()->session['registerForm']['username'];
                    $new_comp->user_type = ConstantDefine::USER_COMPANY;
                    $new_comp->status = ConstantDefine::USER_STATUS_ACTIVE;
                    $new_comp->user_activation_key = md5(time() . $new_comp->username . USER_SALT);
                    
                    if ($new_comp->save()) {
                        $cprofile = new UserCompanyProfile;
                        $cprofile->companyId = $new_comp->user_id;
                        $cprofile->companyname = $model->companyname;
                        $cprofile->vat_code = $model->vat_code;
                        $cprofile->firstname = $model->firstname;
                        $cprofile->lastname = $model->lastname;
                        $cprofile->companytype = $model->companytype;
                        $cprofile->region_id = $model->region_id;
                        $cprofile->province_id = $model->province_id;
                        $cprofile->location = $model->location;
                        $cprofile->adress = $model->adress;
                        $cprofile->postal_code = $model->postal_code;
                        $cprofile->phone = $model->phone;
                        $cprofile->bank_name = $model->bank_name;
                        $cprofile->bank_iban = $model->bank_iban;
                        $cprofile->save();
                        
                        $cshop = new UserCompanyShop;
                        $cshop->companyId = $new_comp->user_id;
                        $cshop->save(false);

                        $csettings = new UserCompanySettings;
                        $csettings->companyId = $new_comp->user_id;
                        $csettings->email_news = $model->email_news;
                        $csettings->save(array('companyId'), false);
                        
                        app()->session['registerForm'] = array(
                            "username" => $_POST['UserCompanyRegisterForm']['username'],
                            "email" => $_POST['UserCompanyRegisterForm']['email'],
                            "password" => $_POST['UserCompanyRegisterForm']['password'],
                        );
                        app()->controller->redirect(array('page/render', 'slug' => 'register-comp', 'op' => 'email-verification'));
                    } else {
                        $this->addError('email', t('site', 'Something goes wrong with the Registration Process. Please try again later!'));
                        return false;
                    }
                }
            }
            /*             * * handle the captcha to refresh on page reload ** */
            $session = Yii::app()->session;
            $prefixLen = strlen(CCaptchaAction::SESSION_VAR_PREFIX);
            foreach ($session->keys as $key) {
                if (strncmp(CCaptchaAction::SESSION_VAR_PREFIX, $key, $prefixLen) == 0)
                    $session->remove($key);
            }
            /*             * ** */

            if (isset($_GET['op']) && ($_GET['op'] == 'email-verification')) {
                if (isset($_GET['key']) && isset($_GET['email'])) {
                    $email = $_GET['email'];
                    $key = $_GET['key'];
                    //Find the User
                    $user = User::model()->find(array('condition' => 'email=:email', 'params' => array(':email' => $email)));
                    if ($user) {
                        if ($user->confirmed == 0) {
                            //Ok We will check the key here
                            if ($user->user_activation_key == $key) {
                                $user->confirmed = 1;
                                if ($user->save(false)) {
                                    app()->controller->redirect(array('page/render', 'slug' => 'register-comp', 'op' => 'success'));
                                }
                            }
                        } else if ($user->confirmed == 1) {
                            app()->controller->redirect(array('page/render', 'slug' => 'dashboard'));
                        }
                    }
                    throw new CHttpException('503', 'Wrong Link');
                } else {
                    if (!empty(app()->session['registerForm'])) {
                        $this->render('common.blocks.signup_company.email-verification', array(
                            'username' => app()->session['registerForm']['username'],
                            'email' => app()->session['registerForm']['email'],
                        ));
                    } else if (!user()->isGuest) {
                        $this->render('common.blocks.signup_company.email-verification', array(
                            'username' => user()->username,
                            'email' => user()->email,
                        ));
                    } else {
                        app()->controller->redirect(array('page/render', 'slug' => 'dashboard'));
                    }
                }
            } else if (isset($_GET['op']) && ($_GET['op'] == 'success')) {
                if (!empty(app()->session['registerForm'])) {
                    $loginForm = new UserLoginForm;
                    $loginForm->username = app()->session['registerForm']['username'];
                    $loginForm->password = app()->session['registerForm']['password'];
                    if ($loginForm->validate() && $loginForm->login()) {
                        app()->clientScript->registerMetaTag("5;url=" . bu() . "/dashboard/", null, 'refresh');
                        unset(app()->session['registerForm']);
                    } else {
                        unset(app()->session['registerForm']);
                        app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
                    }
                    $this->render('common.blocks.signup_company.success', array());
                } else if (!user()->isGuest) {

                    app()->clientScript->registerMetaTag("5;url=" . bu() . "/dashboard/", null, 'refresh');

                    $this->render('common.blocks.signup_company.success', array());
                } else {
                    app()->controller->redirect(array('page/render', 'slug' => 'dashboard'));
                }
            }
            else
                $this->render(BlockRenderWidget::setRenderOutput($this), array('model' => $model));
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