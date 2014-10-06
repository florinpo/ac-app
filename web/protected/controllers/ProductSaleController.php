<?php

class ProductSaleController extends FeController {

    /**
     * List of allowed default Actions for the productsale
     * @return type 
     */
    public function allowedActions() {
        return 'tagsautocomplete,
            getchildren,
            selectedcategories,
            upload,
            deleteselected,
            deleteimg,
            mainimg,
            slideimages,
            visiblehome,
            favoriteadd,
            favoritedelete,
            commentadd';
    }

    public function actionTagsautocomplete() {
        $q = strtolower($_GET["term"]);
        if (!$q)
            return;

        $user = User::model()->findByPk(user()->id);
        $contacts = $user->contactIds;

        $tags = ProductSaleTag::model()->findAll();

        $result = array();

        foreach ($tags as $k => $tag) {
            $label = $tag->name;
            if (strpos(strtolower($label), $q) === 0) {
                array_push($result, array("id" => $tag->id, "label" => $label));
            }

            if (count($result) > 11)
                break;
        }

        echo json_encode($result);
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

    // get children in dynamic dropdowns
    public function actionGetchildren() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            $id = $_POST['id'];
            $result = array();
            $current_parent = ProductSaleCategoryList::model()->findByPk($id);
            $categories = $current_parent->children()->findAll();

            foreach ($categories as $k => $category) {
                array_push($result, array("id" => $category->id, "name" => $category->name));
                $count++;
            }

            if ($count > 0) {
                $message = t('site', ':count items have been found.', array(':count' => $count));
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => 1,
                        'result' => $result
                    ));
                    app()->end();
                }
            } else {
                $message = t('site', 'Empty result.');
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => 1
                    ));
                    app()->end();
                }
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /* This function will handle upload images for the products */
    public function actionUpload() {
        Yii::import("cms.extensions.xupload.models.XUploadForm");
//Here we define the paths where the files will be stored temporarily

        if (!(file_exists(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp'))) {
            mkdir(IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp', 0777, true);
        }

        $path = IMAGES_FOLDER . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $publicPath = IMAGES_URL . '/' . 'tmp/';

        $sizes = ImageSize::getProductSizes();


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
                    if (Yii::app()->user->hasState('imagesProduct')) {
                        $userImages = Yii::app()->user->getState('imagesProduct');

                        foreach ($userImages as $k => $image) {
                            if ($userImages[$k]["filename"] == $_GET["file"]) {
                                echo "is true";
                                unset($userImages[$k]);
                                Yii::app()->user->setState('imagesProduct', $userImages);
                            }
                        }
                    }

                    $file = $path . $_GET["file"];
                    if (is_file($file)) {
                        unlink($file);
                    }

                    // we also check for each size of the product
                    foreach ($sizes as $size) {
                        $file = $path . $size['id'] . DIRECTORY_SEPARATOR . $_GET["file"];
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
                $filename = md5(Yii::app()->user->id . microtime() . $model->name);
                $filename .= "." . $model->file->getExtensionName();
                if ($model->validate()) {
//Move our file to our temporary dir
                    $model->file->saveAs($path . $filename);
                    chmod($path . $filename, 0777);
//here you can also generate the image versions you need 

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

                        if ($size['width'] == '120') {
                            $thumbs->smart_resize($size['width'], $size['height'])->quality($size['quality']);
                        } elseif ($curr_width < $size['width']) {
                            $thumbs->square_fixed($size['width'], $size['height'])->quality($size['quality']);
                        } else {
                            $thumbs->square()->resize($size['width'], $size['height'])->quality($size['quality']);
                        }



                        $thumbs->save($path . DIRECTORY_SEPARATOR . $size['id'] . DIRECTORY_SEPARATOR . $filename);
                    }

                    // Now we need to save this path to the user's session
                    if (Yii::app()->user->hasState('imagesProduct')) {
                        $userImages = Yii::app()->user->getState('imagesProduct');
                    } else {
                        $userImages = array();
                    }
                    $userImages[] = array(
                        "path" => $path . $filename,
                        "120" => $path . 'img120' . DIRECTORY_SEPARATOR . $filename,
                        "200" => $path . 'img200' . DIRECTORY_SEPARATOR . $filename,
                        "500" => $path . 'img500' . DIRECTORY_SEPARATOR . $filename,
                        "filename" => $filename,
                        'size' => $model->size,
                        'mime' => $model->mime_type,
                        'name' => $model->name,
                        'extension' => $model->file->getExtensionName(),
                    );
                    Yii::app()->user->setState('imagesProduct', $userImages);

//Now we need to tell our widget that the upload was succesfull
//We do so, using the json structure defined in
// https://github.com/blueimp/jQuery-File-Upload/wiki/Setup
                    echo json_encode(array(array(
                            "name" => $model->name,
                            "type" => $model->mime_type,
                            "size" => $model->size,
                            "url" => $publicPath . $filename,
                            "thumbnail_url" => $publicPath . "img120/" . $filename,
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

   /* This function will delete the selcted products */
    public function actionDeleteselected() {
        if (Yii::app()->request->isPostRequest) {
            $count = 0;
            foreach ($_POST['ids'] as $pid) {
                if (!is_int($pid = (int) $pid))
                    continue;
                $product = ProductSale::model()->findByPk($pid);

                if (!$product->belongsTo(user()->id))
                    continue;
                if ($product->delete())
                    $count++;
            }
            if ($count) {
                if ($count > 1) {
                    $message = t('site', ':count products have been deleted.', array(':count' => $count));
                } else {
                    $message = t('site', 'The product has been deleted.');
                }
                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'success' => $message,
                        'header' => t('site', 'Atenzione!'),
                        'redirect' => 0,
                    ));
                    Yii::app()->end();
                }
            } else {
                $message = t('site', 'Error while trying to mark the conversation(s) as unread.');

                if (isset($_GET['ajax'])) {
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                }
            }
        } else
            throw new CHttpException(400, t('site', 'Invalid request. Please do not repeat this request again.'));
    }

    /*** This function will delete the image ***/
    public function actionDeleteimg() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['imgId'];
            if (isset($id)) {
                $image = ProductSaleImage::model()->findByPk($id);
                if ($image->delete()) {
                    echo json_encode(array('success' => 1, 'id' => $id, 'type' => 'delete'));
                    Yii::app()->end();
                } else {
                    echo "0";
                    Yii::app()->end();
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
   /* This function will set the image as principal for listing */
    public function actionMainimg() {
        if (Yii::app()->request->isPostRequest) {
            $id = (int) $_POST['imgId'];
            $productId = (int) $_POST['prodId'];
            $product = ProductSale::model()->findByPk($productId);
            if ($product && isset($id)) {
                ProductSale::model()->updateByPk($productId, array('main_image' => $id, 'update_time' => time()));

                echo json_encode(array('success' => 1, 'id' => $id, 'type' => 'mainimg'));
                Yii::app()->end();
            } else {
                echo "0";
                Yii::app()->end();
            }
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /* This function will make the product visible on main page of the store
     * */
    public function actionVisibleHome() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['pid']) ? (int) $_POST['pid'] : 0;
            $value = $_POST['val'];
            $shop = $_POST['shop'];
            $selected = ProductSale::model()->findAll(array(
                'condition' => 'shopId=:shopId AND visible_home=1 AND status=1',
                'params' => array(':shopId' => $shop)
                    ));
            if ($id != 0) {
                if ($value == 1 && count($selected) == ProductSale::MAX_SELECTED) {
                    $message = t('site', 'Attenzione! hai raggiunto il numero massimo di offerte visible per la prima pagina.');
                    echo json_encode(array(
                        'error' => $message,
                        'header' => t('site', 'Errore!')
                    ));
                    Yii::app()->end();
                } else {
                    ProductSale::model()->updateByPk($id, array('visible_home' => $value));
                    echo json_encode(array('success' => 1, 'id' => $id, 'count' => count($selected)));
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

    public function actionCommentadd() {
        if (Yii::app()->request->isPostRequest) {
            $comment = $_POST['ProductSaleCommentForm']['comment'];
            $product_id = $_POST['ProductSaleCommentForm']['product_id'];

            if (isset($comment) && isset($product_id)) {
                $commentObj = new ProductSaleComment;
                $commentObj->comment = $comment;
                $commentObj->product_id = $product_id;
                $commentObj->user_id = user()->id;
                $commentObj->save();
                echo json_encode(array('success' => 1, 'comment' => $comment));
                Yii::app()->end();
            } else {
                echo "0";
                Yii::app()->end(); //for ajax
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

}