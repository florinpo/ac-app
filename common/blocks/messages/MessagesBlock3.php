<?php

/**
 * Class for render User Profile
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.profile
 */
class MessagesBlock extends CWidget {

//Do not delete these attr block, page and errors
    public $id = 'messages';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $maxChars = 70;
    public $elipsis = '&#8230;';
    public $layout_asset = '';
    public $defaultSubject;
    public $folder;
    private $_cs;

    public function setParams($params) {
        return;
    }

    public function run() {
        if (!user()->isGuest) {
            $this->renderContent();
        } else {
            user()->setFlash('error', t('site', 'You need to sign in before continue'));
            app()->controller->redirect(array('page/render', 'slug' => 'sign-in'));
        }
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {

            $folder = isset($_GET['folder']) ? plaintext($_GET['folder']) : 'inbox';
            switch ($folder) {

                case "compose":

                    $folder = 'inbox'; // this will be the 

                    $renderArray = $this->composeRender($conversations, $folder);

                    break;

                case "sent":

                    $folder = 'sent';

                    if (isset($_GET['item-id'])) {
                        $id = isset($_GET['item-id']) ? (int) ($_GET['item-id']) : 0;
                        if ($id != 0)
                            $renderArray = $this->renderConversation($id, $folder);
                    } else {

                        $conversations = Mailbox::model()->sent(user()->id);
                        $renderArray = $this->renderList($conversations, $folder);
                    }
                    break;

                case "archived":

                    $folder = 'archived';

                    if (isset($_GET['item-id'])) {
                        $id = isset($_GET['item-id']) ? (int) ($_GET['item-id']) : 0;
                        if ($id != 0)
                            $renderArray = $this->renderConversation($id, $folder);
                    } else {

                        $conversations = Mailbox::model()->archived(user()->id);
                        $renderArray = $this->renderList($conversations, $folder);
                    }
                    break;

                case "trash":

                    $folder = 'trash';

                    if (isset($_GET['item-id'])) {
                        $id = isset($_GET['item-id']) ? (int) ($_GET['item-id']) : 0;
                        if ($id != 0)
                            $renderArray = $this->renderConversation($id, $folder);
                    } else {

                        $conversations = Mailbox::model()->trash(user()->id);
                        $renderArray = $this->renderList($conversations, $folder);
                    }
                    break;

                case "spam":

                    $folder = 'spam';

                    if (isset($_GET['item-id'])) {
                        $id = isset($_GET['item-id']) ? (int) ($_GET['item-id']) : 0;
                        if ($id != 0)
                            $renderArray = $this->renderConversation($id, $folder);
                    } else {

                        $conversations = Mailbox::model()->spammed(user()->id);
                        $renderArray = $this->renderList($conversations, $folder);
                    }
                    break;

                default:
                    // the default is inbox
                    $folder = 'inbox';

                    if (isset($_GET['action']) && $_GET['action'] != '') {
                        if ($_GET['action'] == 'view' && isset($_GET['item-id'])) {
                            $id = isset($_GET['item-id']) ? (int) ($_GET['item-id']) : 0;
                            if ($id != 0)
                                $renderArray = $this->renderConversation($id, $folder);
                        } else {
                            $renderArray = $this->renderCompose($folder);
                        }
                    } else {
                        $conversations = Mailbox::model()->inbox(user()->id);
                        $renderArray = $this->renderList($conversations, $folder);
                    }
            }


            if (Yii::app()->request->isAjaxRequest) {
                $this->render(BlockRenderWidget::setRenderOutput($this), $renderArray);
                Yii::app()->end();
            }
            else
                $this->render(BlockRenderWidget::setRenderOutput($this), $renderArray);
        } else {
            echo '';
        }
    }

    /*
     * return the render view array of conversation view item
     */

