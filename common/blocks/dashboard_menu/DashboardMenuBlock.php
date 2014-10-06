<?php

/**
 * Class for render HTML Content Block
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.html
 */
class DashboardMenuBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'dashboard_menu';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            $userSession = user()->getState('current_user');
            if ($userSession['confirmed'] == 1) {
                $this->renderContent();
            } else {
                if ($userSession['user_type'] == ConstantDefine::USER_COMPANY) // we check if user is company
                    app()->controller->redirect(array('page/render', 'slug' => 'register-comp', 'op' => 'email-verification'));
                else
                    app()->controller->redirect(array('page/render', 'slug' => 'register-user', 'op' => 'email-verification'));
            }
        } else {
            user()->setFlash('error', t('cms', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Set Params from Block Params
            if (!user()->isGuest) {
                $user = User::model()->findByPk(user()->id);
                if (!$user) {
                    user()->logout();
                    app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
                }
            }
            $this->render(BlockRenderWidget::setRenderOutput($this), array('user' => $user));
        } else {
            echo '';
        }
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