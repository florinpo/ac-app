<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<li id="r-<?php echo $data->id; ?>" class="row clearfix <?php echo ($index % 2 == 0) ? "first" : "last"; ?>">
    <div class="thumbnail">
        <?php
        $user = $data->user;
        $image = ($user->user_type == 0) ? $user->profile->selectedImage(100) : $user->cshop->selectedImage(100);
        echo Chtml::link($image, array(
            'site/store',
            'username' => $data->shop->company->username,
        ));
        ?>
    </div>
    <div class="data-wrap">
        <div class="author">
            <?php
            echo Chtml::link($user->full_name, array(
                'site/store',
                'username' => $data->shop->company->username,
            ));
            ?>
            <span><?php echo t('site', 'ha scritto il') ?></span>
            <span><?php echo simple_date($data->create_time); ?></span>
        </div>
        <div class="content">
            <?php echo $data->comment; ?>
        </div>
        <div class="vote-wrap">
            <label for="rate"><?php echo t('site', 'ha votato:'); ?></label>
            <div class="vote-input clearfix">
                <?php
                $this->widget('CStarRating', array(
                    'name'=>'rating'.$data->rating->id, // an unique name
                    'attribute' => 'rate',
                    'model' => $data->rating,
                    'allowEmpty' => false,
                    'readOnly' => true,
                    'cssFile' => $layout_asset . "/css/jquery.rating.css",
                    'minRating' => 1,
                    'maxRating' => 5,
                    'ratingStepSize' => 1,
                    'htmlOptions' => array('class'=>'rSmall')
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="bottom-bar">
            <span class="label"><?php echo t('site', 'E stato utile?'); ?></span>

            <?php
            $votesUp = ShopReviewVote::model()->findAll(array(
                'condition' => 'review_id=:reviewId AND value=1',
                'params' => array(':reviewId' => $data->id))
            );
             $class = count($votesUp)>0 ? 'active' :'hidden';
            ?>
                <span class="r-up response <?php echo $class; ?>">
                    <?php
                    if (count($votesUp) > 1) {
                        echo t('site', ':count hanno detto SI', array(':count' => count($votesUp)));
                    } else {
                        echo t('site', '1 ha detto SI');
                    }
                    ?>
                </span>
           
              
             <?php
                $votesDown = ShopReviewVote::model()->findAll(array(
                    'condition' => 'review_id=:reviewId AND value=-1',
                    'params' => array(':reviewId' => $data->id))
                );
                
                $class= count($votesDown) > 0 ? 'active' :'hidden';
                $classSeparator = count($votesUp) > 0 && count($votesDown) > 0 ? 'active':'hidden';
              ?>
            <span class="separator <?php echo $classSeparator; ?>">|</span>
            <span class="r-down response <?php echo $class; ?>">
                <?php
                    if (count($votesDown) > 1) {
                        echo t('site', ':count hanno detto NO', array(':count' => count($votesDown)));
                    } else {
                        echo t('site', '1 ha detto NO');
                    }
                ?>
            </span>
           
            <?php
            $reviewVotes = ShopReviewVote::model()->findAll(array('condition'=>'review_id=:reviewId','params'=>array(':reviewId'=>$data->id)));
            $votedUsers = array();
             foreach($reviewVotes as $vote){
                    $votedUsers[] = $vote->user_id;
             }
             $class = (!user()->isGuest && !in_array(user()->id, $votedUsers)) ? 'active' : 'hidden';
            ?>
            <div id="vote-no" class="vote-btns <?php echo $class; ?>">
                <?php
                echo CHtml::ajaxLink(
                        '<span class="icon"></span>
                             <span class="inner">
                             <span class="text">' .
                        t('site', 'Si') .
                        '</span></span>', app()->createUrl('site/reviewvote'), array(
                    'type' => 'POST',
                    'dataType' => 'json',
                    'cache' => false,
                    'data' => array(
                        'review_id' =>$data->id,
                        'val' => 'up',
                        'counter' =>count($votesUp),
                        'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                        ),
                    'success' => "function(data) {
                                    if(data.success==1){
                                       $('li#r-'+ data.rid +' .r-up').removeClass('hidden').addClass('active');
                                       if($('li#r-'+ data.rid +' .r-down').hasClass('active')){
                                         $('li#r-'+ data.rid +' .bottom-bar .separator').removeClass('hidden').addClass('active');
                                       }
                                       if(data.count > 1) {
                                         $('li#r-'+ data.rid +' .r-up').html(data.count+".t('site','" hanno detto SI"').");
                                       }
                                       $('li#r-'+ data.rid +' #vote-no').removeClass('active').addClass('hidden');
                                       $('li#r-'+ data.rid +' #vote-yes').removeClass('hidden').addClass('active');
                                    }
                                }"), array(
                    'id' => 'vote_up_'.$data->id,
                    'class' => 'btn-s grey t-up',
                    'href' => 'javascript:void(0)'
                ));
                ?>
                <?php
                echo CHtml::ajaxLink(
                        '<span class="icon"></span>
                             <span class="inner">
                             <span class="text">' .
                        t('site', 'No') .
                        '</span></span>', app()->createUrl('site/reviewvote'), array(
                    'type' => 'POST',
                    'dataType' => 'json',
                    'cache' => false,
                    'data' => array(
                        'review_id' =>$data->id,
                        'val' => 'down',
                        'counter' =>count($votesDown),
                        'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                        ),
                    'success' => "function(data) {
                                    if(data.success==1){
                                       $('li#r-'+ data.rid +' .r-down').removeClass('hidden').addClass('active');
                                       if($('li#r-'+ data.rid +' .r-up').hasClass('active')){
                                         $('li#r-'+ data.rid +' .bottom-bar .separator').removeClass('hidden').addClass('active');
                                       }
                                       if(data.count > 1) {
                                         $('li#r-'+ data.rid +' .r-down').html(data.count+".t('site','" hanno detto SI"').");
                                       }
                                       $('li#r-'+ data.rid +' #vote-no').removeClass('active').addClass('hidden');
                                       $('li#r-'+ data.rid +' #vote-yes').removeClass('hidden').addClass('active');
                                    }
                                }"), array(
                    'id' => 'vote_down_'.$data->id,
                    'class' => 'btn-s grey t-down',
                    'href' => 'javascript:void(0)'
                ));
                ?>
            </div>
            
            <?php 
               $class = (!user()->isGuest && !in_array(user()->id, $votedUsers)) ? 'hidden' : 'active'; 
            ?>
            <div id="vote-yes" class="vote-btns <?php echo $class; ?>">
                <?php
                echo CHtml::link(
                        '<span class="icon"></span>
                             <span class="inner">
                             <span class="text">' .
                        t('site', 'Si') .
                        '</span></span>', 'javascript:void(0)', array(
                            'class' => 'btn-s grey t-up',
                        ));
                ?>
                <?php
                echo CHtml::link(
                        '<span class="icon"></span>
                             <span class="inner">
                             <span class="text">' .
                        t('site', 'No') .
                        '</span></span>', 'javascript:void(0)', array(
                    'class' => 'btn-s grey t-down',
                    'href' => 'javascript:void(0)'
                ));
                ?>
            </div>
            <?php
//                echo CHtml::ajaxLink(t('site', 'Segnala abuso'), app()->createUrl('site/reviewvote'), array(
//                    'type' => 'POST',
//                    'dataType' => 'json',
//                    'data' => array('id' => $data->id, 'val' => 'down', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
//                    //'update' => '#product_img',
//                    'success' => "function(data) {
//                                    if(data.success==1){
//                                         alert('true');
//                                    }
//                                }"), array(
//                    'class' => 'isSpam',
//                    'href' => 'javascript:void(0)'
//                ));
                ?>
        </div>
</li>