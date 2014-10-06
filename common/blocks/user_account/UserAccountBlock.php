<?php

/**
 * Class for render User Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.profile
 */
class UserAccountBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'user_account';
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

            $user = User::model()->findByPk(user()->id);
            $profile = UserProfile::model()->find(array(
                'condition' => 'userId=:userId',
                'params' => array(':userId' => user()->id)));

            $model = new UserProfileForm;
            Yii::import("cms.extensions.xupload.models.XUploadForm");
            $files = new XUploadForm;

            //Set basic info for Current user
            //Get the user by current Id
            if ($profile) {
                $model->firstname = $profile->firstname;
                $model->lastname = $profile->lastname;
                $model->gender = $profile->gender;
                $model->birthday = $profile->birthday;
                $model->region_id = $profile->region_id;
                $model->province_id = $profile->province_id;
                $model->location = $profile->location;
                $model->phone = $profile->phone;
            } else {
                throw new CHttpException('503', 'Profile is not valid');
            }

            // collect user input data
            if (isset($_POST['UserProfileForm'])) {
                $model->attributes = $_POST['UserProfileForm'];
                // validate user input and redirect to the previous page if valid                            
                if ($model->validate()) {
                    $profile->firstname = $model->firstname;
                    $profile->lastname = $model->lastname;
                    $profile->gender = $model->gender;
                    $profile->birthday = $model->birthday;
                    $profile->region_id = $model->region_id;
                    $profile->province_id = $model->province_id;
                    $profile->location = $model->location;
                    $profile->phone = $model->phone;
                    $profile->save();
                    
                    $user->display_name = $model->firstname . ' '.$model->lastname;
                    $user->save(false);

                    user()->setFlash('success', t('site', 'Your profile has been successfully updated!'));
                    app()->controller->redirect(array('page/render', 'slug' => 'notification'));
                }
            }


            // collect user input data

            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'model' => $model,
                'user' => $user,
                'profile' => $profile,
                'files' => $files)
            );
        } else {
            
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