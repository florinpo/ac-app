<?php

/**
 * Backend User Controller.
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package backend.controllers
 *
 */
class ProductSaleController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            //array('label' => Yii::t('AdminProduct', 'Manage products'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
        );
    }

    public function actionCreate() {
        
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $compId= isset($_GET['comp_id']) ? (int) ($_GET['comp_id']) : 0;
        if($id!=0 && $compId ==0){
            $compId = ProductSale::model()->findByPK($id)->companyId;
        } 
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminProduct', 'Manage products'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Manage company products'), 'url' => array('admin', 'comp_id'=>$compId), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Create product'), 'url' => array('create', 'comp_id'=>$compId), 'linkOptions' => array('class' => 'button')),
                )
        );
        
        $this->render('product_create');
    }


    public function actionIndex() {
        $this->redirect(array('admin'));
    }

    public function actionAdmin() {
        $this->render('product_admin');
    }

    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        if($id!=0){
            $shopId = ProductSale::model()->findByPK($id)->shopId;
        }
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminProduct', 'Manage products'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Manage company products'), 'url' => array('admin', 'comp_id'=>$shopId), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Product view'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Product Update'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
                )
        );

        $this->render('product_view');
    }


    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        if($id!=0){
            $compId = ProductSale::model()->findByPK($id)->companyId;
        }
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminProduct', 'Manage products'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Manage company products'), 'url' => array('admin', 'comp_id'=>$compId), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Product view'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminProduct', 'Product Update'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
                )
        );
        $this->render('product_update');
    }

    public function actionDelete($id) {
        GxcHelpers::deleteModel('ProductSale', $id);
    }

    /* Manage companies categories
      // Company categories
     */

    public function actionManageCategories() {

        $this->menu = array(
            array('label' => Yii::t('AdminUser', 'Manage categories'), 'url' => array('managecategories'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Create category'), 'url' => array('createcategory'), 'linkOptions' => array('class' => 'button')),
        );
        $this->render('category_manage');
    }

    public function actionCreateCategory() {

        $this->menu = array(
            array('label' => Yii::t('AdminUser', 'Manage categories'), 'url' => array('managecategories'), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Create category'), 'url' => array('createcategory'), 'linkOptions' => array('class' => 'button')),
        );
        $this->render('category_create');
    }

    public function actionUpdateCategory() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array(
            array('label' => Yii::t('AdminUser', 'Manage categories'), 'url' => array('managecategories', 'user_type' => 1), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Create category'), 'url' => array('createcategory'), 'linkOptions' => array('class' => 'button')),
                //array('label' => Yii::t('AdminUser', 'Update category'), 'url' => array('updatecategory', 'id' => $id), 'linkOptions' => array('class' => 'button')),
        );
        $this->render('category_update');
    }

    public function actionViewCategory() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array(
            array('label' => Yii::t('AdminUser', 'Manage categories'), 'url' => array('managecategories', 'user_type' => 1), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminUser', 'Create category'), 'url' => array('createcategory'), 'linkOptions' => array('class' => 'button')),
                //array('label' => Yii::t('AdminUser', 'Update category'), 'url' => array('updatecategory', 'id' => $id), 'linkOptions' => array('class' => 'button')),
        );
        $this->render('category_update');
    }
    

    // dynamic drop downs
    public function actionUpdateCategories() {

        $current_parent = ProductSaleCategoryList::model()->findByPk($_POST['domain_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
    }

    // dynamic drop downs
    public function actionUpdateSubcategories() {
        $current_parent = ProductSaleCategoryList::model()->findByPk($_POST['category_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
    }

    public function actionDeleteCategory($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = ProductSaleCategoryList::model()->findByPk((int) $id);
            if ($model === null) {
                $this->redirect(array('managecategories'));
            }
            if ($model->hasManyRoots == false && $model->isRoot()) {
                $this->redirect(array('managecategories'));
            }
            $model->deleteNode();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('managecategories'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
    
    // delete selected categories
    public function actionDeleteSelected() {
        $ids = isset($_POST['ids']) ? $_POST['ids'] : 0;
        if ($ids != 0) {
            foreach ($ids as $id) {
                if (Yii::app()->request->isPostRequest) {
                    // we only allow deletion via POST request
                    $model = ProductSaleCategoryList::model()->findByPk((int) $id);
                    if ($model === null) {
                        $this->redirect(array('managecategories'));
                    }
                    if ($model->hasManyRoots == false && $model->isRoot()) {
                        $this->redirect(array('managecategories'));
                    }
                    $model->deleteNode();

                    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                    if (!isset($_GET['ajax']))
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('managecategories'));
                }
                else
                    throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
            }
        }
    }

    public function actionUpload() {
        Yii::import("cms.extensions.xupload.models.XUploadForm");
        //Here we define the paths where the files will be stored temporarily

        if (!(file_exists(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp'))) {
            mkdir(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp', 0777, true);
        }

        $path = IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $publicPath = IMAGES_URL . '/' . 'tmp/';


        //This is for IE which doens't handle 'Content-type: application/json' correctly
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT'])
                && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }

        //Here we check if we are deleting and uploaded file
        if (isset($_GET["_method"])) {
            if ($_GET["_method"] == "delete") {

                if ($_GET["file"][0] !== '.') {

                    // if we delete the file then we also took out from user session
                    if (Yii::app()->user->hasState('images')) {
                        $userImages = Yii::app()->user->getState('images');

                        foreach ($userImages as $k => $image) {
                            if ($userImages[$k]["filename"] == $_GET["file"]) {
                                echo "is true";
                                unset($userImages[$k]);
                                Yii::app()->user->setState('images', $userImages);
                            }
                        }
                    }

                    $file = $path . $_GET["file"];
                    if (is_file($file)) {
                        unlink($file);
                    }

                    $img80 = $path . 'img80' . DIRECTORY_SEPARATOR . $_GET["file"];
                    if (is_file($img80)) {
                        unlink($img80);
                    }

                    $img100 = $path . 'img100' . DIRECTORY_SEPARATOR . $_GET["file"];
                    if (is_file($img100)) {
                        unlink($img100);
                    }

                    $img180 = $path . 'img180' . DIRECTORY_SEPARATOR . $_GET["file"];
                    if (is_file($img180)) {
                        unlink($img180);
                    }

                    $img400 = $path . 'img400' . DIRECTORY_SEPARATOR . $_GET["file"];
                    if (is_file($img400)) {
                        unlink($img400);
                    }
                }
                echo json_encode(true);
            }
        } else {
            $model = new XUploadForm;
            $model->file = CUploadedFile::getInstance($model, 'uploadimg');
            //We check that the file was successfully uploaded
            if ($model->file !== null) {
                //Grab some data
                $model->mime_type = $model->file->getType();
                $model->size = $model->file->getSize();
                $model->name = $model->file->getName();
                //(optional) Generate a random name for our file
                $filename = md5(Yii::app()->user->id . microtime() . $model->name);
                $filename .= "." . $model->file->getExtensionName();
                if ($model->validate()) {
                    //Move our file to our temporary dir
                    $model->file->saveAs($path . $filename);
                    chmod($path . $filename, 0777);
                    //here you can also generate the image versions you need 


                    $sizes = ImageSize::getSizes();

                    foreach ($sizes as $size) {
                        if (!(file_exists($path . $size['id']) && ($path . $size['id'] ))) {
                            mkdir($path . $size['id'], 0777, true);
                        }
                        Yii::import('cms.extensions.image.Image');
                        $thumbs = new Image($path . $filename);

                        // we check the image dimension here
                        $cur_size = getimagesize($path . $filename);
                        $curr_width = $cur_size[0];
                        $curr_height = $cur_size[1];


                        if ($curr_width < $size['width']) {
                            $thumbs->square_fixed($size['width'], $size['height'])->quality($size['quality']);
                        } else {
                            $thumbs->square()->resize($size['width'], $size['height'])->quality($size['quality']);
                        }
                        $thumbs->save($path . DIRECTORY_SEPARATOR . $size['id'] . DIRECTORY_SEPARATOR . $filename);
                    }

                    //using something like PHPThumb
                    //Now we need to save this path to the user's session
                    if (Yii::app()->user->hasState('images')) {
                        $userImages = Yii::app()->user->getState('images');
                    } else {
                        $userImages = array();
                    }
                    $userImages[] = array(
                        "path" => $path . $filename,
                        //the same file or a thumb version that you generated
                        "thumb" => $path . $filename,
                        "80" => $path . 'img80' . DIRECTORY_SEPARATOR . $filename,
                        "100" => $path . 'img100' . DIRECTORY_SEPARATOR . $filename,
                        "180" => $path . 'img180' . DIRECTORY_SEPARATOR . $filename,
                        "400" => $path . 'img400' . DIRECTORY_SEPARATOR . $filename,
                        "filename" => $filename,
                        'size' => $model->size,
                        'mime' => $model->mime_type,
                        'name' => $model->name,
                        'extension' => $model->file->getExtensionName(),
                    );
                    Yii::app()->user->setState('images', $userImages);

                    //Now we need to tell our widget that the upload was succesfull
                    //We do so, using the json structure defined in
                    // https://github.com/blueimp/jQuery-File-Upload/wiki/Setup
                    echo json_encode(array(array(
                            "name" => $model->name,
                            "type" => $model->mime_type,
                            "size" => $model->size,
                            "url" => $publicPath . $filename,
                            "thumbnail_url" => $publicPath . "img80/" . $filename,
                            "delete_url" => app()->createUrl("productsale/upload", array(
                                "_method" => "delete",
                                "file" => $filename,
                                    //'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken(),
                            )),
                            "delete_type" => "POST",
                            )));
                } else {
                    //If the upload failed for some reason we log some data and let the widget know
                    echo json_encode(array(
                        array("error" => $model->getErrors('file'),
                            )));
                    Yii::log("XUploadAction: " . CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR, "cms.extensions.xupload.actions.XUploadAction"
                    );
                }
            } else {
                throw new CHttpException(500, "Could not upload file");
            }
        }
    }
    
    
     public function actionDeleteImg() {
        $id = isset($_POST['img_id']) ? (int) $_POST['img_id'] : 0;
        if($id != 0){
            $image = ProductSaleImage::model()->findByPk($id);
            if($image->delete()){
                echo "1";
                Yii::app()->end();
            } 
        }

    }
    
    public function actionMainImg() {
        $id = isset($_POST['img_id']) ? (int) $_POST['img_id'] : 0;
        $productId = (int) $_POST['prod_id'];

        $product = Product::model()->find(array('condition' => 'id=:prodId', 'params' => array(':prodId' => $productId)));

        if ($product && $id != 0) {
            Product::model()->updateByPk($productId, array('main_image' => $id, 'update_time' => time()));

            echo "1";
            Yii::app()->end();
        }
    }

}