    public function renderCompose($folder) {

        $this->registerComposeLayout();

        $this->defaultSubject = t('site', 'No subject');
        $model = new MessageForm('compose');
        $conv = new Mailbox();
        $message = new Message();
        if (isset($_POST['MessageForm'])) {
            $model->attributes = $_POST['MessageForm'];

            if ($model->validate()) {

                $conv->interlocutors = explode(',', $model->to);
                $conv->subject = !empty($model->subject) ? $model->subject : $this->defaultSubject;
                $conv->initiator_id = user()->id;
                $conv->modified = time();
                $conv->bm_read = Mailbox::INITIATOR_FLAG;
//                if ($inSpam != null) {
//                    $conv->bm_spammed = Mailbox::INTERLOCUTOR_FLAG;
//                    $conv->interlocutor_spam = Mailbox::INTERLOCUTOR_FLAG;
//                }

                $message->text = $model->body;
                $validate = $conv->validate(); // html purify
                $message->created = time();
                $message->sender_id = $conv->initiator_id;
                $message->sender_read = Mailbox::INITIATOR_FLAG;
                $message->recipients = explode(',', $model->to);
                $message->crc64 = Message::crc64($message->text); // 64bit INT

                $validate = $conv->validate(null, false); // don't clear errors
                $validate = $message->validate() && $validate;

                if ($validate) {
                    $conv->save();
                    $message->conversation_id = $conv->conversation_id;
                    if ($message->save()) {
                        if (app()->user->hasState('recipients')) {
                            app()->user->setState('recipients', null);
                        }
                        user()->setFlash('success', "Message has been sent!");
                        app()->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
                    }
                } else {
                    user()->setFlash('error', "Error sending message!");
                }
            }
        }

        return array(
            'folder' => $folder, // [inbox, outbox, spambox, reciclebox]
            'view' => 'message_compose',
            'data' => array(
                'model' => $model
            )
        );
    }

    public function registerComposeLayout() {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerComposeConfig();
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.autosize.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.infieldlabel.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.jgrowl.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/tag-it.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.slimscroll.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . "/js/plugins/jquery.CharacterCounter.min.js", CClientScript::POS_HEAD);
        $this->_cs->registerCssFile($this->layout_asset . '/jquery-ui-default/css/jquery-ui-modal.css');
        $this->_cs->registerCssFile($this->layout_asset . '/css/plugins/jquery.jgrowl.css');
        $this->_cs->registerCssFile($this->layout_asset . '/css/plugins/jquery.tagit.css');
        $this->_cs->registerCssFile($this->layout_asset . '/css/mailbox.css');
        $this->_cs->registerCssFile($this->layout_asset . '/css/contact-list.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/mailbox/compose.js', CClientScript::POS_END);
    }

    public function registerComposeConfig() {

        $userid = user()->id;

        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }
        
        $closeLabel = t('site', 'hide all notifications');

        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $contactListUrl = app()->createUrl('contactlist/autocomplete');
        $messageUrl = app()->createUrl('message');
        $selectContactsUrl = app()->controller->createUrl('page/render', array('slug' => 'select-contacts'));



        // dialog labels
        $cancelDialogLabel = t("site", "Cancel");
        $okDialogLabel = t("site", "Ok, add selected");



// set vars for javascript
        $js = <<<EOD
