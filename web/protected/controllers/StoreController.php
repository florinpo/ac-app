<?php

class StoreController extends FeController {

    public $defaultAction = 'index';

    /**
     * List of allowd default Actions for the user
     * @return type 
     */
    public function allowedActions() {
        return 'index, view, error,
            addsection, order, deletesection,
            editsection, productgallery, favoriteadd,
            favoritedelete, reviewadd';
    }

    public function actions() {
        return array(
            'order' => array(
                'class' => 'cms.extensions.OrderColumn.OrderAction',
                'modelClass' => 'ProductSaleSection',
                'pkName' => 'id',
            ),
        );
    }

    /**
     * Index Page of the Site, re route here
     */
    //public function actionIndex($path)
    public function actionIndex() {

        $slug = isset($_GET['username']) ? fn_clean_input($_GET['username']) : '';
        if ($slug == '') {
            $slug = Yii::app()->settings->get('general', 'homepage');
        }
        parent::renderPageSlug($slug);
    }

    /**
     * When viewing a Page
     */
    public function actionView($id) {
        parent::renderPage($id);
    }

    /**
     * This is the action to add sections for store.
     */
    public function actionAddSection() {
        $model = new ProductSaleSection;

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'section-form') {
            $model = new ProductSaleSection;
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $company = User::model()->find(array(
            'condition' => 'user_id=:userId AND has_membership=1',
            'params' => array(':userId' => user()->id))
        );
        $shop = $company->cshop;
        if (isset($_POST['ProductSaleSection']) && isset($shop)) {
            $model->name = ucfirst($_POST['ProductSaleSection']['name']);
            $model->shopId = $shop->id;
            //$model->slug= toSlug($model->name);
            if ($model->validate()) {
                $model->save();
                $model = new ProductSaleSection;
            }
        }
    }

    /**
     * This is the action to edit sections jeditable.
     */
    public function actionEditSection() {
        $id = $_POST['id'];
        if ($id) {
            $section = ProductSaleSection::model()->findByPk((int) $id);
            $section->name = ucfirst($_POST['ProductSaleSection']['name']);
            $section->save(false);
            echo $section->name;
        }
    }

    /**
     * Action to Delete section
     */
    public function actionDeleteSection($id) {
        //GxcHelpers::deleteModel('ProductSaleSection', $id);

        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            GxcHelpers::loadDetailModel('ProductSaleSection', $id)->delete();
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('page/render', 'slug' => 'manage-store', 'op' => 'section-create'));
        } else
            throw new CHttpException(400, t('Invalid request. Please do not repeat this request again.'));
    }

    /**
     * Action to navigate on product gallery
     */
    public function actionProductGallery() {
        $selectedImage = isset($_POST['selectedImage']) ? (int) $_POST['selectedImage'] : '';
        $productId = isset($_POST['productId']) ? (int) $_POST['productId'] : '0';
        $product = ProductSale::model()->findByPk($productId);
        $images = $product->pimages;
        $totalImages = count($images);
        $ids = array();
        foreach ($images as $k => $image) {
            $ids[] = $image->id;
        }
        if (!empty($selectedImage)) {
            $currentKey = array_search($selectedImage, $ids);
            $cImage = ProductSaleImage::model()->findByPk($selectedImage);
            echo json_encode(array('key' => $currentKey, 'path' => $cImage->path));
        }
    }

    /**
     * Action to add the current shop to favorites
     */
    public function actionFavoriteAdd() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $shop = UserCompanyShop::model()->findByPk($id);
            if ($id != 0 && !empty($shop)) {
                $favShop = new FavoriteShop;
                $favShop->shopId = $shop->id;
                $favShop->userId = user()->id;
                $favShop->save();
                $countFav = count($shop->favusers);
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

    /**
     * Action to delete the current shop to favorites
     */
    public function actionFavoriteDelete() {
        if (Yii::app()->request->isPostRequest) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $shop = UserCompanyShop::model()->findByPk($id);
            $favShop = FavoriteShop::model()->find(array(
                'condition' => 'shopId=:shopId AND userId=:userId',
                'params' => array(':shopId' => $id, ':userId' => user()->id))
            );
            if ($id != 0 && !empty($favShop)) {
                $favShop->delete();
                $countFav = count($shop->favusers);
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

    public function actionReviewadd() {
        if (Yii::app()->request->isPostRequest) {
            $comment = $_POST['ShopReviewForm']['comment'];
            $shop_id = $_POST['ShopReviewForm']['shop_id'];
            $vote = $_POST['ShopReviewForm']['rating'];
            if (isset($comment) && isset($shop_id)) {
                $review = new ShopReview;
                $review->comment = $comment;
                $review->shop_id = $shop_id;
                $review->user_id = user()->id;
                $review->save();

                $rating = new ShopReviewRating;
                $rating->user_id = user()->id;
                $rating->review_id = $review->id;
                $rating->rate = $vote;
                $rating->save();

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