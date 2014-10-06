<?php

/**
 * Backend Store Controller.
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package backend.controllers
 *
 */
class StoreController extends BeController {

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->menu = array(
            array('label' => t('cms','Manage stores'), 'url' => array('admin'), 'linkOptions' => array('class' => 'button')),
            array('label' => t('cms','Add store'), 'url' => array('create'), 'linkOptions' => array('class' => 'button')),
        );
    }

    public function actionIndex() {

        $this->redirect(array('admin'));
    }

    /**
     * The function that do Create new Store
     * 
     */
    public function actionCreate() {
        $this->render('store_create');
    }

    /**
     * The function that do Manage Store
     * 
     */
    public function actionAdmin() {
        $this->render('store_admin');
    }

    /**
     * The function that view Store details
     * 
     */
    public function actionView() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminStore', 'Update this store'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminStore', 'View this store'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );
        $this->render('store_view');
    }

    /**
     * The function that update Store
     * 
     */
    public function actionUpdate() {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
        $this->menu = array_merge($this->menu, array(
            array('label' => Yii::t('AdminStore', 'Update this Store'), 'url' => array('update', 'id' => $id), 'linkOptions' => array('class' => 'button')),
            array('label' => Yii::t('AdminStore', 'View this Store'), 'url' => array('view', 'id' => $id), 'linkOptions' => array('class' => 'button'))
                )
        );
        $this->render('Store_update', array('id' => $id));
    }

    /**
     * The function is to Delete Store
     * 
     */
    public function actionDelete($id) {
        GxcHelpers::deleteModel('CompanyStore', $id);
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

                    $img600 = $path . 'img600' . DIRECTORY_SEPARATOR . $_GET["file"];
                    if (is_file($img600)) {
                        unlink($img600);
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


                    $sizes = ImageSize::getStoreSizes();

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
                        "600" => $path . 'img600' . DIRECTORY_SEPARATOR . $filename,
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
                            "thumbnail_url" => $publicPath . "img80/$filename",
                            "delete_url" => $this->createUrl("upload", array(
                                "_method" => "delete",
                                "file" => $filename,
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

    // function to delete product image
    public function actionDeleteImg() {
        $deleteLogo = $_POST['deleteImg'];
        $id = $_POST['id'];
        if ($deleteLogo == 'true' && $id != null) {
            $store = CompanyStore::model()->findByPk($id);

            if ($store) {
                if ($store->image != null && $store->image != '') {
                    //We will delete the old avatar here
                    $old_avatar_path = $store->image;

                    //Delete old file Sizes
                    $sizes = ImageSize::getStoreSizes();
                    foreach ($sizes as $size) {
                        if (file_exists(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_avatar_path))
                            @unlink(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_avatar_path);
                    }
                    $store->image = '';
                    if ($store->save()) {
                        echo "1";
                        Yii::app()->end();
                    }
                }
            } else {
                throw new CHttpException('403', 'Wrong Link!');
            }
        }
    }

}