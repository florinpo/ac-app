<?php

class MessageController extends FeController {

    /**
     * List of allowd default Actions for the user
     * @return type 
     */
    public function allowedActions() {
        return 'addcontact, contactlist, reply, markread, markunread, markspam, marknospam, restore, 
               archive, delete, permanentdelete';
    }

    public function actionContactlist() {
        $q = strtolower($_GET["term"]);
        if (!$q)
            return;

        $items = array(
            "1" => "Admin",
            "2" => "Florin Pojum",
            "7" => "Vietta SRL",
        );

        $result = array();
        foreach ($items as $key => $value) {
            if (strpos(strtolower($value), $q) !== false) {
                array_push($result, array("id" => $key, "label" => $value, "value" => strip_tags($key)));
            }
            if (count($result) > 11)
                break;
        }
        echo json_encode($result);
    }

    public function actionAddcontact() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;

                // we check the user contacts
                $user = User::model()->findByPk(user()->id);
                $contacts = array();
                foreach ($user->contacts as $contact) {
                    $contacts[] = $contact->user_id;
                }

                $senders = Mailbox::conversationSenders($conversation_id, user()->id, 'inbox', false, false);
                if (count($senders) > 0) {
                    foreach ($senders as $k => $sender) {
                        if (!in_array($k, $contacts)) {
                            $contact = new ContactList;
                            $contact->owner_id = user()->id;
                            $contact->contact_id = $k;
                            $contact->save();
                            $count++;
                        }
                    }
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count contacts have been added to your list.', array(':count' => $count));
                } else {
                    $message = t('site', 'The contact has been added to your list.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Confirmation!'),
                        'redirect' => 0
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {

                $message = t('site', 'Please make sure the selected contacts are not already in your list.');

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function will handle the ajax reply in conversation view
     */
    public function actionReply() {
        if (Yii::app()->request->isPostRequest) {
            $body = $_POST['MessageForm']['body'];
            $conv_id = $_POST['MessageForm']['conversation_id'];
            $to = $_POST['MessageForm']['to'];
            $conv = Mailbox::conversation($conv_id);

            $reply = new Message;
            $reply->text = $body;
            $reply->conversation_id = $conv->conversation_id;
            $reply->recipients = explode(',', $to);
            $reply->sender_id = user()->id;
            $reply->created = time();
            $reply->crc64 = Message::crc64($body);

            //we also check if the message has been replied to a new interlocutor
            $newInterlocutors = array_diff(explode(',', $to), $conv->interlocutorIds);
            if (count($newInterlocutors) > 0) {
                $conv->interlocutors += $newInterlocutors;
            }
            $conv->modified = $reply->created;

            $validate = $reply->validate();
            $validate = $conv->validate() && $validate;

            if ($validate) {
                $conv->save();
                $reply->save();
                $message = t('site', 'Your message has been successfully sent.');
                $user = User::model()->findByPk($reply->sender_id);
                $sender = GxcHelpers::getDisplayName($reply->sender_id);
                $image = ($user->user_type == 0) ? $user->profile->selectedImage(100) : $user->cshop->selectedImage(100, 'frontend');
                $html = "<li class='message-item clearfix'><div class='thumbnail'>";
                $html .= Chtml::link($image, '');
                $html .= "</div><div class='data-wrap'><div class='sender'>";
                $html .= $sender;
                $html .= "<span class='sender-date'>" . date("j M Y, H:i", $reply->created) . "</span>";
                $html .= "<span>" . " " . t('site', 'ha risposto:') . "</span></div>";
                $html .= "<div class='content'>" . nl2br(makeLinks($reply->text)) . "</div>";
                $html .= "</div></li>";
                echo json_encode(array(
                    'success' => $message,
                    'header' => t('site', 'Conferma!'),
                    'output' => $html
                ));
                app()->end();
            } else {
                $message = t('site', 'Error while sending the message.');
                echo json_encode(array('error' => $message, 'header' => t('site', 'Errore!')));
                app()->end();
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function mark as read the selected conversations
     */
    public function actionMarkread() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->read(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->read(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been marked as read.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been marked as read.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 0,
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to mark the conversations as read.');
                } else {
                    $message = t('site', 'Error while trying to mark the conversation as read.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function mark as unread the selected conversations
     */
    public function actionMarkunread() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->unread(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->unread(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been marked as unread.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been marked as unread.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 0,
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to mark the conversations as unread.');
                } else {
                    $message = t('site', 'Error while trying to mark the conversation as unread.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }
    
    /**
     * this function add flag to selected conversations
     */
    public function actionAddflag() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->flag(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->flag(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been flagged.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been flagged.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 0,
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to flag the conversations.');
                } else {
                    $message = t('site', 'Error while trying to flag the conversation.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }
    
    /**
     * this function remove the flag from selected conversations
     */
    public function actionRemoveflag() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->unflag(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->unflag(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been unflagged.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been unflagged.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 0,
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to unflag the conversations.');
                } else {
                    $message = t('site', 'Error while trying to unflag the conversation.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                //$this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function delete the selected conversations
     */
    public function actionRestore() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->restore(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->restore(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been restored.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been restored.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to restore the conversations.');
                } else {
                    $message = t('site', 'Error while trying to restore the conversation.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!'),
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function is to archive the selected conversations
     */
    public function actionArchive() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->archive(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save(false))
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->archive(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save(false))
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been archived.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been archived.');
                }

                if (isset($_GET['ajax'])) {
                    user()->setFlash('info-ajax', $message);
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to archive the conversations.');
                } else {
                    $message = t('site', 'Error while trying to archive the conversation.');
                }

                if (isset($_GET['ajax'])) {
                    user()->setFlash('info-ajax', $message);
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function marks as spam the selected conversations
     */
    public function actionMarkspam() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];

            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $user = User::model()->findByPk(user()->id);

                // we check the user contacts
//                $user = User::model()->findByPk(user()->id);
//                $contacts = array();
//                foreach ($user->spammers as $contact) {
//                    $contacts[] = $contact->user_id;
//                }
//
//                if (isset($_POST['spam'])) {
//                    $senders = Mailbox::conversationSenders($conversation_id, user()->id, 'inbox', false, false);
//                    if (count($senders) > 0) {
//                        foreach ($senders as $k => $sender) {
//                            if (!in_array($k, $contacts)) {
//                                $contact = new MailboxSpam;
//                                $contact->user_id = user()->id;
//                                $contact->spammer_id = $k;
//                                $contact->save();
//                            }
//                        }
//                    }
//                }

                $conv = Mailbox::model()->findByPk($conversation_id);
//                $convInter = MailboxInterlocutor::model()->find(array(
//                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
//                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
//                        ));
               
                   
                    if ($conv->markSpam(user()->id, $folder) || $conv->validate())
                        
                    //Mailbox::spamUsers(user()->id, $conversation_id, $folder);
                        $count++;
               
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been marked as spam.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been marked as spam.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to mark the conversations as spam.');
                } else {
                    $message = t('site', 'Error while trying to mark the conversation as spam.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function unmarks as spam the selected conversations
     */
    public function actionMarknospam() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];
            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));

                $user = User::model()->findByPk(user()->id);
                $contacts = array();
                foreach ($user->spammers as $contact) {
                    $contacts[] = $contact->user_id;
                }

                $senders = Mailbox::conversationSenders($conversation_id, user()->id, 'inbox', false, false);
                if (count($senders) > 0) {
                    foreach ($senders as $k => $sender) {
                        if (in_array($k, $contacts)) {
                            MailboxSpam::model()->deleteAll(array(
                                'condition' => 'user_id=:userid AND spammer_id=:spammerid',
                                'params' => array(':userid' => user()->id, ':spammerid' => $k)
                                ));
                        }
                    }
                }

                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->unmarkSpam(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save(false))
                    //Mailbox::restoreSpamUsers(user()->id, $conversation_id, $folder);
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->unmarkSpam(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save(false))
                    //Mailbox::restoreSpamUsers(user()->id, $conversation_id, $folder);
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been unmarked as spam.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been unmarked as spam.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to unmark the conversations as spam.');
                } else {
                    $message = t('site', 'Error while trying to unmark the conversation as spam.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function delete the selected conversations
     */
    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];

            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->delete(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->delete(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been moved to the trash.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been moved to the trash.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while trying to move the conversations to the trash.');
                } else {
                    $message = t('site', 'Error while trying to move the conversation to the trash.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /**
     * this function delete permanently the selected conversations
     */
    public function actionPermanentdelete() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $folder = $_POST['folder'];

            foreach ($_POST['convs'] as $conversation_id) {
                if (!is_int($conversation_id = (int) $conversation_id))
                    continue;
                $conv = Mailbox::model()->findByPk($conversation_id);
                $convInter = MailboxInterlocutor::model()->find(array(
                    'condition' => 'conversation_id=:conversationId AND interlocutor_id=:userId',
                    'params' => array(':conversationId' => $conversation_id, ':userId' => user()->id)
                        ));
                if ($conv->initiator_id == user()->id) {
                    if (!$conv->belongsTo(user()->id))
                        continue;
                    if (!$conv->permanentDelete(user()->id) || !$conv->validate())
                        continue;
                    if ($conv->save())
                        $count++;
                } elseif ($convInter != null) {
                    if (!$convInter->belongsTo(user()->id))
                        continue;
                    if (!$convInter->permanentDelete(user()->id) || !$convInter->validate())
                        continue;
                    if ($convInter->save())
                        $count++;
                }
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count conversations have been deleted.', array(':count' => $count));
                } else {
                    $message = t('site', 'The conversation has been deleted.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 1,
                        'redirect_url' => app()->createUrl('page/render', array('slug' => 'messages', 'folder' => $folder))
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('success', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            } else {
                if ($count > 1) {
                    $message = t('site', 'Error while deleting the conversations.');
                } else {
                    $message = t('site', 'Error while deleting the conversation.');
                }

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
                user()->setFlash('error', $message);
                $this->controller->redirect(array('page/render', 'slug' => 'messages', 'folder' => $folder));
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

}