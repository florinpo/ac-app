<?php

class MembershipController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array('label' => t('cms', 'Manage Membership'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => t('cms', 'Create Membership'), 'url' => array('create'), 'linkOptions' => array('class' => 'button')),
        );
    }

    public function actionIndex() {
        $this->redirect(array('manageorders'));
    }

    public function actionCreate() {
        $this->render('membership_create');
    }

    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

        $this->menu = array_merge($this->menu, array(
            array('label' => t('cms', 'Update membership'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => t('cms', 'View membership'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );

        $this->render('membership_update');
    }

    public function actionAdmin() {
        $this->render('membership_admin');
    }

    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => t('cms', 'Update membership'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => t('cms', 'View membership'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );
        $this->render('membership_view');
    }

    public function actionDelete($id) {
        GxcHelpers::deleteModel('MembershipItem', $id);
    }

    public function actionManageOrders() {
        $this->menu = array();
        $this->render('membership_manage_orders');
    }

    public function actionViewOrder() {
        $this->menu = array(
            array('label' => t('cms', 'Manage membership orders'), 'url' => array('manageorders'), 'linkOptions' => array('class' => 'button')),
        );
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->render('membership_view_order');
    }

    public function actionUpdateOrder() {
        $this->menu = array(
            array('label' => t('cms', 'Manage membership orders'), 'url' => array('manageorders'), 'linkOptions' => array('class' => 'button')),
        );
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->render('membership_update_order');
    }

    public function actionDeleteOrder($id) {
        GxcHelpers::deleteModel('MembershipOrder', $id);
    }

    public function actionPrintOrder() {
        $this->pageTitle = t('cms', 'Membership Order Info');
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        
        $model = GxcHelpers::loadDetailModel('MembershipOrder', $id);
       //$this->renderPartial('membership_print_order', array('model'=> $model));
        if ($model && !empty($model->invoice_num)) {
            $fileName = $model->invoice_num;
            $file = BILLS_FOLDER . DIRECTORY_SEPARATOR . $fileName . '.pdf';

            $mPDF1 = Yii::app()->ePdf->mpdf('', 'A4');
            //$mPDF1->SetDisplayMode('fullpage');
            //$stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css') . '/screen.css');
            //$mPDF1->WriteHTML($stylesheet, 1);
            //$stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css') . '/custom.css');
            //$mPDF1->WriteHTML($stylesheet, 1);
            $mPDF1->WriteHTML($this->renderPartial('membership_print_order', array('model' => $model), true));
            $mPDF1->Output($file, EYiiPdf::OUTPUT_TO_FILE);


            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="'.$fileName.'.pdf"');
            readfile($file);
        }
    }

}
