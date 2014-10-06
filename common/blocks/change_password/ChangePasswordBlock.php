<?php

/**
 * Class for render Account Page
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.account
 */
class ChangePasswordBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'change_password';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            $this->renderContent();
        } else {
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            $model = new UserChangePassForm;
            
            $u = User::model()->findbyPk(user()->id);
            if ($u !== null) {
                // collect user input data
                if (isset($_POST['UserChangePassForm'])) {
                    $model->attributes = $_POST['UserChangePassForm'];

                    // validate user input password
                    if ($model->validate()) {
                        $u->password = $model->new_password_1;
                        if ($u->save(false)) {
                            user()->setFlash('success', t('site', 'Password has been successfully changed!'));
                            app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
                        }
                    }
                }
                
            } else {
                throw new CHttpException('403', Yii::t('FrontendUser','This User does not exist'));
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