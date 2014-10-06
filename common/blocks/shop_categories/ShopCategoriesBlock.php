<?php

/**
 * Class for render Member Company Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class ShopCategoriesBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'shop_categories';
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
            $user = User::model()->findByPk(user()->id);
            $shop = $user->cshop;
            $model = new CompanyCategoryForm;
            $this->render(BlockRenderWidget::setRenderOutput($this), array('model' => $model, 'user' => $user, 'shop'=>$shop));
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