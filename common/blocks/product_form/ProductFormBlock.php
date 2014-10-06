<?php

/**
 * Class for render Member Company Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.member
 */
class ProductFormBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'product_form';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    private $_cs;

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
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            $this->registerLayout();
            if (!user()->isGuest) {
                $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

                $categoryForm = new ProductSaleCategoryForm;

                Yii::import("cms.extensions.xupload.models.XUploadForm");
                $files = new XUploadForm;
                $company = User::model()->findByPk(user()->id);
                $shop = $company->cshop;
                $model = new ProductSaleForm;
                $product = ProductSale::model()->find(array('condition' => 'id=:productId and shop_id=:shopId', 'params' => array(':productId' => $id, ':shopId' => $shop->id)));
                if ($product) {
                    //we check for old tags
                    $model->name = $product->name;
                    $model->model = $product->model;
                    $model->price = round2($product->price);
                    $model->discount_price = $product->discount_price > 0 ? round2($product->discount_price) : '';
                    $model->discount_rate = $product->discount_price > 0 ? ($product->price - $product->discount_price) * 100 / $product->price : '';
                    $model->min_quantity = $product->min_quantity;
                    $model->status = $product->status;
                    $model->description = $product->description;
                    $model->domain_id = $product->domain_id;
                    $model->selected_cats = !empty($product->categoryIds) ? implode(',', $product->categoryIds) : '';
                    $model->tags = $product->_oldTags;

                    if (isset($_POST['ProductSaleForm'])) {
                        $model->attributes = $_POST['ProductSaleForm'];

                        if ($model->validate()) {
                            $product->isNewRecord = false;
                            $product->scenario = 'updateWithTags';
                            $product->tags = $model->tags;
                            $product->name = ucfirst($model->name);
                            $product->price = $model->price;
                            //$product->status = ConstantDefine::PRODUCT_STATUS_PENDING;
                            $product->status = ConstantDefine::PRODUCT_STATUS_ACTIVE;
                            $product->currency = ConstantDefine::CURRENCY_EURO;
                            $product->description = $model->description;
                            $product->domain_id = $model->domain_id;
                            //$product->categories = explode(',', $model->selected_cats);
                            $product->categories = $model->category_id;
                            
                            $product->slug = toSlug($model->name);
                            if (!empty($model->has_discount)) {
                                $product->discount_price = $model->discount_price;
                                $product->min_quantity = $model->min_quantity;
                                if ($model->discount_duration != 0) {
                                    $product->expire_time = strtotime("+" . $model->discount_duration . " days");
                                }
                            } else {
                                $product->discount_price = 0;
                                $product->min_quantity = 1;
                                $product->expire_time = '';
                            }
                            if ($product->save()) {
                                user()->setFlash('success', t('site', 'Your product has been submitted to processing.
                                    We reserve the right to change/edit the product information if the content does not match our standards.'));
                                app()->controller->redirect(array('page/render', 'slug' => 'products-manage'));
                            }
                        }
                    } else {
                        $company->clearImagesSession();
                    }
                    $this->render(BlockRenderWidget::setRenderOutput($this), array(
                        'model' => $model,
                        'product' => $product,
                        'files' => $files,
                        'shop' => $shop,
                        'categoryForm' => $categoryForm
                    ));
                } else {
                    if (isset($_POST['ProductSaleForm'])) {
                        $model->attributes = $_POST['ProductSaleForm'];
                        // validate user input password
                        if ($model->validate()) {
                            $product = new ProductSale;
                            $product->shop_id = $shop->id;
                            $product->name = ucfirst($model->name);
                            $product->model = ucfirst($model->model);
                            $product->price = $model->price;
                            $product->status = ConstantDefine::PRODUCT_STATUS_ACTIVE;
                            $product->currency = ConstantDefine::CURRENCY_EURO;
                            $product->description = $model->description;
                            $product->domain_id = $model->domain_id;
                            //$product->categories = explode(',', $model->selected_cats);
                            $product->categories = $model->category_id;
                            $product->tags = $model->tags;
                            $product->slug = toSlug($model->name);
                            $product->discount_price = $model->discount_price;
                            $product->expire_time = strtotime("+" . $model->discount_duration . " days");

                            if ($product->save()) {
                                user()->setFlash('success', t('site', 'Your product has been submitted to processing.
                                    We reserve the right to change/edit the product information if the content does not match our standards.'));
                                app()->controller->redirect(array('page/render', 'slug' => 'products-manage'));
                            }
                        }
                    } else {
                        $company->clearImagesSession();
                    }
                    $this->render(BlockRenderWidget::setRenderOutput($this), array(
                        'model' => $model,
                        'files' => $files,
                        'product' => null,
                        'shop' => $shop,
                        'categoryForm' => $categoryForm
                    ));
                }
            }
        } else {
            echo '';
        }
    }

    public function registerLayout() {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerConfig();
        $this->_cs->registerCssFile($this->layout_asset . '/css/products/product-add-edit.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/tag-it.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.slimscroll.min.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/chosen.jquery.min.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . "/js/ckeditor/ckeditor.js", CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . "/js/ckeditor/adapters/jquery.js", CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/products/product-form.js', CClientScript::POS_END);
    }

    public function registerConfig() {
        
        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $tagsUrl = app()->createUrl('productsale/tagsautocomplete');
        $getChildrenUrl = app()->createUrl('productsale/getchildren');
        $productControllerUrl = app()->createUrl('productsale');

        $closeLabel = t('site', 'hide all notifications');

        // dialog labels
        $cancelLabel = t("site", "Cancel");
        $cancelDialogLabel = t("site", "Cancel");
        $confirmDialogLabel = t("site", "Yes, delete");

        $deleteConfirmTxt = t('site', 'Are you sure you want to delete the selected contact(s)?');
        $deleteConfirmTitle = t('site', 'Delete confirmation');

// set vars for javascript
        $js = <<<EOD
\$.productForm = {
     tagsUrl: '{$tagsUrl}',
     getChildrenUrl: '{$getChildrenUrl}',
     productControllerUrl: '{$productControllerUrl}',
     csrf:'{$csrf}',
     cancelLabel: '{$cancelLabel}',
     cancelDialogLabel: '{$cancelDialogLabel}',
     confirmDialogLabel: '{$confirmDialogLabel}',
     deleteConfirmTxt: '{$deleteConfirmTxt}',
     deleteConfirmTitle: '{$deleteConfirmTitle}'
     
};
EOD;
        $this->_cs->registerScript('productform-js', $js, CClientScript::POS_HEAD);
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