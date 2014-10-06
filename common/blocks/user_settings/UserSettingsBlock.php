<?php

/**
 * Class for render Member Company settings
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class UserSettingsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'user_settings';
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
                if ($current_user['user_type'] != ConstantDefine::USER_NORMAL) {
                    user()->setFlash('error', t('site', 'This page is dedicated for users only'));
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

            $user = User::model()->findByPk(user()->id);
            $settings = $user->settings;
            $model = new UserSettingsForm;

            //Set basic info for Current Company
            //email_news, email_message, email_traffic, email_inquiry, email_status
            if ($user) {
                $model->email_news = $settings->email_news;
                $model->email_message = $settings->email_message;
                $model->email_public = $settings->email_public;
            } else {
                throw new CHttpException('503', 'User is not valid');
            }

            // collect user input data
            if (isset($_POST['UserSettingsForm'])) {

                $model->attributes = $_POST['UserSettingsForm'];

                // validate user input and redirect to the previous page if valid                            
                if ($model->validate()) {
                    $settings->email_news = $model->email_news;
                    $settings->email_message = $model->email_message;
                    $settings->email_public = $model->email_public;
                    $settings->save();
                    user()->setFlash('success', t('site', 'Your settings has been successfully updated!'));
                    app()->controller->redirect(array('page/render', 'slug' => 'notification'));
                }
            }


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