\$.yiicompose = {
     selectContactsUrl : '{$selectContactsUrl}',
     contactListUrl: '{$contactListUrl}',
     messageUrl: '{$messageUrl}',
     csrf:'{$csrf}',
     notification: '{$notification}',
     notificationHeader: '{$notificationHeader}',
     notificationCloseLabel:'{$closeLabel}',
     currentUser: '{$userid}',
     cancelDialogLabel: '{$cancelDialogLabel}',
     okDialogLabel: '{$okDialogLabel}'
};
EOD;
        $this->_cs->registerScript('compose-js', $js, CClientScript::POS_HEAD);
    }

    /*
     * return the render view array of conversation view item
     */

    public function renderConversation($id, $folder) {

        $conv = Mailbox::conversation($id);
        
        $messages = $conv->messages(array('scopes' => array($folder => array(user()->id, 'ASC'))));


        if (count($messages) > 0) {
            $this->registerConversationLayout($id, $folder);
            $conv->read(user()->id, $folder);
            
            $reply = new Message;
            
            $models = array();

            foreach ($messages as $k => $message) {
                $c = $k;

                $models[$k] = new MessageForm('reply');

                if (!app()->request->isAjaxRequest && isset($_POST['MessageForm']) && $_REQUEST['counter'] == $k) {

                    $models[$k]->attributes = $_POST['MessageForm'];

//                     var_dump($k);
//                     exit();
                    if ($models[$k]->validate()) {
                        $reply->text = $models[$k]->body;
                        $reply->conversation_id = $conv->conversation_id;
                        if ($conv->initiator_id != user()->id) {
                            $reply->recipient_id = $conv->initiator_id;
                        } else {
                            $reply->recipient_id = $conv->interlocutor_id;
                        }
                        $reply->sender_id = user()->id;
                        $reply->sender_read = Mailbox::INITIATOR_FLAG;

                        $reply->created = time();
                        $conv->modified = $reply->created;

                        $reply->crc64 = Message::crc64($models[$k]->body);

                        $validate = $reply->validate();
                        $validate = $conv->validate() && $validate;
                        if ($validate) {
                            $conv->save();
                            $reply->save();
                            user()->setFlash('success', "Your message has been successfully sent");
                            app()->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder, 'action' => 'view', 'item-id' => $id));
                        } else {
                            Yii::app()->user->setFlash('error', "Error sending message!");
                        }
                    }
                }
            }
            return array(
                'folder' => $folder, // [inbox, outbox, spambox, reciclebox]
                'view' => 'message_view',
                'data' => array(
                    'conv' => $conv,
                    'messages' => $messages,
                    'models' => $models
                )
            );
        } else {
            throw new CHttpException(404, Yii::t('error', 'The requested page does not exist.'));
        }
    }

    public function registerConversationLayout($id, $folder) {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerConversationConfig($id, 'conversation', $folder);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.autosize.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.infieldlabel.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.jgrowl.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/tag-it.min.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . "/js/plugins/jquery.CharacterCounter.min.js", CClientScript::POS_HEAD);
        $this->_cs->registerCssFile($this->layout_asset . '/css/plugins/jquery.jgrowl.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/mailbox/conversation.js', CClientScript::POS_END);
        $this->_cs->registerCssFile($this->layout_asset . '/css/mailbox.css');
    }

    public function registerConversationConfig($id, $actionId, $folder) {

        $userid = user()->id;


        $senders = Mailbox::conversationSenders($id, $userid, $folder, false, true, true);

        $jsonMultipleSenders = json_encode($senders);
        $sendersMultipleLabels = MessagesBlock::getSendersString($id, $userid, $folder, true);

        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }
        
        $closeLabel = t('site', 'hide all notifications');

        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $conversationUrl = app()->createUrl('conversation');
        $messageUrl = app()->createUrl('message');
        $autocompleteUrl = app()->createUrl('message/autocomplete', array('conv-id' => $id, 'folder' => $folder));

        // dialog labels
        $cancelDialogLabel = t("site", "Nu");
        $okDialogLabel = t("site", "Da, continua operatiunea");

        //dialog text
        $spamTxt = t("site", "Ati marcat acest mesaj ca spam. Doriti de asemenea ca viitoarele mesaje ale acestui expeditor sa se regaseasca in folderul spam?");
        $spamTitle = t("site", "Markati expeditor spam?");

        $deleteTxt = t("site", "Sunteti sigur ca vreti sa stergeti definitiv aceasta conversatie?");
        $deleteTitle = t("site", "Stergeti definitiv conversatia?");

// set vars for javascript
        $js = <<<EOD
