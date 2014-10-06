<?php

/**
 * Class for render HTML Content Block
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.html
 */
class CompanyStoreSidebarBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'company_store_sidebar';
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
            //Set Params from Block Params
            $username = isset($_GET['username']) ? plaintext($_GET['username']) : '';
            $company = User::model()->find(array('condition' => 'username=:username AND user_type=1', 'params' => array(':username' => $username)));
            $shop = $company->cshop;
            $favShop = FavoriteShop::model()->find(array('condition' => 'shopId=:shopId AND userId=:userId', 'params' => array(':shopId' => $shop->id, ':userId' => user()->id)));
            $countFavUsers = !empty($shop->favusers) ? count($shop->favusers) : '0';
            
            if($shop->countReviews>0){
              $averageRating = ($shop->countRating1*1 + $shop->countRating2*2 + $shop->countRating3*3
                        + $shop->countRating4*4 + $shop->countRating5*5) / $shop->countReviews;  
            } else {
                 $averageRating =0;
            }
            
            
            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'company' => $company,
                'shop' => $shop,
                'favShop' => $favShop,
                'countFavUsers' => $countFavUsers,
                'averageRating' => $averageRating
            ));
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
        $model = CompanyCats::model()->findByPk($childId);
        if ($model === null)
            return null;
        $items = array();
        $parents = $this->getAncestors($model);
        foreach ($parents as $parent)
            $items[] = Chtml::link($parent->name, array('page/render', 'slug' => 'aziende', 'cat' => $parent->slug . '-' . $parent->id));
        if ($items !== array()) {
            $items[] = Chtml::link($model->name, array('page/render', 'slug' => 'catalogo-aziende', 'cat' => $parent->slug . '-' . $parent->id, 'subcat' => $model->slug . '-' . $model->id));
        }
        return implode('<span></span>', $items);
    }

}

?>