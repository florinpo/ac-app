<?php

/**
 * Class for render Order * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.order */
class OrderBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'order';
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
            Yii::app()->user->setReturnUrl(Yii::app()->request->getUrl());
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Order here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $model = new PaymentInfoForm;
            $type = isset($_GET['type']) ? $_GET['type'] :'';
            $item_id = isset($_GET['item-id']) ? (int) $_GET['item-id'] : 0;
            
            $item_order = ''; // initialization
            if($type=='premium' && $item_id !=0) {
                $item_order = MembershipItem::model()->findByPk($item_id);
                if ($item_order!=null)
                    $model->item = $item_order->id;
            }
            
            if (isset($_POST['PaymentInfoForm'])) {
                $model->attributes = $_POST['PaymentInfoForm'];
                // validate user input password
                if ($model->validate()) {
                    $new_payment = new PaymentInfo;
                    $new_payment->company_id = user()->id;
                    $new_payment->product_id = $model->item;
                    $new_payment->product_type = $type;
                    $new_payment->last_name = $model->last_name;
                    $new_payment->first_name = $model->first_name;
                    $new_payment->email = $model->email;
                    $new_payment->company_name = $model->company_name;
                    $new_payment->company_position = $model->company_position; 
                    $new_payment->vat_code = $model->vat_code;
                    $new_payment->bank_name = $model->bank_name;
                    $new_payment->bank_number = $model->bank_number;
                    $new_payment->region_id = $model->region_id;
                    $new_payment->province_id = $model->province_id;
                    $new_payment->location = $model->location;
                    $new_payment->adress = $model->adress;
                    $new_payment->postal_code = $model->postal_code;
                    $new_payment->phone = $model->phone;
                    $new_payment->fax = $model->fax;
                    $new_payment->mobile = $model->mobile;
                    if($new_payment->save()){
                        app()->controller->redirect(array('page/render', 'slug' => 'notification'));
                        user()->setFlash('success', t('site', 'Your proforma has been successfuly generated.'));
                    }
                    
                }
            }
            $this->render(BlockRenderWidget::setRenderOutput($this), array('model'=>$model, 'item_order'=>$item_order));
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