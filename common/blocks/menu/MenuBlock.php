<?php

/**
 * Class for render Top Menu
 * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.front_blocks.search
 */
class MenuBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'menu';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    public $menu_id;

    public function setParams($params) {
        $this->menu_id = isset($params['menu_id']) ? $params['menu_id'] : null;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
           $params=b64_unserialize($this->block['params']);
            $this->setParams($params);
            $menu_items = self::getMenuItems($this->menu_id);
            $this->render(BlockRenderWidget::setRenderOutput($this), array('menus' => $menu_items));
        } else {
            echo '';
        }
    }

    public function validate() {
        if ($this->menu_id == "") {
            $this->errors['menu_id'] = Yii::t('AdminMenu', 'Please select a Menu');
            return false;
        }
        else
            return true;
    }

    public function params() {
        return array(
            'menu_id' => t('AdminMenu', 'Menu'),
        );
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

    public static function getMenuItems($menu_id) {
        $menu_items = MenuItems::model()->findAll(
                array(
                    'condition' => 'level > 1 AND menu_id=:mid',
                    'params' => array(':mid' => $menu_id),
                    'order' => 'root, lft')
        );

        $result = array();
        foreach ($menu_items as $menu_item) {
            $result[] = array('name' => $menu_item->name, 'level' => $menu_item->level, 'link' => self::buildLink($menu_item), 'id' => $menu_item->id);
        }
        return $result;
    }

    public static function findMenu() {
        $result = array();
        $menus = Menu::model()->findAll();
        if ($menus) {
            foreach ($menus as $menu) {
                $result[$menu->menu_id] = $menu->menu_name;
            }
        }

        return $result;
    }

    public static function buildLink($item) {
        switch ($item->type) {
            case ConstantDefine::MENU_TYPE_URL:
                return $item->value;
                break;
            case ConstantDefine::MENU_TYPE_PAGE:
                $page = Page::model()->findByPk(array($item->value));
                if ($page)
                    //return FRONT_SITE_URL . '/' . $page->slug;
                return app()->createUrl('page/render', array('slug'=>$page->slug));
                else {
                    return 'javascript:void(0);';
                }
                break;
            case ConstantDefine::MENU_TYPE_TERM:
                break;
            case ConstantDefine::MENU_TYPE_STRING:
                return $item->value;
                break;
            default :
                return $item->value;
                break;
        }
    }

}

?>