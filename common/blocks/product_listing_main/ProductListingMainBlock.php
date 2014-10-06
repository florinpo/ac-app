<?php

/**
 * Class for render Content based on Content list
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.html
 */
class ProductListingMainBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_listing_main';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $scatname_length = 50; //for subcat name length
    public $wordsLimit = 30;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            $slug = plaintext($_GET['slug']);
            
            if (isset($_GET['cat'])) {
                $cat_id = numFromString($_GET['cat']);
                if ($cat_id) {
                    $category = ProductSaleCategoryList::model()->findByPk($cat_id);
                    if ($category) {
                        Yii::app()->controller->pageTitle = CHtml::encode($category->name);
                        Yii::app()->controller->change_title = true;
                        if (!empty($category->description)) {
                            Yii::app()->controller->description = CHtml::encode($category->description);
                        }
                        $this->render('common.blocks.product_listing_main.product_listing_main_subcatecategory_view', array('category'=>$category, 'slug'=>'catalogo-prodotti'));
                    } else {
                        throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                    }
                } else {
                    throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
                }
            } 
            else {
                $slugview = 'catalogo-prodotti';
                $categories = ProductSaleCategoryList::model()->findAll(array('order' => 'name ASC', 'condition' => 'level=1'));
                $this->render(BlockRenderWidget::setRenderOutput($this), array('categories' => $categories, 'slug'=>$slug, 'slugview'=>$slugview));
            }
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