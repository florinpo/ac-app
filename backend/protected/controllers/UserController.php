<?php

/**
 * Backend User Controller.
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package backend.controllers
 *
 */
class UserController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array('label' => Yii::t('AdminUser', 'Manage Users'), 'url' => array('admin', 'user_type'=>0), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Create User'), 'url' => array('create'), 'linkOptions' => array('class' => 'button')),
        );
    }

    /**
     * Filter by using Modules Rights
     * 
     * @return type 
     */
    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * List of allowd default Actions for the user
     * @return type 
     */
    public function allowedActions() {
        //return 'login,logout';
    }


    /**
     * The function that do Change Password for authenticated user
     * 
     */
    public function actionChangePass() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
          $this->menu =  array(
            array('label' => Yii::t('AdminUser', 'Edit my profile'), 'url' => array('editprofile'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Change password'), 'url' => array('changepass'), 'linkOptions' => array('class' => 'button')), 
        );
        $this->render('change_pass');
    }

   /**
     * The function that do Update Profile for authenticated user
     * 
     */
    
    public function actionEditProfile() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu =  array(
            array('label' => Yii::t('AdminUser', 'Edit my profile'), 'url' => array('editprofile'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Change password'), 'url' => array('changepass'), 'linkOptions' => array('class' => 'button')), 
        );

        $this->render('edit_profile');
    }
    

    /**
     * The function that do Create new User
     * 
     */
    public function actionCreate() {
        $this->render('user_create');
    }
    
    /**
     * The function that do Manage User
     * 
     */
    public function actionAdmin() {
        $this->render('user_admin');
    }

    /**
     * The function that do View User
     * 
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
         $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminUser', 'User account'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'User profile'), 'url' => array('userprofile', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'View user'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
            )
        );
        $this->render('user_view');
    }

    /**
     * The function that do Update User
     * 
     */
    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

         $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminUser', 'User account'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'User profile'), 'url' => array('userprofile', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'View user'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
            )
        );
        $this->render('user_update');
    }
    
    /**
     * The function that do Update User Profile
     * 
     */
    public function actionUserProfile() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminUser', 'User account'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'User profile'), 'url' => array('userprofile', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'View user'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
            )
        );

        $this->render('user_edit_profile');
    }

    /**
     * The function is to Delete a User
     * 
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('User', $id);
    }
    
    /**
     * The function to populate dynamic dropdown
     * 
     */
    public function actionProvinceFromRegion() {
        $region_id = (int) $_POST['region_id'];
        Province::getProvinceFromRegion($region_id);
    }
    
    public function actionUncheckedRoles() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

        if (isset($_POST['roles'])) {
            $roles = json_decode($_POST['roles']);
            foreach ($roles as $role) {
                $authorizer = Yii::app()->getModule("rights")->authorizer;
                $authorizer->authManager->revoke($role, $id);
            }
        }
    }

    public function actionUncheckedMemberships() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;

        if (isset($_POST['memberships'])) {
            $memberships = json_decode($_POST['memberships']);
            // get the current membership
            foreach ($memberships as $m) {
                // echo $m;
                $role = MembershipItem::model()->findByPk($m);

                $authorizer = Yii::app()->getModule("rights")->authorizer;
                $authorizer->authManager->revoke($role->rolename, $id);
            }
        }
    }

}