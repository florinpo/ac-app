<?php

/**
 * Backend Category Select Controller.
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package backend.controllers
 *
 */
class CategorySelectController extends BeController {

    public $layout = '//layouts/blank';

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
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
        //return 'companycategory';
    }

    //select category in popup
    public function actionCompanyCategory() {
        $this->render('company_category', array());
    }
    
    public function actionSelectedCategoriesComp() {
        $categories = CompanyCats::model()->findAll();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id != 0) {
            foreach ($categories as $category) {
                if ($category->id == $id) {
                    if (count($category->children()->findAll()) == 0) {
                        $name = CompanyCats::model()->getCategoryParents($category->id, true);
                        echo CHtml::tag('option', array('value' => $category->id), CHtml::encode($name), true);
                    } else {
                        return null;
                    }
                }
            }
        }
    }
    
    //select category in popup
    public function actionProductCategory() {
        $this->render('product_category', array());
    }
    
    
    public function actionSelectedCategoriesProd() {
        $categories = ProductSaleCategoryList::model()->findAll();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id != 0) {
            foreach ($categories as $category) {
                if ($category->id == $id) {
                    if (count($category->children()->findAll()) == 0) {
                        $name = ProductSaleCategoryList::model()->getCategoryParents($category->id, true);
                        echo CHtml::tag('option', array('value' => $category->id), CHtml::encode($name), true);
                    } else {
                        return null;
                    }
                }
            }
        }
    }

}