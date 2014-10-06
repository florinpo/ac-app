<?php

class SiteController extends FeController {

    /**
     * Filter by using Modules Rights
     * 
     * @return type 
     */
    public function allowedActions() {
        return 'provincefromregion, selectedprovinces';
    }
    
    

    public function actionIndex() {
        //$slug = Yii::app()->settings->get('general', 'homepage');


        $slug = isset($_GET['slug']) ? fn_clean_input($_GET['slug']) : '';
        if ($slug == '') {
            $slug = Yii::app()->settings->get('general', 'homepage');
        }


        parent::renderPageSlug($slug);
    }

    public function actionStore() {
        $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : '';
        if ($slug == '') {
            $slug = 'store-view';
        }
        parent::renderPageSlug($slug);
    }

    public function actionLogout() {
        if (user()->hasState('imagesProduct')) {
            $user = User::model()->findByPK(user()->id);
            $user->clearImagesSession();
        }
        user()->logout();
        $this->redirect(app()->homeUrl);
    }

    /**
     * This is the action to Clear Cache
     */
    public function actionCachingClear() {

        if (isset($_POST['key'])) {
            if ($_POST['key'] == FRONTEND_CLEAR_CACHE_KEY) {
                Yii::app()->cache->flush();
                echo '1';
            }
        } else {
            echo '0';
        }
    }

    public function actionDeleteCache() {

        if (isset($_POST['key'])) {
            app()->cache->delete($_POST['key']);
            echo '1';
        } else {
            echo '0';
        }
    }

    public function actionProvincefromregion() {
        $region_id = (int) $_POST['region_id'];
        Province::getProvinceFromRegion($region_id);
    }

    public function actionSelectedprovinces() {
        $provinces = Province::model()->findAll();
        $id = (isset($_POST['prov_id'])) ? (int) $_POST['prov_id'] : null;
        foreach ($provinces as $province) {
            if ($province->id == $id) {
                echo CHtml::tag('option', array('value' => $province->id), CHtml::encode($province->name), true);
            }
        }
    }

    public function actionUploadavatar() {
        Yii::import("cms.extensions.xupload.models.XUploadForm");
        //Here we define the paths where the files will be stored temporarily

        if (!(file_exists(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp'))) {
            mkdir(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp', 0777, true);
        }

        $path = IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

        $folder = 'avatar';

        $profile = UserProfile::model()->find(array(
            'condition' => 'userId=:userId',
            'params' => array(':userId' => user()->id)));


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
                    if ($profile) {
                        $profile->avatar = '';
                        $profile->save(false);
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
            $model->file = CUploadedFile::getInstance($model, 'avatar');
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
                    $profile->avatar = $folder . '/' . $filename;
                    $profile->save(false);
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
                            "thumbnail_url" => IMAGES_URL . "/img180/avatar/" . $filename,
                            "delete_url" => app()->createUrl("site/uploadavatar", array(
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

    public function actionDeleteavatar() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['id'];

            $profile = UserProfile::model()->findByPk($id);

            if ($profile) {
                //We will delete the old avatar here
                $old_avatar_path = $profile->avatar;

                //Delete old file Sizes
                $sizes = ImageSize::getAvatarSizes();
                foreach ($sizes as $size) {
                    if (file_exists(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_avatar_path))
                        @unlink(IMAGES_FOLDER . '/' . $size['id'] . '/' . $old_avatar_path);
                }
                $profile->avatar = '';
                if ($profile->save(false)) {
                    echo "1";
                    Yii::app()->end();
                }
            } else {
                throw new CHttpException('403', 'Wrong Link!');
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionDeleteimg() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['img_id'];
            $counter = $_POST['counter'];
            if (isset($id) && isset($counter)) {
                $image = ProductSaleImage::model()->findByPk($id);
                if ($image->delete()) {
                    echo json_encode(array('success' => 1, 'counter' => $counter, 'id' => $id));
                    Yii::app()->end();
                } else {
                    echo "0";
                    Yii::app()->end();
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionReviewvote() {
        if (Yii::app()->request->isPostRequest) {
            $rid = $_POST['review_id'];
            $val = $_POST['val'];
            $counter = $_POST['counter'];
            if (isset($rid) && isset($val)) {
                $vote = new ShopReviewVote;
                $vote->review_id = $rid;
                $vote->user_id = user()->id;
                $vote->value = ($val == 'up') ? '1' : '-1';
                if ($vote->save()) {
                    $review = ShopReview::model()->findByPk($rid);
                    if ($val == 'up') {
                        $review->score++;
                    } else {
                        $review->score--;
                    }
                    $review->save(false);

                    $count = $counter + 1;
                    echo json_encode(array('success' => 1, 'rid' => $rid, 'count' => $count));
                    Yii::app()->end();
                } else {
                    echo "0";
                    Yii::app()->end();
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

}