\$.yiiconversation = {
     confirmDelete:2,
     currentFolder:'{$folder}',
     conversationUrl: '{$conversationUrl}',
     messageUrl: '{$messageUrl}',
     autocompleteUrl: '{$autocompleteUrl}',
     csrf:'{$csrf}',
     notification: '{$notification}',
     notificationHeader: '{$notificationHeader}',
     currentUser: '{$userid}',
     jsonMultipleSenders: '{$jsonMultipleSenders}',
     sendersMultipleLabels : '{$sendersMultipleLabels}',
     cancelDialogLabel: '{$cancelDialogLabel}',
     okDialogLabel: '{$okDialogLabel}',
     spamTxt: '{$spamTxt}',
     spamTitle: '{$spamTitle}',
     deleteTxt: '{$deleteTxt}',
     deleteTitle: '{$deleteTitle}',
};
EOD;
        $this->_cs->registerScript('conversation-js', $js, CClientScript::POS_HEAD);
    }

    /*
     * return the render view array of conversations list [inbox, trashbox, spambox etc]
     */

    public function renderList($conversations, $folder) {
        $this->registerMailboxLayout($folder);
        $this->folder = $folder;

        $criteria = new CDbCriteria;

        $sort_date = isset($_GET['SortForm']['date']) ? $_GET['SortForm']['date'] : 'date';


        if ($sort_date == 'date') {
            $criteria->order = 'modified DESC';
        } else if ($sort_date == 'date_rev') {
            $criteria->order = 'modified ASC';
        }


        $sort_action = isset($_GET['SortForm']['action']) ? $_GET['SortForm']['action'] : 'none';

        if ($sort_action == 'unread') {
            $criteria->addCondition('(initiator_id=:userid AND initiator_read=0) OR (interlocutor_id=:userid AND interlocutor_read=0)');
        } else if ($sort_action == 'flagged') {
            $criteria->addCondition('(initiator_id=:userid AND initiator_flag>0) OR (interlocutor_id=:userid AND interlocutor_flag>0)');
        } else {
            $criteria->condition = '';
        }

        $criteria->params = array(
            ':userid' => user()->id,
        );

        $dataProvider = new CActiveDataProvider($conversations,
                        array(
                            'criteria' => $criteria,
                            'pagination' => array(
                                'pageVar' => 'page',
                                //'pageSize' => 2
                            'pageSize' => app()->settings->get('system', 'page_size')
                            )
                ));

        return array(
            'view' => 'mailbox',
            'folder' => $folder, // [inbox, outbox, spambox, reciclebox]
            'data' => array(
                'dataProvider' => $dataProvider,
                'sortDate' => $sort_date,
                'sortAction' => $sort_action
            )
        );
    }

    public function registerMailboxLayout($folder) {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerMailboxConfig('mailbox', $folder);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.jgrowl.js', CClientScript::POS_HEAD);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.uniform.js', CClientScript::POS_HEAD);
        $this->_cs->registerCssFile($this->layout_asset . '/css/plugins/jquery.jgrowl.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/mailbox/mailbox.js', CClientScript::POS_END);
        $this->_cs->registerCssFile($this->layout_asset . '/css/mailbox.css');
    }

    public function registerMailboxConfig($actionId, $folder) {
        $csrf = Yii::app()->getRequest()->getCsrfToken();
        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }

        $closeLabel = t('site', 'hide all notifications');

        $controllerUrl = app()->createUrl('conversation'); // this is the controller action
// set vars for javascript
        $js = <<<EOD
\$.yiimailbox = {
     confirmDelete:2,
     notification: '{$notification}',
     notificationHeader: '{$notificationHeader}',
     notificationCloseLabel:'{$closeLabel}',
     currentFolder:'{$folder}',
     controllerUrl: '{$controllerUrl}',
     csrf:'{$csrf}',
};
EOD;
        $this->_cs->registerScript('mailbox-js', $js, CClientScript::POS_HEAD);
    }

    public static function getSendersString($convId, $userid, $folder, $uname = true) {
        $senders = Mailbox::conversationSenders($convId, $userid, $folder, false, true, $uname);
        $ids = array();
        $labelNames = array();
        foreach ($senders as $k => $sender) {
            $ids[] = $k;
            $labelNames[] = $sender;
        }
        return implode(", ", $labelNames);
    }

    

    public function getStringDate() {
        $types = array(
            'date' => t('site', 'Newest'),
            'date_rev' => t('site', 'Oldest')
        );
        return $types;
    }

    public function getStringAction() {
        $types = array(
            'none' => t('site', 'All messages'),
            'unread' => t('site', 'Unread'),
            'flagged' => t('site', 'Flagged')
        );
        return $types;
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