<?php

/**
 * Class for render Product View * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.product_view */
class ProductViewBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_view';
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
                    app()->controller->redirect(array('page/render', 'slug' => 'notification'));
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
            //Start working with Product View here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
            $product = ProductSale::model()->find(array(
                'condition' => 'id=:productId and companyId=:companyId',
                'params' => array(':productId' => $id, ':companyId' => user()->id))
            );
            $this->render(BlockRenderWidget::setRenderOutput($this), array('product'=>$product));
        } else {
            echo '';
        }
    }
    
    /*     * *
     * for generating the menus as breadcrumbs
     */

    public function getAncestors($model) {
        $ancestors = array();
        $ancestors2 = array();
        if ($model->parent) {
            $ancestors[] = $model->parent;
            foreach ($ancestors as $ancestor) {
                if ($ancestor->parent) {
                    $ancestors2[] = $ancestor->parent;
                }
            }
        }
        if (count($ancestors2) > 0) {
            return $ancestors2;
        } else {
            return $ancestors;
        }
    }

    //public function for recursive lists
    public function getCategoryParents($id = null) {
        $childId = ($id === null) ? $owner->getAttribute($this->id) : $id;
        $model = ProductSaleCategoryList::model()->findByPk($childId);
        if ($model === null)
            return null;
        $items = array();
        $parents = $this->getAncestors($model);
        foreach ($parents as $parent)
            $items[] = Chtml::link($parent->name, array('page/render', 'slug' => 'prodotti', 'cat' => $parent->slug . '-' . $parent->id));
        if ($items !== array()) {
            $items[] = Chtml::link($model->name, array('page/render', 'slug' => 'catalogo-prodotti', 'cat' => $parent->slug . '-' . $parent->id, 'subcat' => $model->slug . '-' . $model->id));
        }
        return implode('<span></span>', $items);
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