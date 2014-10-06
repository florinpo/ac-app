<?php

class PageController extends FeController {

    public $defaultAction = 'render';
    
    public function filters() {
        return array(
            'cacheInit +render'
        );
    }

    public function allowedActions() {
        return 'render, captcha';
    }
    
    public function actions() {
        return array(
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'maxLength' => 6,
                'minLength' => 5,
                'padding' => 8,
                'backColor' => 0xeaeaea,
                'foreColor' => 0x43663a,
                'width' => 120,
                'height' => 50,
                'testLimit' => 0 // for ajax
            ),
        );
    }

    public function actionRender() {
        $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : false;

//        switch ($slug) {
//            case "inbox":
//                if (isset($_POST['convs'])) {
//                    $this->actionMailbox('inbox');
//                }
//                break;
//        }
        
        if ($slug) {
            parent::renderPageSlug($slug);
        } else {
            throw new CHttpException('404', t('cms', 'Oops! Page not found!'));
        }
    }

}