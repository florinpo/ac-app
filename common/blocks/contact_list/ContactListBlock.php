<?php

/**
 * Class for render Contact List * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.contact_list */
class ContactListBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'contact_list';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    private $_cs;
    //Contact list attribute
    public $display_type;

    //Display types for the list view render 

    const DISPLAY_TYPE_MAIN = 0;
    const DISPLAY_TYPE_COMPOSE = 1;

    public function setParams($params) {
        $this->display_type = isset($params['display_type']) ? $params['display_type'] : self::DISPLAY_TYPE_MAIN;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Contact List here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $this->registerLayout();

            $criteria = new CDbCriteria();
            $criteria->with = array('contact');
            $criteria->addCondition('owner_id=:userid AND contact.status=1');
            $criteria->params = array(':userid' => user()->id);
            
            $contacts = ContactList::model()->findAll($criteria);
            $total = count($contacts);
            

            $sort_name = isset($_GET['name']) ? $_GET['name'] : '';
            //$criteria->order = 'contact.display_name';

            if ($sort_name == 'desc') {
                $criteria->order = 'contact.display_name DESC';
            } else {
                $criteria->order = 'contact.display_name ASC';
            }

            $sort_status = isset($_GET['has_shop']) ? $_GET['has_shop'] : 0;

            if ($sort_status == '1') {
                $criteria->addCondition('contact.user_type=1');
            }

            $sort_premium = isset($_GET['premium']) ? $_GET['premium'] : 0;

            if ($sort_premium == '1') {
                $criteria->addCondition('contact.has_membership=1');
            }

            $dataProvider = new CActiveDataProvider('ContactList',
                            array(
                                'criteria' => $criteria,
                                'sort' => array(
                                    'defaultOrder' => 'contact.display_name ASC',
                                ),
                            )
            );

            


            if ($this->display_type == self::DISPLAY_TYPE_COMPOSE) {
                $this->render('common.blocks.contact_list.contact_list_compose', array('dataProvider' => $dataProvider, 'total' => $total));
            } else {
                $this->render('common.blocks.contact_list.contact_list_main', array('dataProvider' => $dataProvider, 'total' => $total));
            }
        } else {
            echo '';
        }
    }

    /*
     * this function is for list view registration layout
     */

    public function registerLayout() {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerConfig();
        if ($this->display_type == self::DISPLAY_TYPE_COMPOSE) {
            $this->_cs->registerScriptFile($this->layout_asset . "/js/bootstrap-checkbox.js", CClientScript::POS_HEAD);
            $this->_cs->registerScriptFile($this->layout_asset . '/js/mailbox/contact-list-compose.js', CClientScript::POS_END);
            
        } else {
            $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.jgrowl.js', CClientScript::POS_HEAD);
            $this->_cs->registerCssFile($this->layout_asset . '/css/plugins/jquery.jgrowl.css');
            $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.slimscroll.min.js', CClientScript::POS_HEAD);
            $this->_cs->registerCssFile($this->layout_asset . '/css/mailbox/contact-list.css');
            $this->_cs->registerScriptFile($this->layout_asset . '/js/mailbox/contact-list.js', CClientScript::POS_END);
        }
    }

    /*
     * this function is for list view configuration
     */

    public function registerConfig() {
        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }

        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $controllerUrl = app()->createUrl('contactlist'); // this is the controller action
        $closeLabel = t('site', 'hide all notifications');

        // dialog labels
        $cancelDialogLabel = t("site", "Cancel");
        $confirmDialogLabel = t("site", "Yes, delete");

        $deleteConfirmTxt = t('site', 'Are you sure you want to delete the selected contact(s)?');
        $deleteConfirmTitle = t('site', 'Delete confirmation');

// set vars for javascript
        $js = <<<EOD
\$.yiicontactlist = {
     confirmDelete:1,
     notification: '{$notification}',
     notificationHeader: '{$notificationHeader}',
     notificationCloseLabel:'{$closeLabel}',
     controllerUrl: '{$controllerUrl}',
     csrf:'{$csrf}',
     cancelDialogLabel: '{$cancelDialogLabel}',
     confirmDialogLabel: '{$confirmDialogLabel}',
     deleteConfirmTxt: '{$deleteConfirmTxt}',
     deleteConfirmTitle: '{$deleteConfirmTitle}'
     
};
EOD;
        $this->_cs->registerScript('contactlist-js', $js, CClientScript::POS_HEAD);
    }

    public function validate() {
        return true;
    }

    public function params() {
        return array(
            'display_type' => t('cms', 'Content list'),
        );
    }

    public static function getDisplayTypes() {
        return array(
            self::DISPLAY_TYPE_MAIN => t('cms', 'Display as main'),
            self::DISPLAY_TYPE_COMPOSE => t('cms', 'Compose popup'));
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

}

?>