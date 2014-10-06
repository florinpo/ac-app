
(function($) {
    // track selected button
    $.yiicompose.ajaxerror=0;
    $.yiicompose.recipientsIds = [];
    $.yiicompose.recipientsLabels = [];
    $.yiicompose.recipients = {};
    $.yiicompose.selectedContactIds = [];
    $.yiicompose.tagFlag = true;
    $.yiicompose.filters = {};
    $.yiicompose.afterAjax = false; // flag to exclude components on listview update
    $.yiicompose.maxNumberOfFiles = 5;
    
    
    $.yiicompose.updateCompose = function() {
        
        
        // we check if we have any notification from the user session
        if($.yiicompose.notification!=''){
            $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[  " + $.yiicompose.notificationCloseLabel + " ]</div></div>";
            $.jGrowl($.yiicompose.notification, {
                header: $.yiicompose.notificationHeader,
                theme: "cLight success",
                life: 5000,
                sticky: false,
                closeTemplate: ""
            });
        }
        
        
        // trigger upload form
        $("#selectfile").click(function(){
            $("#XUploadForm_uploadimg").trigger("click");
        });
        
        
        $("#reset-form").click(function(){
            $("#message-form textarea, #message-form input[type=text]").val("");
            if($().tagit){
                $("#contacts-lbs").tagit("removeAll");
            }
            
            $(".character-counter-main-wrapper .counterT").text("2000");
            
            $("#message-form textarea, #message-form input[type=text]").blur();
        });
        
        // xupload
        $('#message-form').bind('fileuploadadded', function (e, data) {
            $(".compose-upload-block").parent(".row").removeClass("hidden");
            $(".t-right").tooltipster('hide');
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
            
            $("#reset-form").bind("click", function(){
                removeNode();
                that._adjustMaxNumberOfFiles(1);
            });
            
            $.each(data.files, function (index, file) {
                if(file.error && filesCount > $.yiicompose.maxNumberOfFiles && errorsCount > 1){
                    removeNode();
                    that._adjustMaxNumberOfFiles(1);
                }
            });
        })
        .bind('fileuploadadded', function (e, data) {
             $('.t-error').tooltipster({
                trigger: 'hover',
                theme: '.tooltipster-red'
            });
        })
        .bind('fileuploaddone', function (e, data) {
            
            var that = $('#message-form').data("fileupload");
            $("#reset-form").bind("click", function(){
                $.each(data.result, function (index, file) {
                    that._trigger("destroy", e, {
                        context: data.context,
                        url: file.delete_url,
                        type: file.delete_type || 'DELETE',
                        dataType: data.dataType
                    });
                });
            });
            
        })
        .bind('fileuploadfailed', function (e, data) {
            
            var filesCount = $("ul.files li").length;
            if(filesCount==0){
                $(".compose-upload-block").parent(".row").addClass("hidden");
            }
        })
        .bind('fileuploaddestroyed', function (e, data) {
            var filesCount = $("ul.files li").length;
            if(filesCount==0){
                $(".compose-upload-block").parent(".row").addClass("hidden");
            }
        });
        
        
        // we hide the confirm btn by default in dialog
        $(document.body).on("dialogopen", "#select-contacts", function (event, ui) {
            if($.yiicompose.selectedContactIds.length == 0){
                $('.ui-dialog-buttonpane').find('#btn-confirm').css('visibility','hidden');
            }
        });
        

        // checkbox bootstrap
        $('input[type="checkbox"]').checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-check2',
            uncheckedClass: 'icon-check2-empty',
            constructorCallback: null,
            defaultState: true,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });
        
        if($.yiicompose.getContactItems().length>8){
            // we apply the scroll to the #clist-panel
            $('ul.contacts-items').slimScroll({
                height: '320px',
                wheelStep: 10,
                distance: '0px',
                railColor: '#CCC',
                railOpacity: 1,
                railVisible: true,
                alwaysVisible: true
            });
        }
        if($.yiicompose.getSelectedContactItems().length > 8) {
            $('ul#contacts-selected').slimScroll({
                height: '320px',
                wheelStep: 10,
                distance: '0px',
                railColor: '#CCC',
                railOpacity: 1,
                railVisible: true,
                alwaysVisible: true
            });
        }
        
        $('.slimScrollBar').css({
            'border-radius':0
        });
        $('.slimScrollRail').css({
            'border-radius':0
        });
        
        if($.yiicompose.recipientsIdsSes){
            $.yiicompose.recipients = jQuery.parseJSON($.yiicompose.recipientsSes);
            $.yiicompose.recipientsLabelsSes = jQuery.parseJSON($.yiicompose.recipientsLabelsSes);
            $.yiicompose.recipientsIds = jQuery.parseJSON($.yiicompose.recipientsIdsSes);
            
            $.yiicompose.tagFlag = false;
            
            $("#Contacts_labels").val($.yiicompose.recipientsLabelsSes.join());
            $(".usr-labels").empty();
            $(".usr-labels").text($.yiicompose.recipientsLabelsSes.join(", "));
            console.log($("#MessageForm_to").val());
        }
        
        
        
        $.yiicompose.getTags();
        
        if($.yiicompose.afterAjax == false) {
            $(".maibox-txt").characterCounter({
                characterCounterNeeded: false,
                maximumCharacters: 2000,
                minimumCharacters: 0,
                shortFormat: true,
                charactersLabel: " caratteri rimanenti",
                shortFormatSeparator: " / ",
                positionBefore: false,
                chopText: true
            });
        
            $(".maibox-txt").autosize();
        }
        
        
        $(document).on("click", ".usr-labels", function() {
            var id = $(this).attr('id').match(/\d+/);
            
            $(".usr-labels").addClass("hidden");
            $(".c-tags").removeClass("hidden");
            $("#contacts-lbs").data('uiTagit').tagInput.focus();
        });
        
        
        $(document).click(function(e) {
            if ( !$(e.target).hasClass('usr-labels') && !$(e.target).hasClass('c-tags') 
                && !$(e.target).hasClass('tagit-new') && !$(e.target).hasClass('ui-autocomplete-input')) {
                
                $(".usr-labels").empty();
                $(".usr-labels").text($.yiicompose.recipientsLabels.join(", "));
                
                $(".usr-labels").removeClass("hidden");
                $(".c-tags").addClass("hidden");
                
            }
        });
        
        $('input#check-all').on("change", function () {
            $('ul.contacts-items').find('input[type=checkbox]').prop('checked', this.checked);
            var item = $("ul.contacts-items > li");
            var selectedContacts = $.yiicompose.getSelectedContactIds();
            $.yiicompose.selectedContactIds = $.yiicompose.getSelectedContactIds();
            
             
            if($(this).is(':checked')){
                $('.ui-dialog-buttonpane').find('#btn-confirm').css('visibility','visible');
                item.addClass('selected');
                item.find(':checkbox').checkbox({
                    checked: true
                });
                $('.selected-view-block .default-view').addClass('hidden');
                $('.selected-view-block .selected-view').removeClass('hidden');
                $('.selected-view span.counter').text(selectedContacts.length + ' ');
                $('ul#contacts-selected li').each(function(i){
                    $(this).remove();   
                });
                
                $('ul.contacts-items input:checked').each(function(i, v){
                    var label = $(this).closest('li').find('.cUser').text();
                    var value = $(this).val();
                        
                    var li = $('<li id =cid-' + value + '><span class="icon icon-remove-sign"></span>' + label + '</li>');
                    $('ul#contacts-selected').append(li);
                });
                
            } else {
                $('.ui-dialog-buttonpane').find('#btn-confirm').css('visibility','hidden');
                item.removeClass('selected');
                item.find(':checkbox').checkbox({
                    checked: false
                });
                
                $('.selected-view-block .default-view').removeClass('hidden');
                $('.selected-view-block .selected-view').addClass('hidden');
                
                $('ul#contacts-selected li').each(function(i){
                    $(this).remove();   
                })
            }
            console.log($.yiicompose.selectedContactIds);
            
        });
        
        
        $('ul.contacts-items input[type=checkbox]').on('change',function(e) {
            var $elem = $(this);
            var item = $elem.closest('li');
            var val = $elem.val();
            var label = item.text();
            var selectedContacts = $.yiicompose.getSelectedContactIds();
            var rowsCount = $('ul.contacts-items li').length;
            
            // if all checkbox selected then we add "checked" to the .checkAll
            if(selectedContacts.length < rowsCount){
                $('.ui-dialog-buttonpane').find('#btn-confirm').css('visibility','hidden');
                $('input#check-all').checkbox({
                    checked: false
                });
            } else {
                $('input#check-all').checkbox({
                    checked: true
                });
            }
            
            if(selectedContacts.length > 0) {
                $('.ui-dialog-buttonpane').find('#btn-confirm').css('visibility','visible');
                $('.selected-view-block .default-view').addClass('hidden');
                $('.selected-view-block .selected-view').removeClass('hidden');
                $('.selected-view span.counter').text(selectedContacts.length + ' ');
                
            } else {
                $('.selected-view-block .default-view').removeClass('hidden');
                $('.selected-view-block .selected-view').addClass('hidden');
            }
            
            //we apply the selected class for the list item
            if($elem.is(":checked")){
                item.addClass("selected");
                var li = $('<li id =cid-' + val + '><span class="icon icon-remove-sign"></span>' + label + '</li>');
                $('ul#contacts-selected').append(li);
                if($.inArray(val, $.yiicompose.selectedContactIds)==-1){
                    $.yiicompose.selectedContactIds.push(val);
                } 
                
            } else {
                item.removeClass('selected');
                $('ul#contacts-selected li#cid-'+val).remove();
                $.yiicompose.selectedContactIds = jQuery.grep($.yiicompose.selectedContactIds, function(value) {
                    return value != val;
                });
            }
            
            return false;
        });
        
        // we add  hover class for contacts-items items
        $("ul.contacts-items li").hover(
            function () {
                $(this).addClass("hover");
            },
            function () {
                $(this).removeClass("hover");
            }
            );
            
        $("ul.contacts-items li").click(function(e) {
            //$(this).bind("click");
            if (e.target.type !== 'checkbox' && !$(e.target).hasClass('btn-checkbox')) {
                $(':checkbox', this).trigger('click');
            } 
        });

        $("ul#contacts-selected").delegate("li", "click", function(){
            var id =  $(this).attr('id').match(/\d+/);
            //console.log(id);
            var cInput = $("#contact-list #c_"+id);
            var item = cInput.closest('li');
            item.trigger("click");
        //console.log($.yiicompose.selectedContactIds);
        });
       
        // delete all link
        $("#delete-selected").click(function(){
            $("ul#contacts-selected li").each(function(){
                var id =  $(this).attr('id').match(/\d+/);
                var cInput = $("#contact-list #c_"+id);
                var item = cInput.closest('li');
                item.trigger("click");
            });
        });
        
        
        /** Filter links
         *
         */
        
        $('.filter-link').click(function(e){
            
            $.yiicompose.afterAjax = true;
            
            var id = $(this).attr('id');
            
            if($(this).hasClass('checked')){
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
            
            //console.log($.yiicompose.filters);
            
            if(id == "name-desc" ){
                if($.yiicompose.filters.name == null || $.yiicompose.filters.name == "asc") {
                    $.yiicompose.filters.name = "desc";
                } else {
                    $.yiicompose.filters.name = "asc";
                   
                }
            }
            
            if(id == "has-shop" ){
                if($.yiicompose.filters.has_shop == null || $.yiicompose.filters.has_shop == "0") {
                    $.yiicompose.filters.has_shop = "1";
                
                } else {
                    $.yiicompose.filters.has_shop = "0";
                    $.yiicompose.filters.premium = "0";
                }
            }
            
            if(id == "premium") {
                if($.yiicompose.filters.premium == null || $.yiicompose.filters.premium == "0") {
                    $.yiicompose.filters.premium = "1";
                } else {
                    $.yiicompose.filters.premium = "0";
                }
            }
            
            
            $.fn.yiiListView.update("contact-list", {
                data: jQuery.param($.yiicompose.filters),
                complete: function(){
                    
                    if($.yiicompose.filters.name == 'desc') {
                        $("#name-desc").addClass("checked");
                    } 
                    
                    if($.yiicompose.filters.has_shop == 1) {
                        $("#has-shop").addClass("checked");
                        $("#premium").closest("li").removeClass("hidden");
                    }
                    
                    if($.yiicompose.filters.premium == 1) {
                        $("#premium").addClass("checked");
                    }
                    
                    //console.log($.yiicompose.selectedContactIds);
                    $.each($.yiicompose.selectedContactIds, function(i, val){
                        var cInput = $("#contact-list #c_" + val);
                        var item = cInput.closest('li');
                        item.addClass('selected');
                        cInput.prop('checked', true);
                        cInput.checkbox({
                            checked: true
                        });
                    });
                    
                }
            });
            
            return false;
            
        });
    }
    
    $.yiicompose.getTags = function() {
        
        var recipients = $.yiicompose.recipients;
        
        var ids = [];
        var filtered = [];
        var items = [];
        var hasData = false;
    
        $("#contacts-lbs").tagit({
            singleField: true,
            singleFieldNode: $("#Contacts_labels"),
            allowSpaces: true,
            animate: false,
            minLength: 2,
            removeConfirmation: true,
            
            tagSource: function( request, response ) {
                $.yiicompose.tagFlag = true;
                $.ajax({
                    url:$.yiicompose.contactListUrl,
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
            },
            beforeTagAdded: function(event, ui) {
                var availableTags = [];
                
                if($.yiicompose.tagFlag == true) {
            
                    $.each(filtered, function(i, v){
                        if($.inArray(filtered[i].id, $.yiicompose.recipientsIds)==-1){
                            availableTags.push(filtered[i].label);
                        } else {
                            $(".tagit-new input").val("");
                            return false;
                        }
                    });
                    
                    //console.log($.yiicompose.recipientsIds);

                    if($.inArray(ui.tagLabel, availableTags)==-1 && hasData==true){
                        $(".tagit-new input").val("");
                        return false;
                    }
                }
            
            },
            afterTagAdded: function(event, ui) {
        
                $.each(filtered, function(i, v) {
                    if(ui.tagLabel==filtered[i].label){
                        items.push(filtered[i]);
                    }
                });

                if(items.length>0){
                    $.each(items, function(i, v) {
                        if($.inArray(items[i].id, $.yiicompose.recipientsIds)==-1){
                            $.yiicompose.recipientsIds.push(items[i].id);
                        }
                    });
                } else  {
                    
                    $.each($.yiicompose.recipients, function(key, element) {
                        if(element==ui.tagLabel && $.yiicompose.tagFlag == true){
                            $.yiicompose.recipientsIds.push(key);
                        } 
                    });
                    
                }
                
                $.yiicompose.recipientsLabels.push(ui.tagLabel);
                
                $("#MessageForm_to").val($.yiicompose.recipientsIds.join());
          
            },
        
            afterTagRemoved: function(event, ui) {
               
                if(items.length>0) {
                    $.each(items, function(i, v) {
                        if(ui.tagLabel == items[i].label ){
                            $.yiicompose.recipientsIds = jQuery.grep($.yiicompose.recipientsIds, function(value) {
                                return value != items[i].id;
                            }); 
                        }

                    });
                
                    items = jQuery.grep(items, function(value) {
                        return value.label != ui.tagLabel;
                    });

                } 
                    
                $.each($.yiicompose.recipients, function(key, element) {
                    //console.log(element);
                    if(element==ui.tagLabel){
                        $.yiicompose.recipientsIds = jQuery.grep($.yiicompose.recipientsIds, function(value) {
                            return value != key;
                        });
                    } 
                });
                
                $.yiicompose.recipientsLabels = jQuery.grep($.yiicompose.recipientsLabels, function(value) {
                    return value != ui.tagLabel;
                });
                
                $("#MessageForm_to").val($.yiicompose.recipientsIds.join());
                //console.log($.yiicompose.recipients);
                //console.log($("#MessageForm_to").val());
            }
        });
        
    }
    
    /**
     * Return an array of selected checkbox items
     */
    $.yiicompose.getSelectedContactIds = function()
    {
        return $('ul.contacts-items li input:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    /**
     * Return the array of items of the objects li
     */
    $.yiicompose.getContactItems = function()
    {
        return $('ul.contacts-items li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
    
    /**
     * Return the array of items of the objects li with checkbox child selected
     */
    $.yiicompose.getSelectedContactItems = function()
    {
        var selected = [];
        $('ul.contacts-items li input:checked').each(function(i,n) {
            selected.push($(n).closest('li'));
        }); 
        return selected;
    }
    
    /*
     * this function will append the tags from dialog to the input
     */
     
    $.yiicompose.appendTags = function(){
        
        $.yiicompose.tagFlag = false;
                    
        $("#contact-list ul li input:checked").each(function(i,n) {
            var label = $.trim($(n).closest("li").find(".cUser").text());
            var id = $(n).val();
                        
                        
            if($.inArray(id, $.yiicompose.recipientsIds)==-1){

                $.yiicompose.recipientsIds.push(id);
                $.yiicompose.recipients[id]=label;
                $("#contacts-lbs").tagit("createTag", label);
            }
            //console.log($.yiicompose.recipientsIds);
                        
        });
                    
        $(".usr-labels").empty();
        $(".usr-labels").text($.yiicompose.recipientsLabels.join(", "));
    }
    
    $.yiicompose.updateView = function(type, header, text) {
        
        $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[" + $.yiicompose.notificationCloseLabel + "]</div></div>";
        $.jGrowl(text, {
            header: header,
            theme: "cLight "+type,
            life: 5000,
            sticky: false,
            closeTemplate: ""
        });
    }
    
    $.yiicompose.init = function(){
        $.yiicompose.updateCompose();
    }
    

})(jQuery); // jQuery
$.yiicompose.init();
