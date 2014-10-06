<?php

/**
 * Class for render Dashboard Breadcrumbs * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.dashboard_breadcrumbs */
class DashboardBreadcrumbsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'dashboard_breadcrumbs';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $breadcrumbs = array();

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
            //Start working with Dashboard Breadcrumbs here
            $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : '';

            $this->breadcrumbs = array(t('site', 'My account') => array('page/render', 'slug' => 'dashboard'));

            switch ($slug) {
              case "member-profile":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Member profile')));
                    break;
                 case "company-profile":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Company profile')));
                    break;
                case "company-settings":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Settings')));
                    break;
                case "change-password":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Change password')));
                    break;
                case "profile":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Profile')));
                    break;
                
                // company store
                case "manage-store":
                    $option = isset($_GET['op']) ? plaintext($_GET['op']) : '';
                    if ($option == 'section-create') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Store edit') => array('page/render', 'slug' => $slug),
                                    t('site', 'Create section'),
                                ));
                    } else if ($option == 'product-section') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(
                                    t('site', 'Store edit') => array('page/render', 'slug' => $slug),
                                    t('site', 'Select section'),
                                ));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Manage store')));
                    }
                    break;

                // messages
                case "inbox":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Inbox')));
                    break;
                case "outbox":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Sent')));
                    break;
                case "recyclebox":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Deleted')));
                    break;
                case "recyclebox":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Deleted')));
                    break;
                case "spambox":
                    $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Spam')));
                    break;
                case "message-view":
                    $view = isset($_GET['mailbox']) ? plaintext($_GET['mailbox']) : '';
                    if ($view == 'inbox') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Inbox')));
                    } else if ($view == 'outbox') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Sent')));
                    } else if ($view == 'spambox') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Spam')));
                    } else if ($view == 'recyclebox') {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Deleted')));
                    }
                    break;

                //products add edit manage
                case "product-addedit-sale":
                    if (isset($_GET['id'])) {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Update product')));
                    } else {
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Create product')));
                    }
                    break;
                case "product-list-sale":
                        $this->breadcrumbs = CMap::mergeArray($this->breadcrumbs, array(t('site', 'Product list')));
                    
                    break;
            }


            $this->render(BlockRenderWidget::setRenderOutput($this), array());
        } else {
            echo '';
        }
    }

    public function getBreadcrumbs($slug = '', $link = false) {
        $breadcrumbs = array();
        $text = ucfirst($this->title);
        if ($link)
            $breadcrumbs[$text] = $this->buildLink($slug);
        else
            $breadcrumbs[] = $text;

        return $breadcrumbs;
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