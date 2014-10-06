<?php

/**
 * Class for render Latest Products * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.latest_products */
class LatestProductsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'latest_products';
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
            //Start working with Latest Products here
        
            $criteria = new CDbCriteria();
            $criteria->addCondition('t.status=' . ConstantDefine::PRODUCT_STATUS_ACTIVE);
            $criteria->order = 't.create_time ASC';
           
            $duration = 60 * 60 * 24; // 1 day
            $products = Yii::app()->cache->get('last_products_sale');
            if ($products === false) {
                $products = ProductSale::model()->findAll($criteria);
                Yii::app()->cache->set('last_products_sale', $products, $duration);
            }
            $this->render(BlockRenderWidget::setRenderOutput($this), array('products' => $products));
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