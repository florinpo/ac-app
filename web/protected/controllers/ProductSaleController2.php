<?php

class ProductSaleController extends FeController {

    /**
     * List of allowed default Actions for the productsale
     * @return type 
     */
    public function allowedActions() {
        return 'updatecategories,
            updatesubcategories,
            selectedcategories,
            upload,
            deleteselected,
            deleteimg,
            mainimg,
            slideimages,
            visiblehome,
            favoriteadd,
            favoritedelete';
    }

    public function actionSelectedCategories() {
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

// dynamic drop downs categories
    public function actionUpdateCategories() {

        $current_parent = ProductSaleCategoryList::model()->findByPk($_POST['domain_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
    }

// dynamic drop downs subcategories
    public function actionUpdateSubcategories() {
        $current_parent = ProductSaleCategoryList::model()->findByPk($_POST['category_id']);
        $data = $current_parent->children()->findAll();
        $data = CHtml::listData($data, 'id', 'name');
        foreach ($data as $value => $name)
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
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


//                    $sizes = ImageSize::getSizes();
//
//                    foreach ($sizes as $size) {
//                        if (!(file_exists($path . $size['id']) && ($path . $size['id'] ))) {
//                            mkdir($path . $size['id'], 0777, true);
//                        }
//                        Yii::import('cms.extensions.image.Image');
//                        $thumbs = new Image($path . $filename);
//
//// we check the image dimension here
//                        $cur_size = getimagesize($path . $filename);
//                        $curr_width = $cur_size[0];
//                        $curr_height = $cur_size[1];
//
//
//                        if ($curr_width < $size['width']) {
//                            $thumbs->square_fixed($size['width'], $size['height'])->quality($size['quality']);
//                        } else {
//                            $thumbs->square()->resize($size['width'], $size['height'])->quality($size['quality']);
//                        }
//                        $thumbs->save($path . DIRECTORY_SEPARATOR . $size['id'] . DIRECTORY_SEPARATOR . $filename);
//                    }

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
                            )),
                            "delete_type" => "POST",
                        //'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken(),
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

    //delete selected products
    public function actionDeleteSelected() {
        if (Yii::app()->request->isPostRequest) {
            $ids = isset($_POST['ids']) ? $_POST['ids'] : 0;
            $action = $_POST['action'];
            if ($ids != 0) {
                foreach ($ids as $id) {
                    // we only allow deletion via POST request
                    $model = ProductSale::model()->findByPk((int) $id);
                    if ($model === null) {
                        $this->redirect(array('page/render', 'slug' => $action));
                    }

                    if ($model->companyId == user()->id) {
                        $model->delete();
                    }
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => $action));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionDeleteimg() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['img_id'];
            $counter = $_POST['counter'];
            if (isset($id) && isset($counter)) {
                $image = ProductSaleImage::model()->findByPk($id);
                if ($image->delete()) {
                    echo json_encode(array('success' => 1, 'counter' => $counter));
                    Yii::app()->end();
                } else {
                    echo "0";
                    Yii::app()->end();
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionMainimg() {
        $id = isset($_POST['img_id']) ? (int) $_POST['img_id'] : 0;
        $productId = (int) $_POST['prod_id'];

        $product = ProductSale::model()->find(array('condition' => 'id=:prodId AND companyId=:companyId', 'params' => array(':prodId' => $productId, ':companyId' => user()->id)));

        if ($product && $id != 0) {
            ProductSale::model()->updateByPk($productId, array('mainImage' => $id, 'update_time' => time()));

            echo "1";
            Yii::app()->end();
        }
    }

    public function actionVisibleHome() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['pid']) ? (int) $_POST['pid'] : 0;
            $value = $_POST['val'];
            $selected = ProductSale::model()->findAll(array(
                'condition' => 'companyId=:compId AND visible_home=1 AND status=1',
                'params' => array(':compId' => user()->id)
                    ));
            if ($id != 0) {
                if ($value == 1 && count($selected) == 4) {
                    echo "0";
                    Yii::app()->end(); //for ajax
                } else {
                    $product = ProductSale::model()->findByPk($id);
                    $product->visible_home = $value;
                    $product->save(false);
                    echo "1";
                    Yii::app()->end();
                }
            }
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionFavoriteadd() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $product = ProductSale::model()->findByPk($id);
            if ($id != 0 && !empty($product)) {
                $favProduct = new FavoriteProduct;
                $favProduct->productId = $product->id;
                $favProduct->userId = user()->id;
                $favProduct->save();
                $countFav = count($product->favusers);
                echo json_encode(array('success' => 1, 'count' => $countFav));
                Yii::app()->end();
            } else {
                echo "0";
                Yii::app()->end(); //for ajax
            }
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionFavoritedelete() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $product = ProductSale::model()->findByPk($id);
            $favProduct = FavoriteProduct::model()->find(array(
                'condition' => 'productId=:productId AND userId=:userId',
                'params' => array(':productId' => $id, ':userId' => user()->id))
            );
            if ($id != 0 && !empty($favProduct)) {
                $favProduct->delete();
                $countFav = count($product->favusers);
                echo json_encode(array('success' => 1, 'count' => $countFav));
                Yii::app()->end();
            } else {
                echo "0";
                Yii::app()->end(); //for ajax
            }
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionSlideImages() {
        $first = (int) $_POST['firstl'];
        $last = (int) $_POST['lastl'];
        $op = $_POST['op'];


        $products = Yii::app()->cache->get('last_products_sale');
        $items = array();
        $ids = array();
        foreach ($products as $k => $product) {
            $items[] = array(
                'id' => $product->id,
                'name' => str_trim($product->name, 26),
                'image' => $product->selectedImage(80)
            );
            $ids[] = $product->id;
        }

        $limit = 5;
        $first_k = array_search($first, $ids);
        $last_k = array_search($last, $ids);

        $first_l = max(0, intval($first_k) - $limit);
        $last_l = max($first_l + $limit, intval($last_k) - $limit);

        if ($op == 'next') {
            $length = $last_l - $first_l;
            $selected = array_slice($items, $last_l, $length);
        } else if ($op == 'prev') {
            $length = $last_l;
            $selected = array_slice($items, $first_l, $length);
        }

        echo json_encode($selected);
    }

}