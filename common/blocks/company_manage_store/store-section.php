<?php
$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.tooltip',
    'options' => array(
        'position' => 'right',
        'trigger' => 'hover',
        'speed' => '150',
        'theme' => '.tooltipster-yellow'
    )
));
?>


<section id="manage-store">
    <div class="box_round_c grid_19 omega">
        <h1 class="dash"><?php echo t('site', 'Gestisci negozio') ?></h1>
        <?php
        $this->widget('zii.widgets.CMenu', array(
            'id' => 'tabnav',
            'items' => array(
                array(
                    'label' => t('site', 'Modifica negozio'),
                    'url' => array('page/render', 'slug' => 'manage-store', 'op' => 'info-negozio'),
                    'active' => !isset($_GET['op']) || $_GET['op'] == 'info-negozio' ? true : false
                ),
                array(
                    'label' => t('site', 'Aggiungi sezioni'),
                    'url' => array('page/render', 'slug' => 'manage-store', 'op' => 'sezioni-negozio'),
                    'active' => isset($_GET['op']) && $_GET['op'] == 'sezioni-negozio' ? true : false
                )
            )
        ));
        ?>
        <div class="tabnav-body">
            <div class="form-wrapper">
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'section-create-form',
                    'htmlOptions' => array('autocomplete' => 'off', 'class' => 'section-form'),
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'clientOptions' => array(
                        'validateOnSubmit' => true,
                        'validateOnChange' => false,
                        'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
//                        'beforeValidate' =>'js:function(form){
//                            $.fn.yiiGridView.update("section-grid");
//                            return true;
//                        }',
                        'afterValidate' => 'js:function(form,data,hasError){
                        if(!hasError){
                                $.ajax({
                                    "type":"POST",
                                    "url":"' . CHtml::normalizeUrl(array("store/addsection")) . '",
                                    "data":form.serialize(),
                                    "success":function(data){
                                        $.fn.yiiGridView.update("section-grid");
                                        $("#ProductSaleSection_name").val("");
                                    }
                                 });
                         }
                        }'
                    ),
                        ));
                ?>
                <div class="row">
                    <label for="name"><?php echo Yii::t('CompanyStore', 'Nome sezione:'); ?></label>
                    <?php echo $form->textField($section, 'name', array()); ?>
                    <?php echo CHtml::submitButton(t('site', 'Aggiungi'), array('class' => 'btn-i-s i-green')); ?>
                    <a class="tooltip" href='javascript:void(0);' title="Max 26 caratteri"></a>
                    <?php echo $form->error($section, 'name'); ?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="hr2"></div>
            <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'section-grid',
                'dataProvider' => $model,
                'cssFile' => false,
                'summaryText' => t('site', 'Displaying') . ' {start} - {end} ' . t('site', 'in') . ' {count} ' . t('site', 'results'),
                'pager' => array(
                    'cssFile' => '',
                    'header' => '',
                    'firstPageLabel' => t('site', 'primo'),
                    'lastPageLabel' => t('site', 'ultimo'),
                    'nextPageLabel' => t('site', 'successivo'),
                    'prevPageLabel' => t('site', 'precedente'),
                    'maxButtonCount' => 5,
                ),
                'pagerCssClass' => 'pagination pagination-centered nofl',
                //'enableSorting' => false,
                'template' => "{summary}{items}{pager}",
                'columns' => array(
                    array(
                        'header' => t('site', 'Nome sezione'),
                        'type' => 'raw',
                        'htmlOptions' => array('class' => 'gridLeft section-t'),
                        'value' => '"<div class=\'editable-".$data->id."\'>$data->name</div>"',
                    ),
                    array(
                        'header' => t('site', 'Ordina'),
                        'name' => 'position',
                        'htmlOptions' => array('class' => 'grid_40 order'),
                        'ajaxUrl' => array('store/order'),
                        'class' => 'cms.extensions.OrderColumn.OrderColumn',
                    ),
                    array
                        (
                        'class' => 'cms.components.CustomButtonColumn',
                        'htmlOptions' => array('class' => 'grid_180 gridCenter'),
                        'template' => '{edit}{delete}',
                        //'deleteConfirmation' => false,
                        //'deleteButtonUrl' => 'Yii::app()->createUrl("store/deletesection", array("id"=>$data->id))',
                        'buttons' => array(
                            'edit' => array(
                                'options' => array('class' => 'edit btn-s grey', 'iclass' => 'icon-edit icon-grey'),
                                'label' => t("site", "Modifica"),
                                'imageUrl' => false,
                                'click' => "function(e){
                                    e.preventDefault();
                                    $('div.editable-'+$(this).parent().parent().children(':first-child').children().attr('class').substr('9')).trigger({ 
                                        type:   'click',
                                    });
                                    //return false;
                                 }",
                            ),
                            'delete' => array(
                                'options' => array(
                                    'iclass' => 'icon-trash icon-grey',
                                    'class' => 'delete btn-s grey',
                                    'data-update-dialog-title' => Yii::t( 'app', 'Delete confirmation' ),
                                ),
                                'label' => t("site", "Cancela"),
                                'url' => 'Yii::app()->createUrl("store/deletesection", array("id"=>$data->id))',
                                'imageUrl' => false,

                                
                                'click'=>"function(){
                                    var url = $(this).attr('href');
                                    var data =  ".(Yii::app()->request->enableCsrfValidation ? "{'".Yii::app()->request->csrfTokenName."':'".Yii::app()->request->csrfToken."'}":'').";
                                    confirmationDeleteGrid('".t('site', 'Sei sicuro di voler rimuovere questa sezione?')."','section-grid', url, data, 'Conferma', '".t('site', 'Si')."');
                                   return false;
                              }
                              ",

                            ),
                        ),
                    ),
                ),
            ));
            ?>
        </div>
    </div>
