<?php

class CompanyController extends FeController {

    /**
     * List of allowd default Actions for the company user
     * @return type 
     */
    public function allowedActions() {
        return 'updatecategories,
            updatesubcategories,
            selectedcategories,
            deletelogo,
            uploadlogo';
    }

    public function actionSelectedCategories() {
        $categories = CompanyCats::model()->findAll();
        $id = (isset($_POST['id'])) ? (int) $_POST['id'] : null;
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

    // dynamic drop downs categories
    public function actionUpdateCategories() {

        $current_parent = CompanyCats::model()->findByPk($_POST['domain_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
    }

    // dynamic drop downs subcategories
    public function actionUpdateSubcategories() {
        $current_parent = CompanyCats::model()->findByPk($_POST['category_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
    }

    public function actionDeletelogo() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['id'];

            $shop = UserCompanyShop::model()->findByPk($id);

            if ($shop) {
                //We will delete the old avatar here
                $old_logo_path = $shop->logo;

                //Delete old file Sizes
                $sizes = ImageSize::getAvatarSizes();
                foreach ($sizes as $size) {
                    if (file_exists(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_logo_path))
                        @unlink(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_logo_path);
                }
               
                // we use update method because we don't want to trigger after save for logo
                $shop->logo = '';
                $shop->update(array('logo'));
                echo "1";
                Yii::app()->end();
                
            } else {
                throw new CHttpException('403', 'Wrong Link!');
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionUploadlogo() {
        Yii::import("cms.extensions.xupload.models.XUploadForm");
        //Here we define the paths where the files will be stored temporarily

        if (!(file_exists(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp'))) {
            mkdir(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp', 0777, true);
        }

        $path = IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

        $folder = 'company';

        $shop = UserCompanyShop::model()->find(array(
            'condition' => 'companyId=:companyId',
            'params' => array(':companyId' => user()->id)));


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
                    if ($shop) {
                        $shop->logo = '';
                        $shop->save(false);
                    }

                    $sizes = ImageSize::getAvatarSizes();

                    foreach ($sizes as $size) {
                        $file = $sizePath = IMAGES_FOLDER . DIRECTORY_SEPARATOR . $size['id'] . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $_GET["file"];
                        if (is_file($file)) {
                            unlink($file);
                        }
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
                $filename = gen_uuid() . '_' . md5(Yii::app()->user->id . microtime() . $model->name);
                $filename .= "." . $model->file->getExtensionName();
                if ($model->validate()) {
                    //Move our file to our temporary dir
                    $model->file->saveAs($path . $filename);
                    chmod($path . $filename, 0775);

                    // we safe the avatar path in profile table
                    $shop->logo = $folder . '/' . $filename;
                    $shop->save(false);
                    //here you can also generate the image versions you need 

                    $sizes = ImageSize::getAvatarSizes();

                    foreach ($sizes as $size) {

                        Yii::import('cms.extensions.image.Image');
                        $thumbs = new Image($path . $filename);

                        $thumbs->smart_resize($size['width'], $size['height'])->quality($size['quality']);

                        $sizePath = IMAGES_FOLDER . DIRECTORY_SEPARATOR . $size['id'] . DIRECTORY_SEPARATOR . $folder;

                        if (!(file_exists($sizePath) && ($sizePath))) {
                            mkdir($sizePath, 0775, true);
                        }

                        if (!(file_exists($sizePath . DIRECTORY_SEPARATOR . 'index.html'))) {
                            $fp = fopen($sizePath . DIRECTORY_SEPARATOR . 'index.html', 'w'); // open in write mode.
                            fclose($fp); // close the file.
                        }
                        $thumbs->save($sizePath . DIRECTORY_SEPARATOR . $filename);
                    }

                    unlink($path . DIRECTORY_SEPARATOR . $filename);


                    //Now we need to tell our widget that the upload was succesfull
                    //We do so, using the json structure defined in
                    // https://github.com/blueimp/jQuery-File-Upload/wiki/Setup
                    echo json_encode(array(array(
                            "name" => $model->name,
                            "type" => $model->mime_type,
                            "size" => $model->size,
                            //"url" => $publicPath . $filename,
                            "thumbnail_url" => IMAGES_URL . "/img180/company/" . $filename,
                            "delete_url" => app()->createUrl("company/uploadlogo", array(
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

}