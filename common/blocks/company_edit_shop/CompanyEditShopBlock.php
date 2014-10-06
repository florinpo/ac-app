<?php

/**
 * Class for render Company Shop * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.company_shop */
class CompanyEditShopBlock extends CWidget {

//Do not delete these attr block, page and errors
    public $id = 'company_edit_shop';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
//            if (Yii::app()->user->hasState('current_user')) {
//                $current_user = Yii::app()->user->getState('current_user');
//                if ($current_user['user_type'] != ConstantDefine::USER_COMPANY) {
//                    user()->setFlash('error', t('site', 'This page is dedicated for companies only'));
//                    app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
//                } else {
//                    $this->renderContent();
//                }
//            }
            $this->renderContent();
        } else {
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {

            Yii::import("cms.extensions.xupload.models.XUploadForm");
            $files = new XUploadForm;
            $user = User::model()->findByPk(user()->id);
            $shop = $user->cshop;
            $model = new UserCompanyShopForm;
            $op = new ShopShipping;

//Set basic info for Current Company
            if ($user) {
                $model->description = $shop->description;
                $model->services = $shop->services;
                $model->certificate = $shop->certificate;
                $model->shipping_available = $shop->shipping_available;
                $model->delivery_type = $shop->delivery_type;
                $model->region_id = !empty($shop->provinces) && count($shop->provinces) == 1 ? Province::model()->findByPk($shop->provinces[0]->id)->regionId : '';
                $model->province_id = !empty($shop->provinces) && count($shop->provinces) == 1 ? $shop->provinces[0]->id : '';
                $model->selected_cats = !empty($shop->categoryIds) ? implode(',', $shop->categoryIds) : '';
                $model->selected_provinces = !empty($shop->provinceIds) ? implode(',', $shop->provinceIds) : '';
                $data = array();
                if (!empty($shop->ship_options)) {
                    foreach ($shop->ship_options as $option) {
                        array_push($data, $option->optionId);
                    }
                    $options = $data;
                } else {
                    $options = '';
                }
                $model->selected_shipopts = $options;
            } else {
                throw new CHttpException('503', 'User is not valid');
            }

// collect user input data
            if (isset($_POST['UserCompanyShopForm'])) {

                $model->attributes = $_POST['UserCompanyShopForm'];
// validate user input and redirect to the previous page if valid

                if ($model->validate()) {
                    $shop->description = $model->description;
                    $shop->services = $model->services;
                    $shop->certificate = $model->certificate;
                    $shop->shipping_available = $model->shipping_available;
                    $shop->delivery_type = $model->delivery_type;


                    $shop->categories = explode(',', $model->selected_cats);
                    $shop->provinces = explode(',', $model->selected_provinces);
                    if ($shop->save(false)) {
                        if ($_POST['UserCompanyShopForm']['delivery_type'] == ConstantDefine::DELIVER_OPTION_LOCAL) {
                            if (!empty($shop->provinces)) {
                                ShopProvince::model()->deleteAll('shopId = :id', array(':id' => $shop->id));

                                $shopProvince = new ShopProvince;
                                $shopProvince->shopId = $shop->id;
                                $shopProvince->provinceId = $model->province_id;
                                $shopProvince->save();
                            }
                        }
                        if ($_POST['UserCompanyShopForm']['delivery_type'] == ConstantDefine::DELIVER_OPTION_NATIONAL) {
                            if (!empty($shop->provinces)) {
                                ShopProvince::model()->deleteAll('shopId = :id', array(':id' => $shop->id));

                            }
                        }

                        $options = $_POST['UserCompanyShopForm']['selected_shipopts'];
                        $selected = array();
                        if (!empty($shop->ship_options)) {
                            foreach ($shop->ship_options as $option) {
                                array_push($selected, $option->optionId);
                            }
                        }
                        if (!empty($options)) {
                            foreach ($options as $k => $checked) {
                                if (!in_array($checked, $selected)) {
                                    $shipOption = new ShopShipping;
                                    $shipOption->optionId = $checked;
                                    $shipOption->shopId = $shop->id;
                                    $shipOption->save();
                                }
                            }

                            $unselectedIds = array_diff($selected, $options);
                            foreach ($unselectedIds as $k => $id) {
                                $unselected = ShopShipping::model()->find(array('condition' => 'optionId=:optionId', 'params' => array(':optionId' => $id)));
                                $unselected->delete();
                            }
                        } else {
                            if (!empty($selected)) {
                                foreach ($selected as $id) {
                                    $selected = ShopShipping::model()->find(array('condition' => 'optionId=:optionId', 'params' => array(':optionId' => $id)));
                                    $selected->delete();
                                }
                            }
                        }
                    }

                    user()->setFlash('success', t('site', 'Your company shop has been submited to processing.'));
                    app()->controller->redirect(array('page/render', 'slug' => 'notification-info'));
                }
            } else {
//we clear the images from session if the form was no submitted
                if (Yii::app()->user->hasState('images')) {
                    Yii::app()->user->setState('images', null);
                }
            }
        }

        $this->render(BlockRenderWidget::setRenderOutput($this), array(
            'model' => $model,
            'shop' => $shop,
            'user' => $user,
            'files' => $files
        ));
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