</section>

<?php
$url = Yii::app()->createUrl('store/editsection');
Yii::app()->clientScript->registerScript('editString', '
    
$("div[class^=editable-]").live("click", function (e) {

    $(this).editable("' . $url . '", {
        event : "focus.editable",
            indicator : "' . t("site", "Salva...") . '",
            tooltip   : "Click to edit...",

            type      : "autogrow",
            autogrow : {
                lineHeight : 16,
                minHeight  : 32,
            },
            style   : "inherit",
            submit   : "' . t("site", "OK") . '",
            cancel   : "' . t("site", "Anulla") . '",
            name : "ProductSaleSection[name]",
            onsubmit: function(settings, td) {
                var input = $(td).find("input");
                $(this).validate({
                    rules: {
                        "ProductSaleSection[name]": {
                            required: true,
                            maxlength:26,
                            minlength:3
                        }
                    },
                    messages: {
                        "ProductSaleSection[name]": {
                            required: "' . t("site", "This field is required.") . '",
                            maxlength: "' . t("site", "Please no more than 30 characters.") . '",
                            minlength: "' . t("site", "Please at least 3 characters.") . '"
                        }
                    }
                });
                return ($(this).valid());
            },
            submitdata :  function (value,settings){
                            return {"id":$(this).attr("class").substr("9"), "YII_CSRF_TOKEN":"' . Yii::app()->getRequest()->getCsrfToken() . '"}; 
            },
            callback : function(value, settings) {
                $.fn.yiiGridView.update("section-grid");
            }
    });

    $(this).trigger("focus");
   
});

$.editable.addInputType("autogrow", {
    element : function(settings, original) {
    var textarea = $("<textarea />");
    if (settings.rows) {
        textarea.attr("rows", settings.rows);
    } else {
        textarea.height(settings.height);
    }
    if (settings.cols) {
        textarea.attr("cols", settings.cols);
    } else {
        textarea.width(settings.width);
    }
    $(this).append(textarea);
    return(textarea);
},
plugin : function(settings, original) {
    $("textarea", this).autogrow(settings.autogrow);
}
});

', CClientScript::POS_READY);
?>

<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.jeditable.mini.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.autogrow.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.validate.min.js", CClientScript::POS_HEAD);
?>