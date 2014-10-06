<?php

/**
 * Class for render Sign up Box
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.signup
 */
class SignupUserBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'signup_user';
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
            $model = new UserRegisterForm;
            // collect user input data
            if (isset($_POST['UserRegisterForm'])) {
                $model->attributes = $_POST['UserRegisterForm'];
                // validate user input password
                if ($model->validate()) {
                    $new_user = new User;
                    $new_user->username = $model->username;
                    $new_user->display_name = $model->firstname . ' '. $model->lastname;
                    $new_user->email = $model->email;
                    $new_user->password = $model->password;
                    $new_user->display_name = $model->username;
                    $new_user->user_type = ConstantDefine::USER_NORMAL;
                    $new_user->status = ConstantDefine::USER_STATUS_ACTIVE;
                    $new_user->user_activation_key = md5(time() . $new_user->username . USER_SALT);

                    if ($new_user->save()) {
                        $profile = new UserProfile;
                        $profile->userId = $new_user->user_id;
                        $profile->firstname = $model->firstname;
                        $profile->lastname = $model->lastname;
                        $profile->save(false);

                        $settings = new UserSettings;
                        $settings->userId = $new_user->user_id;
                        $settings->email_news = $model->email_news;
                        $settings->save(array('userId, email_news'), false);

                        app()->session['registerForm'] = array(
                            "username" => $_POST['UserRegisterForm']['username'],
                            "email" => $_POST['UserRegisterForm']['email'],
                            "password" => $_POST['UserRegisterForm']['password'],
                        );
                        app()->controller->redirect(array('page/render', 'slug' => 'register-user', 'op' => 'email-verification'));

//                        $message = new YiiMailMessage;
//                        $message->view = 'registration';
//                        
//                        $message->subject = Yii::t('FrontendUser', 'Activate Account ').SITE_NAME;
//
//                        //userModel is passed to the view
//                        $message->setBody(array('new_user' => $new_user), 'text/html');
//
//
//                        $message->addTo($new_user->email);
//                        $message->from = Yii::app()->params['adminEmail'];
//                        Yii::app()->mail->send($message);
                    } else {
                        $this->addError('email', t('site', 'Something goes wrong with the Registration Process. Please try again later!'));
                        return false;
                    }
                }
            }
            /*** handle the captcha to refresh on page reload ***/
            $session = Yii::app()->session;
            $prefixLen = strlen(CCaptchaAction::SESSION_VAR_PREFIX);
            foreach($session->keys as $key)
            {
                    if(strncmp(CCaptchaAction::SESSION_VAR_PREFIX, $key, $prefixLen) == 0)
                            $session->remove($key);
            }
            /*****/
            
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
                                    app()->controller->redirect(array('page/render', 'slug' => 'register-user', 'op' => 'success'));
                                }
                            }
                        } else if ($user->confirmed == 1) {
                            app()->controller->redirect(array('page/render', 'slug' => 'dashboard'));
                        }
                    }
                    throw new CHttpException('503', 'Wrong Link');
                } else {
                    if (!empty(app()->session['registerForm'])) {
                        $this->render('common.blocks.signup_user.email-verification', array(
                            'username' => app()->session['registerForm']['username'],
                            'email' => app()->session['registerForm']['email'],
                        ));
                    } else if (!user()->isGuest) {
                        $this->render('common.blocks.signup_user.email-verification', array(
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
                    $this->render('common.blocks.signup_user.success', array());
                } else if (!user()->isGuest) {
                   
                    app()->clientScript->registerMetaTag("5;url=" . bu() . "/dashboard/", null, 'refresh');

                    $this->render('common.blocks.signup_user.success', array());
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