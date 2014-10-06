<?php

class BillingController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array('label' => Yii::t('AdminMembershipItem','Manage Membership'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminMembershipItem','Create Membership'), 'url' => array('create'), 'linkOptions' => array('class' => 'button')),
        );
    }

    public function actionIndex() {
        $this->redirect(array('admin'));
    }
    public function actionCreate() {
        $this->render('membershipitem_create');
    }

    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminMembershipItem','Update membership'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminMembershipItem','View membership'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );

        $this->render('membershipitem_update');
    }

    public function actionAdmin() {
        $this->render('billing_admin');
    }

    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminMembershipItem','Update membership'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminMembershipItem','View membership'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );
        $this->render('membershipitem_view');
    }

    public function actionDelete($id) {
        GxcHelpers::deleteModel('MembershipItem', $id);
    }

}
