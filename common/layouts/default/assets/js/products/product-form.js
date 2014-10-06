
(function($) {
    // track selected button
    $.productForm.ajaxerror=0;
    $.productForm.selectedIds = [];
    $.productForm.maxNumberOfFiles = 6;
    $.productForm.fcounter = 0;
    $.productForm.filesCount = $("ul.files li").length;
    
    $.productForm.updateProduct = function() {
        
        // checkbox bootstrap
        $('input.s-checkbox').checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty',
            constructorCallback: null,
            defaultState: true,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });
        
        
        /***=== Discount panel ===***/
        $("#ProductSaleForm_discount_rate, #ProductSaleForm_min_quantity").keyup(function() {
            
            if (!isNaN(parseInt(this.value,10))) {
                this.value = parseInt(this.value);
            } else {
                this.value = "";
            }
            this.value = this.value.replace(/[^0-9]/g, "");
        });
        
        // discount calculation
        $("#ProductSaleForm_discount_rate, #ProductSaleForm_price").keyup(function() {
            var fullPrice = parseFloat($("#ProductSaleForm_price").val());
            var discountRate = parseFloat($("#ProductSaleForm_discount_rate").val());
            var discountPrice = fullPrice - (fullPrice*(discountRate/100));
            
            if(discountPrice % 1 != 0){
                discountPrice = parseFloat(discountPrice).toFixed(2);
            } else {
                discountPrice = Math.round(discountPrice);
            }
            if($("#ProductSaleForm_discount_rate").val()!="" && $("#ProductSaleForm_price").val()!="" && isNumber($("#ProductSaleForm_price").val())){
                if(discountRate < 10 || discountRate > 90){
                    $("#ProductSaleForm_discount_price").val("");
                // return false;
                } else {
                    $("#ProductSaleForm_discount_price").val(discountPrice);
                }
            } else 
                $("#ProductSaleForm_discount_price").val("");
        });
        
        $("#ProductSaleForm_has_discount").on('change',function(){
            var $elem = $(this);
            if($elem.is(':checked')){
                $(".op-panel").removeClass("hidden").addClass("active");  // checked
                $("#ProductSaleForm_has_discount").find(':checkbox').checkbox({
                    checked: true
                });
                 $('#ProductSaleForm_discount_duration').chosen({
            disable_search:true
        });
        
            } else{
                $(".op-panel").removeClass("active").addClass("hidden");  // unchecked
                $("#ProductSaleForm_has_discount").find(':checkbox').checkbox({
                    checked: false
                });
            }
            return false;
        });

        //        $("#ProductSaleForm_has_discount").click(function(){
        //            $(".op-panel").toggle(this.checked);
        //        });
        
        
        /***=== Upload files  ===***/
        
        // image items actions
        $(document).on("click", ".btn-img", function(e) {
            
            var id = $(this).closest('li').attr('id').match(/\d+/)[0];
            
            if($.productForm.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            
            // build URL
            var url = $.productForm.productControllerUrl+$(this).attr('id')+'?ajax=1';
            
            return $.productForm.ajaxImgActions(url, id);
            
        });
         
        // xupload [component for uploading files]
        
        $.productForm.maxNumberOfFiles = $.productForm.maxNumberOfFiles - $.productForm.filesCount;
        
        $('#product-form').bind('fileuploadadded', function (e, data) {
            
            $.productForm.fcounter++;
            
            $(".upload-block .f-block").removeClass("hidden");
            //$(".t-right").tooltipster('hide');
            //console.log(data.context);
            var that = $(this).data("fileupload");
            var errorsCount = $(".template-upload .error").length;
            var filesCount = $("ul.files li").length;
            var removeNode = function () {
                that._transition(data.context).done(
                    function () {
                        $(this).remove();
                        that._trigger("failed", e, data);                            
                    });
            };
            
            $('.cancel > .btn').tooltipster({
                trigger: 'hover',
                content: $.productForm.cancelLabel
            });
            
            $('.t-error').tooltipster({
                trigger: 'hover',
                theme: '.tooltipster-red'
            });
            
            $.each(data.files, function (index, file) {
               
                if(file.error && filesCount > $.productForm.maxNumberOfFiles && errorsCount > 1){
                    removeNode();
                    that._adjustMaxNumberOfFiles(1);
                }
            });
        })
        .bind('fileuploadfailed', function (e, data) {
            
            var filesCount = $("ul.files li").length;
            if(filesCount==0){
                $(".upload-block .f-block").addClass("hidden");
            }
            
        })
        .bind('fileuploaddestroyed', function (e, data) {
            
            var filesCount = $("ul.files li").length;
            if(filesCount==0){
                $(".upload-block .f-block").addClass("hidden");
            }

        })
        .bind('fileuploadalways', function (e, data) {
            
            $('.delete > .btn').tooltipster({
                trigger: 'hover',
                content: $.productForm.cancelLabel
            });
             
        });
        
        
        /***=== CKEditor configuration ===***/
      
        var config = {
            removePlugins: 'elementspath,resize',
            extraPlugins: 'wordcount,autogrow',
            autoGrow_maxHeight: 260,
            autoGrow_minHeight: 100,
            wordcount: {
                // Whether or not you want to show the Word Count
                showWordCount: false,

                // Whether or not you want to show the Char Count
                showCharCount: true,

                // Whether or not to include Html chars in the Char Count
                countHTML: false,
                
                minCharLimit: 100,
    
                // Option to limit the characters in the Editor
                charLimit: 2000,
  
                // Option to limit the words in the Editor
                wordLimit: 'unlimited',
                
                // separator for space between min and max
                separator: '/'
            },
            height: 100,
            resize_minWidth: 428,
            resize_maxWidth: 428,
            width : 428,
            language: "it",
            skin: "moono-light",
            toolbar : [
            ["Bold","BulletedList"]
            //[ 'Link', 'Unlink']
            ]
        };
        
        // Set the CKEditor
        $("#ProductSaleForm_description").ckeditor(config);
        
        
        // we setup the tooltip on instance ready event
        CKEDITOR.on('instanceReady', function(evt) {
            $('.cke_button').tooltipster({
                trigger: 'hover'
            });
        });
        // close the multiselect on editor focus
        CKEDITOR.instances['ProductSaleForm_description'].on('focus', function(){
            $("#ProductSaleForm_category_id").multiselect('close');
        });
        
       
        
        
        //console.log(CKEDITOR.instances.editor1.focusManager.hasFocus);
        
        /***=== Tags block ===***/
        // tagit update
        $.productForm.getTags();
        
        
        /***=== Categories configuration ===***/
        
        // we update the global selectedIds
        if($("#ProductSaleForm_selected_cats").val() != '') {
            var values = $("#ProductSaleForm_selected_cats").val();
            $.productForm.selectedIds = values.split(',');
        }
        
        
       
       // we uptate the categories
        //$.productForm.updateCategories();
        
        $('#ProductSaleForm_section_store').chosen({
            disable_search:true
        });
        
        $('#ProductSaleForm_domain_id').chosen({
            disable_search:true
        });
        
        
         $('#ProductSaleForm_category_id').chosen({
            disable_search:true,
            max_selected_options: 3
        });
//        
        $('#ProductSaleForm_domain_id').on('change', function(event, params) {
            var url = $.productForm.getChildrenUrl+'?ajax=1';
            $('#ProductSaleForm_category_id').empty();
            $.productForm.ajaxGetCategories(url,params.selected);
        });
        
        //console.log($('#ProductSaleForm_domain_id_chosen'));
        
        
        $('#ProductSaleForm_category_id').on('change', function(event, params) {
           //console.log($('#ProductSaleForm_category_id').val());
        });
        
        //        $("#ProductSaleForm_category_id").multiselect({
        //            height:'auto',
        //            header:false,
        //            noneSelectedText: "Select category",
        //            selectedText: "# checked",
        //            classes: 'multiselect-cf',
        //            beforeopen: function(){
        //                $('.ui-multiselect-checkboxes input').checkbox({
        //                    buttonStyle: 'btn-checkbox',
        //                    checkedClass: 'icon-check',
        //                    uncheckedClass: 'icon-check-empty',
        //                    constructorCallback: null,
        //                    defaultState: true,
        //                    defaultEnabled: true,
        //                    checked: false,
        //                    enabled: true
        //                });
        //            }
        //        });
        
        //        $("#ProductSaleForm_domain_id").selectbox({
        ////           onOpen: function (inst) {
        ////               $('.sbSelector').addClass('open');
        ////	   },
        //            speed: 20,
        //            onChange: function (val, inst) {
        //                $('#ProductSaleForm_category_id').children().remove();
        //                var url = $.productForm.getChildrenUrl+'?ajax=1';
        //                $.productForm.ajaxGetCategories(url,val);
        //            }
        //        
        //        });
        
        // fix the multiselect click outside
        //        $(document).click(function (e) {
        //            var target = e.target;
        //            if (!$(target).is('.ui-multiselect-menu') && $(target).parents().index($('.ui-multiselect-menu')) == -1) {
        //                $("#ProductSaleForm_category_id").multiselect('close');
        //            }
        //
        //        });
        
        
        //        $(document).on("change", "#ProductSaleForm_domain_id", function(e) {
        //            
        //            console.log($(this).attr('id'));
        //            var $elem = $(this);
        //            var val = $elem.val();
        //            var item = $elem.closest('li');
        //            var url = $.productForm.getChildrenUrl+'?ajax=1';
        //            
        //            // we make the checkbox act as a radio button
        //            
        //            $.productForm.ajaxGetCategories(url,val);
        //            
        //            
        //            return false;
        //        });

        
        
        
        // check categories
        $(document).on("change", "input.c-category", function(e) {
            var $elem = $(this);
            var val = $elem.val();
            var item = $elem.closest('li');
            var url = $.productForm.getChildrenUrl+'?ajax=1';
            
            
            // we make the checkbox act as a radio button
            $("input.c-category").checkbox({
                checked: false
            });
            $(this).checkbox({
                checked: true
            });
            
            $("input.c-category").closest('li').removeClass('selected');
            
            if($elem.is(":checked")){
                item.addClass("selected");
                $("#ProductSaleCategoryForm_category_id").val(val);
                $.productForm.ajaxGetSubcategories(url,val);
            } 
            
            return false;
        });
        
        // check subcategories
        $(document).on("change", "input.c-subcategory", function(e) {
            var $elem = $(this);
            var val = $elem.val();
            var item = $elem.closest('li');
            
            // we make the checkbox act as a radio button
            $("input.c-subcategory").checkbox({
                checked: false
            });
            $(this).checkbox({
                checked: true
            });
            
            $("input.c-subcategory").closest('li').removeClass('selected');
            if($elem.is(":checked")){
                item.addClass("selected");
                $("#ProductSaleCategoryForm_subcategory_id").val(val);
            }
            return false;
        });
        
        // we trigger the checkbox once we click the cat-items item
        $(document).on("click", "ul.cat-items li", function(e) {
            //$(this).bind("click");
            if (e.target.type !== 'checkbox' && !$(e.target).hasClass('btn-checkbox')) {
                $(':checkbox', this).trigger('click');
            } 
        });
        
        // we add  hover class cat-items list item
        $(document).on({
            mouseenter: function() {
                $(this).addClass("hover");
            },
            mouseleave: function() {
                $(this).removeClass("hover");
            }
        }, "ul.cat-items li, ul#selected-items li, ul#p-selected-categories li, ul.files li");
        
        
        $(document).on("click", "#select-category", function(e) {
            $('.loader').show();
            $('.loading').show();
            var form = $('#product-category-form');
            var settings = form.data('settings') ;
            settings.submitting = true;
           
            $.fn.yiiactiveform.validate(form, function(messages) {
                if($.isEmptyObject(messages)) { // If there are no error messages all data are valid
                    $.each(settings.attributes, function () {
                        $.fn.yiiactiveform.updateInput(this,messages,form); 
                    });
                    
                    $.fn.yiiactiveform.updateSummary(form, messages);
                    
                    $.productForm.updateSelected();
                    $('.loader').hide();
                    $('.loading').hide();
                   
                } else {
                    // Fields acquiring invalid data classes and messages show up. Update the inputs.
                    settings = form.data('settings'),
                    $.each(settings.attributes, function () {
                        $.fn.yiiactiveform.updateInput(this,messages,form); 
                    });
                    $.fn.yiiactiveform.updateSummary(form, messages);
                    settings.submitting = false;
                    $('.loader').hide();
                    $('.loading').hide();
                }
            });

        });
        
        
        // delete the items from the selected list in dialog
        $(document).on("click", "ul#selected-items li", function(e) {
            var id = $(this).attr('id').match(/\d+/);
            
            // we delete the id from the global $.productForm.selectedIds
            $.productForm.selectedIds = jQuery.grep($.productForm.selectedIds, function(value) {
                return value != id;
            });
            
            $(this).remove();
            
            $("#ProductSaleCategoryForm_selected_categories").val($.productForm.selectedIds);
            
            if($('ul#selected-items li').length == 0){
                $('.s-wrapper .empty').show();
            }
            
        });
        
        
        // delete the items from the selected list in product form
        $(document).on("click", "ul#p-selected-categories li", function(e) {
            var id = $(this).attr('id').match(/\d+/);
            
            // we delete the id from the global $.productForm.selectedIds
            $.productForm.selectedIds = jQuery.grep($.productForm.selectedIds, function(value) {
                return value != id;
            });
            
            $("#ProductSaleForm_selected_cats").val($.productForm.selectedIds);
            $(this).remove();
            
            $('ul#p-selected-categories li:last-child').addClass('last');
            
        });
        
        // append the dialog selected categories to the product form
        $(document).on("click", "#save-selected", function(e) {
            var productCategoriesList = $("ul#p-selected-categories");
            var output = $("ul#selected-items").html();
            productCategoriesList.html(output);
            
            console.log(output);
            $("#select-categories").dialog( "close" );
            productCategoriesList.find('li:last-child').addClass('last');
             
            $("#ProductSaleForm_selected_cats").val($.productForm.selectedIds);
            
            $('.t-cancel').tooltipster({
                trigger: 'hover',
                position: 'right',
                content: $.productForm.cancelLabel
            });
             
        });
        
    }
    
    $.productForm.updateCategories = function (){
        var v =  $('#ProductSaleForm_domain_id').val();
        var url = $.productForm.getChildrenUrl+'?ajax=1';
        
        if(v.length != 0)
        $.productForm.ajaxGetCategories(url,v);
    }
    
    /**
     * Submit the ajax img actions
     */
    $.productForm.ajaxImgActions = function(url, id){
        
        var productId = getParameter('id');
        
        var data = {
            'imgId': id,
            'prodId': productId,
            'YII_CSRF_TOKEN': $.productForm.csrf
            
        };  
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    if(response.type == 'delete') {
                        // we remove the deleted images from the list files
                        $("ul.files li#i-"+response.id).remove();
                        // we also need to update the maxNumberOfFiles for xupload
                        $.productForm.maxNumberOfFiles++;
                        //console.log($.productForm.maxNumberOfFiles);
                        var that = $('#product-form').data("fileupload");
                        that._adjustMaxNumberOfFiles(1);
                    } else {
                        $("ul.files li").removeClass("selected");
                        $("ul.files li#i-"+response.id).addClass("selected");
                    }
                    
                }
                else
                    return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.productForm.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    
    // update the product tags
    $.productForm.getTags = function() {
        var ids = [];
        var filtered = [];
        var items = [];
        var hasData = false;
    
        $("#product-lbs").tagit({
            singleField: true,
            singleFieldNode: $("#ProductSaleForm_tags"),
            allowSpaces: false,
            animate: false,
            //minLength: 2,
            tagLimit: 5,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                
                $.ajax({
                    url: $.productForm.tagsUrl,
                    data: {
                        term:request.term
                    },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function(item) {
                            return {
                                label: item.label,
                                value: item.key
                            }
                        }));
                        filtered = data;
                        hasData = true;
                    }
                });
            }
        });
        
    }
    
    // return an array of items of the domains
    $.productForm.getDomainItems = function() {
        return $('ul#p-domains li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
    
    // return an array of items of the categories
    $.productForm.getCategoryItems = function() {
        return $('ul#p-categories li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
    
    // return an array of items of the subcategories
    $.productForm.getSubcategoryItems = function() {
        return $('ul#p-subcategories li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
   

    /**
 * Submit the ajax form for clicked buttons/drag-n-drop delete.
 */
    $.productForm.ajaxGetCategories = function(url, id){
        
        
        var data = {
            'id': id,
            'YII_CSRF_TOKEN': $.productForm.csrf
        };
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            beforeSend : function (){
            //$('.loader').show();
            //$('.loading').show();
            },
            success: function(response){
                if(response.success) {
                    
                    //console.log(response.result);
                    
                    $.each(response.result, function(i, val){
                        $('#ProductSaleForm_category_id').append($('<option>', { 
                            value: val.id,
                            text : val.name 
                        }));
                        
                    });
                    
                    $('#ProductSaleForm_category_id').trigger('chosen:updated');
                    
                    //$("#ProductSaleForm_category_id").multiselect('refresh');
                    
                    
                //                    $.each(response.result, function(i, val){
                //                        var output = "<li class='category-item clearfix'>";
                //                        output += "<input class='c-category s-radio' type='checkbox' name='categories[]' value='" + val.id +"' />";
                //                        output += "<span class='c-name'>"+val.name+"</span>";
                //                        output += "</li>";
                //                        //listCategories.append(output);
                //                    });
                }
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.productForm.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    
    $.productForm.ajaxGetSubcategories = function(url, id){
       
        var listSubcategories = $("#p-subcategories");
        listSubcategories.empty();
        $.productForm.getScrollbars(listSubcategories);
        $("#ProductSaleCategoryForm_subcategory_id").val('');
        
        var data = {
            'id': id,
            'YII_CSRF_TOKEN': $.productForm.csrf
        };
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            beforeSend : function (){
                $('.loader').show();
                $('.loading').show();
            },
            success: function(response){
                if(response.success) {
                    
                    $.each(response.result, function(i, val){
                        var output = "<li class='subcategory-item clearfix'>";
                        output += "<input class='c-subcategory s-radio' type='checkbox' name='subcategories[]' value='" + val.id +"' />";
                        output += "<span class='c-name'>"+val.name+"</span>";
                        output += "</li>";
                        listSubcategories.append(output);
                    });
                    
                    $('input.c-subcategory').checkbox({
                        buttonStyle: 'btn-checkbox',
                        checkedClass: 'icon-radio',
                        uncheckedClass: 'icon-radio-empty',
                        constructorCallback: null,
                        defaultState: true,
                        defaultEnabled: true,
                        checked: false,
                        enabled: true
                    });
                    
                    $.productForm.getScrollbars(listSubcategories, listSubcategories.find('li').length);
                     
                    $('.loader').hide();
                    $('.loading').hide();
                    
                } else {
                    $('.loader').hide();
                    $('.loading').hide();
                }
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.productForm.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    $.productForm.init = function(){
        $.productForm.updateProduct();
    }
    

})(jQuery); // jQuery
$.productForm.init();
