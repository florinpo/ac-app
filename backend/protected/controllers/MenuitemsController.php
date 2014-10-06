<?php

/**
 * Backend Menu Items Controller.
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package backend.controllers
 *
 */
class MenuitemsController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $menu = isset($_GET['menu']) ? (int) ($_GET['menu']) : null;
        $this->menu = array(
            array('label' => Yii::t('AdminMenu', 'Manage Menus'), 'url' => array('menu/admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminMenu', 'Manage Menu Items'), 'url' => array('admin', 'menu' => $menu), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminMenu', 'Add Menu Item'), 'url' => array('create', 'menu' => $menu), 'linkOptions' => array('class' => 'button')),
        );
    }

    /**
     * The function that do Create new Menu Item
     * 
     */
    public function actionCreate() {
        //$id=isset($_GET['menu']) ? (int)$_GET['menu'] : 0 ; 
        $this->render('menu_items_create');
    }

    /**
     * The function that do Manage Menu Item
     * 
     */
    public function actionAdmin() {
        $this->render('menu_items_admin');
    }

    /**
     * The function that update Menu Item
     * 
     */
    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminMenu', 'Update this Menu Item'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
                )
        );
        $this->render('menu_items_update', array());
    }

    /**
     * The function is to Delete Menu Item
     * 
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = MenuItems::model()->findByPk((int) $id);
            if ($model === null) {
                $this->getController()->redirect(array('admin'));
            }
            if ($model->hasManyRoots == false && $model->isRoot()) {
                $this->getController()->redirect(array('admin'));
            }
            $model->deleteNode();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
    // delete selected menu items
    public function actionDeleteSelected() {
        if (Yii::app()->request->isPostRequest) {
            $ids = isset($_POST['ids']) ? $_POST['ids'] : 0;
            $parent = isset($_POST['parentId']) ? $_POST['parentId'] : 0;
            if ($ids != 0 && $parent !=0) {
                foreach ($ids as $id) {
                    // we only allow deletion via POST request
                    $model = MenuItems::model()->findByPk((int) $id);
                    if ($model === null) {
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin', 'menu'=>$parent));
                    } else if ($model->hasManyRoots == false && $model->isRoot()) {
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin', 'menu'=>$parent));
                    }
                    $model->deleteNode();
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin', 'menu'=>$parent));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
    
    

    public function actionOrder($pk, $name, $value, $move) {
        $model = MenuItems::model()->findByPk((int) $pk);
        $previous_node_id = isset($model->prevSibling) ? (int) $model->prevSibling->id : 0;
        $next_node_id = isset($model->nextSibling) ? (int) $model->nextSibling->id : 0;

        $previous_node = MenuItems::model()->findByPk($previous_node_id);
        $next_node = MenuItems::model()->findByPk($next_node_id);

        if ($move === 'up') {
            $model->moveBefore($previous_node);
        } else if ($move === 'down') {
            $model->moveAfter($next_node);
        }
    }

    /**
     * This function sugget the Pages
     * 
     */
    public function actionSuggestPage() {
        Page::suggestPage();
    }

    /**
     * This function sugget the Object Content
     * 
     */
    public function actionSuggestContent() {
        MenuItem::suggestContent();
    }

    /**
     * This function sugget Terms
     * 
     */
    public function actionSuggestTerm() {
        MenuItem::suggestTerm();
    }

}