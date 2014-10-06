
(function($) {
    // track selected button
    $.yiiconversation.ajaxerror=0;
    $.yiiconversation.linkAction = '';
    
    $.yiiconversation.sendersSingleLabels = [];
    $.yiiconversation.sendersSingleIds = [];
    $.yiiconversation.jsonSingleSenders = [];
    
    $.yiiconversation.sendersLabels = [];
    $.yiiconversation.jsonSenders = [];
    $.yiiconversation.sendersIds = [];
    $.yiiconversation.tagInitialization = false;
    $.yiiconversation.counter = ''; // counter to keep the number of items (messages)
    $.yiiconversation.openFieldTag = -1;
    $.yiiconversation.tagFlag = true; // flag to keep an eye for the interaction between drop downs and autocomplete
    $.yiiconversation.maxNumberOfFiles = 5; // max number of files for upload form
    $.yiiconversation.curentItemId;
    
    $.yiiconversation.updateSender = true;
    
    
    $.yiiconversation.updateConversation = function(){
        
        $.yiiconversation.counter = $.yiiconversation.getMessages().length;
        $.yiiconversation.curentItemId = $.yiiconversation.counter -1;

        
        
        // blueimp gallery for message item
        
        $(document).on("click", ".images-list", function(event) {
            event.stopPropagation();
            var id = $(this).attr('id').match(/\d+/);
            var target = event.target || event.srcElement,
            link = target.src,
            options = {
                container: '#mailbox-gallery-'+id,
                index: target,
                event: event,
                hidePageScrollbars: false
            },
            links = $(this).find('a:not(".download")');
            blueimp.Gallery(links, options);
        });


        //download image link 
         
        $(document).on("click", "#download-file", function(e) {
           
            $.fileDownload($(this).attr('href'));
            
            return false;
        });
        
        // show hidden items valid when we have more than 10 message items
        $(document).on("click", "#show-hidden", function(e) {
            $('.items-expand').addClass('hidden');
            $('.message-item').removeClass('hidden');
        });
        
        // toogle button minimize / expand all
        $(document).on("click", ".btn-toogle", function(e) {
            var id = $(this).attr('id');
            var minifiedBlock = $('.message-item').children('.minified');
            var expandedBlock = $('.message-item').children('.expanded');
            $(this).addClass('hidden');
            $(this).siblings('.btn-toogle').removeClass('hidden');
            
            $('.message-item').removeClass('hidden');
            $('.items-expand').addClass('hidden');
            
            if(id =='expand') {
                minifiedBlock.addClass('hidden').removeClass('visible');
                expandedBlock.removeClass('hidden').addClass('visible');
            } else {
                minifiedBlock.removeClass('hidden').addClass('visible');
                expandedBlock.addClass('hidden').removeClass('visible');
                $('.mailbox-reply-block').addClass('hidden');
            }
        });
        
        // message item minimize / expand
        $(document).on("click", ".message-item", function(e) {
            
            $('.mailbox-message-list li').removeClass('selected');
            
            //console.log(e.target.tagName);
            $(this).addClass('selected');
            var minifiedBlock = $(this).children('.minified');
            var expandedBlock = $(this).children('.expanded');
            
            if(e.target.tagName!='A' && e.target.tagName!='INPUT' && e.target.tagName!='TEXTAREA' && 
                !$(e.target).parents().is(".reply-message-form") && !$(e.target).parents().is(".blueimp-gallery")) {
                if(minifiedBlock.hasClass('hidden')){
                    minifiedBlock.removeClass('hidden').addClass('visible');
                    expandedBlock.addClass('hidden').removeClass('visible');
                    $(this).find('.mailbox-reply-block').addClass('hidden');
                }
                else if(expandedBlock.hasClass('hidden')){
                    minifiedBlock.addClass('hidden').removeClass('visible');
                    expandedBlock.removeClass('hidden').addClass('visible');
                }
            }
            
        });
        
        // trigger upload form
        $(document).on("click", ".select-file", function(e){
            //$("#XUploadForm_uploadimg").trigger("click");
            
            var form = $(this).closest('form');
            var counter = form.attr('id').match(/\d+/);
            var block = form.find('.reply-upload-block');
            $('#XUploadForm_uploadimg-'+counter).trigger("click");
            console.log(counter);
            e.stopPropagation();
            form.bind('fileuploadadded', function (e, data) {
                
                block.parent(".row").removeClass("hidden");
                $(".t-right").tooltipster('hide');
                //console.log(data.context);
                var that = $(this).data("fileupload");
                var errorsCount = $(".template-upload .error").length;
                var filesCount = $("#message-form-"+counter+" ul.files li").length;
                var removeNode = function () {
                    that._transition(data.context).done(
                        function () {
                            $(this).remove();
                            that._trigger("failed", e, data);                            
                        });
                };
                
                $(this).find(".close-form").bind("click", function(){
                    removeNode();
                    that._adjustMaxNumberOfFiles(1);
                });
            
                $.each(data.files, function (index, file) {
                    if(file.error && filesCount > $.yiiconversation.maxNumberOfFiles && errorsCount > 1){
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
            
                var that = $(this).data("fileupload");
                $(this).find(".close-form").bind("click", function(){
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
                var filesCount = $("#message-form-"+counter+" ul.files li").length;
                if(filesCount==0){
                    block.parent(".row").addClass("hidden");
                }
            })
            .bind('fileuploaddestroyed', function (e, data) {
                var filesCount = $("#message-form-"+counter+" ul.files li").length;
                if(filesCount==0){
                    block.parent(".row").addClass("hidden");
                }
            });
            
        });
        
        
        // we check if we have any notification from the user session
        if($.yiiconversation.notification!=''){
            $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[  " + $.yiiconversation.notificationCloseLabel + " ]</div></div>";
            $.jGrowl($.yiiconversation.notification, {
                header: $.yiiconversation.notificationHeader,
                theme: "cLight",
                life: 5000,
                sticky: false,
                closeTemplate: ""
            });
        }
        
        // we initialize the messages list
        $.yiiconversation.itemsInit();
        
        // open reply form btn
        $(document).on("click", ".open-reply", function(e){
            var item = $(this).closest("li.message-item");
            var id = item.attr('id').match(/\d+/);
            var block = $("#reply-block-"+id);
            
            
            var txtarea = item.find("textarea");
            
            if(block.hasClass("hidden")){
                block.removeClass("hidden");
                $.yiiconversation.getTags(id);
            }
        
            txtarea.focus();
            
        });
        
        // close reply form btn
        $(document).on("click", ".close-form", function(e) {
            e.stopPropagation();
            var item = $(this).closest("li.message-item");
            var txtarea = $(this).closest('form').find("textarea");
            var id = item.attr('id').match(/\d+/);
            
            item.find(".errorSummary").css({
                "display":"none"
            });
            
            $(this).closest('.mailbox-reply-block').addClass('hidden');
            
            txtarea.val("").trigger('autosize.resize');
            
            if($.yiiconversation.updateSender==true){
                item.find("#sendersSingle").trigger("click");
            }
            
          
            $.yiiconversation.openFieldTag = -1;
            
        });
        
        $(document).on("click", ".usr-labels", function(e) {
            e.stopPropagation();
            var id = $(this).attr('id').match(/\d+/);
            
            $(".usr-labels").removeClass("hidden");
            $(".c-tags").addClass("hidden");
            
            $("#contacts-lbs-"+id).removeClass('hidden');
            $("#str-labels-"+id).addClass('hidden');
            $.yiiconversation.openFieldTag = id;
            $("#contacts-lbs-"+id).data('uiTagit').tagInput.focus();
        //console.log($.yiiconversation.openFieldTag);
        });
        
        
        $(document).click( function(e) {
            //e.stopPropagation();
            if ( !$(e.target).hasClass('usr-labels') && !$(e.target).hasClass('c-tags') 
                && !$(e.target).hasClass('tagit-new') && !$(e.target).hasClass('ui-autocomplete-input')) {

                var id = $.yiiconversation.openFieldTag;
                
                if(id != -1) {
                    if($.yiiconversation.sendersLabels[id].length>0) {
                        $("#str-labels-"+id).empty();
                        $("#str-labels-"+id).text($.yiiconversation.sendersLabels[id].join(", "));
                        
                    } else {
                        $("#str-labels-"+id).empty();
                    }
                    $("#str-labels-"+id).removeClass('hidden');
                    $("#contacts-lbs-"+id).addClass('hidden');
                    
                }
            }
            
            if (!$(e.target).hasClass('message-item') && !$(e.target).parents().is(".message-item")
                && !$(e.target).parents().is(".tagit-choice")){
                $('.mailbox-message-list li').removeClass('selected');
            }
        });
        
        $(document).on("click", ".tags-btn", function(e){
            //e.stopPropagation();
            var labels = '';
            var senders = '';
            var ids = [];
            
            var item = $(this).closest('li.message-item');
            var itemId = item.attr('id').match(/\d+/);
           
            
            $.yiiconversation.openFieldTag = itemId;
            
            $.yiiconversation.sendersLabels[itemId] = [];
            
            $.yiiconversation.sendersIds[itemId].length=0;
            
            
            if($(this).attr('id') == 'sendersSingle') {
                senders = $.yiiconversation.jsonSingleSenders[itemId];
                $.yiiconversation.jsonSenders[itemId] = $.yiiconversation.jsonSingleSenders[itemId];
                labels = $.yiiconversation.sendersSingleLabels[itemId];
            } else if($(this).attr('id') == 'sendersMultiple') {
                senders = jQuery.parseJSON($.yiiconversation.jsonMultipleSenders);
                $.yiiconversation.jsonSenders[itemId] = jQuery.parseJSON($.yiiconversation.jsonMultipleSenders);
                labels = $.yiiconversation.sendersMultipleLabels;
            }
        
            $.yiiconversation.tagFlag = false; // we disable the autocomplete tags once we use dropdown
            
            if($.yiiconversation.tagInitialization == true) {
                $("#contacts-lbs-" + itemId).tagit("removeAll");
            }
            
            
            $.each(senders, function(key, label) {
                if($.yiiconversation.tagInitialization == true) { 
                    $("#contacts-lbs-" + itemId).tagit("createTag", label);
                }
                
                if($.inArray(label, $.yiiconversation.sendersLabels[itemId])==-1){
                    $.yiiconversation.sendersLabels[itemId].push(label);
                }
                
                if($.inArray(key, $.yiiconversation.sendersIds[itemId])==-1){
                    $.yiiconversation.sendersIds[itemId].push(key);
                } 
            });
            
            $("#Contacts_labels_" + itemId).val(labels);
            
            item.find("#MessageForm_to").val($.yiiconversation.sendersIds[itemId].join(","));
            
        });
        
        
        $(document).on("click", ".mailbox-btn", function(e) {
            e.stopPropagation();
            // recurses on ajax fail
            if($.yiiconversation.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            
            // build URL
            var url = $.yiiconversation.conversationUrl+$(this).attr('id')+'?ajax=1';
            $.yiiconversation.linkAction = $(this);
            
            if($(this).attr('id') == 'permanentdelete') {
                if($.yiiconversation.deleteConfirmation(url))
                    return true;
            }
            if($(this).attr('id') == 'markspam') {
                if($.yiiconversation.spamConfirmation(url))
                    return true;
            }
            return $.yiiconversation.submitAjax(url);
        });
        
        
        $(document).on("click", ".msg-btn", function(e){
            e.stopPropagation();
            // recurses on ajax fail
            if($.yiiconversation.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            // build URL
            var url = $.yiiconversation.messageUrl+$(this).attr('id')+'?ajax=1';
            
            $.yiiconversation.linkAction = $(this);
            
            var item = $(this).closest('li.message-item');
            var userid = item.find('.sender').attr('id').match(/\d+/); // return the number from string
            
            
            if($(this).attr('id') == 'markspam' && userid != $.yiiconversation.currentUser) {
                if($.yiiconversation.spamConfirmation(url, 'message'))
                    return true;
            }
            
            return $.yiiconversation.submitMessageAjax(url);
        });
       
    }
    
    $.yiiconversation.itemsInit = function () {
        // we start with initial values for tagit contacts
        $('li.message-item').each(function(i, v){
            
            $(this).find(".maibox-txt").characterCounter({
                characterCounterNeeded: false,
                maximumCharacters: 2000,
                minimumCharacters: 0,
                shortFormat: true,
                charactersLabel: " caratteri rimanenti",
                shortFormatSeparator: " / ",
                positionBefore: false,
                chopText: true
            });
            
            $(this).find(".maibox-txt").autosize();
           
            var itemId = $(this).attr('id').match(/\d+/);
            
            var fieldNode = $("#Contacts_labels_"+itemId);
            
            $.yiiconversation.jsonSenders[itemId] = [];
            
            $.yiiconversation.sendersSingleLabels[itemId] = [];
            
            $.yiiconversation.sendersSingleIds[itemId] = [];
            
            $.yiiconversation.sendersLabels[itemId] = [];
            
            $.yiiconversation.sendersSingleLabels[itemId] = fieldNode.val();
            $.yiiconversation.sendersSingleIds[itemId] = $(this).find("#MessageForm_to").val();
            
            $("#str-labels-"+itemId).empty();
            $("#str-labels-"+itemId).text($.yiiconversation.sendersSingleLabels[itemId]);
            
            var singleIds = $.yiiconversation.sendersSingleIds[itemId].split(',');
            var singleLabels = $.yiiconversation.sendersSingleLabels[itemId].split(', ');
            
            
            $.yiiconversation.jsonSingleSenders[itemId] = {};
            
            $.each(singleIds, function(i, val) {
                $.yiiconversation.jsonSingleSenders[itemId][val]=singleLabels[i];
            });
            
            $.yiiconversation.jsonSenders[itemId] = $.yiiconversation.jsonSingleSenders[itemId];
        });
    }
    
    
    $.yiiconversation.updateItem = function(itemId) {
        
        var item = $("#item-"+itemId);
        
        var fieldNode = $("#Contacts_labels_"+itemId);
        
        $.yiiconversation.jsonSenders[itemId] = [];
        $.yiiconversation.sendersSingleLabels[itemId] = [];
        $.yiiconversation.sendersSingleIds[itemId] = [];
        $.yiiconversation.sendersLabels[itemId] = [];
        
        item.find(".maibox-txt").characterCounter({
            characterCounterNeeded: false,
            maximumCharacters: 2000,
            minimumCharacters: 0,
            shortFormat: true,
            charactersLabel: " caratteri rimanenti",
            shortFormatSeparator: " / ",
            positionBefore: false,
            chopText: true
        });
            
        item.find(".maibox-txt").autosize();
        
        $.yiiconversation.sendersSingleLabels[itemId] = fieldNode.val();
        $.yiiconversation.sendersSingleIds[itemId] = item.find("#MessageForm_to").val();
            
        $("#str-labels-"+itemId).empty();
        $("#str-labels-"+itemId).text($.yiiconversation.sendersSingleLabels[itemId]);
            
        var singleIds = $.yiiconversation.sendersSingleIds[itemId].split(',');
        var singleLabels = $.yiiconversation.sendersSingleLabels[itemId].split(', ');
        
        $.yiiconversation.jsonSingleSenders[itemId] = {};
            
        $.each(singleIds, function(i, val) {
            $.yiiconversation.jsonSingleSenders[itemId][val]=singleLabels[i];
        });
            
        $.yiiconversation.jsonSenders[itemId] = $.yiiconversation.jsonSingleSenders[itemId];
    }
    
    $.yiiconversation.getTags = function(itemId) {
            
        var item = $("#item-"+itemId);
            
        var fieldNode = $("#Contacts_labels_"+itemId);
            
        $.yiiconversation.tagInitialization = true;
            
        var items = [];
        
        var filtered = [];
        
        var hasData = false;
            
        $.yiiconversation.sendersIds[itemId] = [];
            
            
        $("#contacts-lbs-"+itemId).tagit({
            singleField: true,
            singleFieldNode: fieldNode,
            autocomplete: {
                open: function() {
                    var position = item.find(".tagit .ui-autocomplete-input").offset(),
                    top = position.top;
        
                    $(".tagit-autocomplete").css({
                        top: (top) + 16 + "px"
                    });
        
                }
            },
            allowSpaces: true,
            animate: false,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                
                $.yiiconversation.tagFlag = true;
                $.ajax({
                    url:$.yiiconversation.autocompleteUrl,
                    data: {
                        term:request.term
                    },
                    dataType: "json",
                    success: function( data ) {
                        response($.map( data, function(item) {
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
                event.stopPropagation();
                var availableTags = [];
                
                //console.log($.yiiconversation.tagFlag);
                
                if($.yiiconversation.tagFlag == true) {
                    $.each(filtered, function(i, v){
                        availableTags.push(filtered[i].label);
                    });

                    if($.inArray(ui.tagLabel, availableTags)==-1 && hasData==true){
                        $(".tagit-new input").val("");
                        return false;
                    } 
                }
                
            
            },
            afterTagAdded: function(event, ui) {
                event.stopPropagation();
                
                $.each(filtered, function(i, v) {
                    if(ui.tagLabel==filtered[i].label){
                        items.push(filtered[i]);
                    } 
                });

                if(items.length>0){
                    $.each(items, function(i, v) {
                        if($.inArray(items[i].id, $.yiiconversation.sendersIds[itemId])==-1){
                            $.yiiconversation.sendersIds[itemId].push(items[i].id);
                        } 
                    });
                } else {
                    
                    $.each($.yiiconversation.jsonSenders[itemId], function(key, element) {
                        if(element==ui.tagLabel){
                            $.yiiconversation.sendersIds[itemId].push(key);
                        } 
                    });
                    
                }
                
                $.yiiconversation.sendersLabels[itemId].push(ui.tagLabel);
              
                
                item.find("#MessageForm_to").val($.yiiconversation.sendersIds[itemId].join(","));
            //console.log("item " + itemId + "has: "+item.find("#MessageForm_to").val());
            },
        
            afterTagRemoved: function(event, ui) {
               
                if(items.length>0) {

                    $.each(items, function(i, v) {
                        
                        if(ui.tagLabel == items[i].label ){
                            $.yiiconversation.sendersIds[itemId] = jQuery.grep($.yiiconversation.sendersIds[itemId], function(value) {
                                return value != items[i].id;
                            });
                        }

                    });

                    items = jQuery.grep(items, function(value) {
                        return value.label != ui.tagLabel;
                    });

                }
                
               
                $.each($.yiiconversation.jsonSenders[itemId], function(key, element) {
                    if(element==ui.tagLabel){
                        $.yiiconversation.sendersIds[itemId] = jQuery.grep($.yiiconversation.sendersIds[itemId], function(value) {
                            return value != key;
                        });

                    } 
                });
                
                $.yiiconversation.sendersLabels[itemId] = jQuery.grep($.yiiconversation.sendersLabels[itemId], function(value) {
                    return value != ui.tagLabel;
                });
                    
                item.find("#MessageForm_to").val($.yiiconversation.sendersIds[itemId].join(","));
            //console.log("item " + itemId + "has: "+item.find("#MessageForm_to").val());
                
            }
        
        });
        
    }
    
    
    /**
     * Return an array of message from the current conversation
     */
    $.yiiconversation.getMessages = function(status) {
        status = status || 'message-item';
        return $('ul.mailbox-message-list li.'+status).each(function(i,n){
            var listId = $(n).attr('id');
            var id = listId.match(/\d+/); // return the number from string
            return id; 
        }).get();
    }
    
    
    /**
     * Submit the ajax message actions.
     */
    $.yiiconversation.submitMessageAjax = function(url, spam){
        
        var link = $.yiiconversation.linkAction;
        var linkId = link.attr('id');
        var item = link.closest('li.message-item');
        var id = item.find('.data-wrap').attr('id').match(/\d+/); // return the number from string

        if(spam==true){
            var data = {
                'msgs': id,
                'folder': $.yiiconversation.currentFolder,
                'YII_CSRF_TOKEN': $.yiiconversation.csrf,
                //'spammed_id': 
                'spam':1
            };  
        } else {
            var data = {
                'msgs': id,
                'folder': $.yiiconversation.currentFolder,
                'YII_CSRF_TOKEN': $.yiiconversation.csrf
            };
        }
        
        //console.log(data)
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    if(response.redirect > 0) {
                        window.location = response.redirect_url;
                    } 
                    else {
                        if(response.notification==1){
                            if(response.clear==1){
                                
                                var flagHeader = $('.mailbox-conversation-header a#removeflag');
                                var flagLink = $('ul#nav-header li a#removeflag');
                                var flaggedMsgs = $.yiiconversation.getMessages('flagged');
                                
                                if(flaggedMsgs.length == 1){
                                    flagHeader.removeClass('visible').addClass('hidden');
                                    flagLink.parent().removeClass('visible').addClass('hidden');
                                    flagHeader.siblings('.mailbox-flag').removeClass('hidden').addClass('visible');
                                    flagLink.parent().siblings('.li-flag').removeClass('hidden').addClass('visible');
                                }
                                item.remove();
                            }
                            
                            if(response.type=='contact') {
                                link.parent().removeClass('visible').addClass('hidden');
                            }
                            
                            $.yiiconversation.updateView("information", response.header, response.success);
                        
                        } else {
                            if(response.type=='flag') {
                                
                                var flagHeader = $('.mailbox-conversation-header a#' + linkId);
                                var flagLink = $('ul#nav-header li a#' + linkId);
                                
                                if(linkId=='removeflag'){
                                    item.removeClass('flagged').addClass('not-flagged');
                                } else if(linkId=='addflag') {
                                    item.removeClass('not-flagged').addClass('flagged');
                                }
                                
                                var flaggedMsgs = $.yiiconversation.getMessages('flagged');
                                
                                link.removeClass('visible').addClass('hidden');
                                link.siblings('.msg-btn').removeClass('hidden').addClass('visible');
                                
                                
                                if((flaggedMsgs.length == 0 && linkId=='removeflag') || (flaggedMsgs.length == 1 && linkId=='addflag')){
                                    flagHeader.removeClass('visible').addClass('hidden');
                                    flagLink.parent().removeClass('visible').addClass('hidden');
                                    flagHeader.siblings('.mailbox-flag').removeClass('hidden').addClass('visible');
                                    flagLink.parent().siblings('.li-flag').removeClass('hidden').addClass('visible');
                                }
                                
                            } 
                        }
                    }
                    
                    
                    
                }
                else
                    $.yiiconversation.updateView("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiiconversation.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    
    /**
     * Submit the ajax conversation actions.
     */
    $.yiiconversation.submitAjax = function(url, withSpam){
        
        var link = $.yiiconversation.linkAction;
        var linkId = link.attr('id');
        var parentList = link.closest('ul');
        var flagHeader = $('.mailbox-conversation-header a#' + linkId);
        var flagLink = $('ul#nav-header li a#' + linkId);
        
        var lastItemFlag = $('ul.mailbox-message-list li:last-child a#' + linkId);
        
        // gather selected conversation id
        var convId = getParameter("item-id");
        if(convId == null || convId == '') {
            return false;
        }
        if(withSpam==true){
            var data = {
                'convs[]': convId,
                'folder': $.yiiconversation.currentFolder,
                'YII_CSRF_TOKEN': $.yiiconversation.csrf,
                'spam':1
            };  
        } else {
            var data = {
                'convs[]': convId,
                'folder': $.yiiconversation.currentFolder,
                'YII_CSRF_TOKEN': $.yiiconversation.csrf
            };
        }
        
        //console.log(data)
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    if(response.redirect>0) {
                        window.location = response.redirect_url;
                    }
                    if(response.notification>0){
                        $.yiiconversation.updateView("information", response.header, response.success);
                    }
                    if(response.type=='flag'){
                        
                        
                        if(linkId=='removeflag'){
                            $('ul.mailbox-message-list li a#' + linkId).each(function(i, v){
                                $(this).removeClass('visible').addClass('hidden');
                                $(this).siblings('.item-flag').addClass('visible').removeClass('hidden');
                                $(this).closest('li').removeClass('flagged').addClass('not-flagged');
                            });
                            
                        } else {
                            lastItemFlag.removeClass('visible').addClass('hidden');
                            lastItemFlag.siblings('.item-flag').removeClass('hidden').addClass('visible');
                            lastItemFlag.closest('li').removeClass('not-flagged').addClass('flagged');
                        }
                        
                        
                        
                        if(parentList.length>0){
                            link.parent().removeClass('visible').addClass('hidden');
                            link.parent().siblings('.li-flag').removeClass('hidden').addClass('visible');
                            flagHeader.removeClass('visible').addClass('hidden');
                            flagHeader.siblings('.mailbox-flag').removeClass('hidden').addClass('visible');
                        } else{
                            link.removeClass('visible').addClass('hidden');
                            link.siblings('.mailbox-flag').removeClass('hidden').addClass('visible');
                            flagLink.parent().removeClass('visible').addClass('hidden');
                            flagLink.parent().siblings('.li-flag').removeClass('hidden').addClass('visible');
                        }
                        
                    }   
                }
                else
                    $.yiiconversation.updateView("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiiconversation.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    /**
     * Submit the ajax reply form .
     */
    $.yiiconversation.submitAjaxForm = function(url, data){
        //var counter = $.yiiconversation.getMessages().length;
        
        var item = $("#item-"+$.yiiconversation.formCounter);
        var expanded = item.find(".expanded");
        var block = item.find(".mailbox-reply-block"); 
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,  
            success: function(response){
                if(response.success) {
                    $.yiiconversation.counter++;
                    
                    // we append the new item to the list
                    $("ul.mailbox-message-list").append(response.output);
                    
                    // we append the new script to the body
                    var newElem = document.createElement( 'script'); //create a script tag
                    newElem.type = 'text/javascript'; // add type attribute
                    newElem.innerHTML = response.js; // add content i.e. function definition and a call
                    document.body.appendChild(newElem);
                        
                    var txtarea = $('form').find("textarea");
                    
                    $(".mailbox-reply-block").addClass("hidden");
                        
                    $(".errorSummary").css({
                        "display":"none"
                    });
                    
                    $.yiiconversation.updateSender = false;
                    
                    $(".close-form").trigger('click');
                    
                    item.removeClass('selected');
                    item.find('.minified').removeClass('hidden').addClass('visible');
                    item.find('.expanded').addClass('hidden').removeClass('visible');
                    expanded.removeClass("loading");
                    block.removeClass("loading");
                    $(".loader-indicator").hide();
                    
                    $.yiiconversation.updateItem(response.counter);
                    $.yiiconversation.updateView("success", response.header, response.success);
                    
                }
                else
                    $.yiiconversation.updateView("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiiconversation.ajaxerror=1;
                return false;
            }
        });
        return false;
        
    }

    $.yiiconversation.updateView = function(type, header, text) {
        $('[data-toggle="dropdown"]').parent().removeClass('open');
        $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ hide all notifications ]</div></div>";
        $.jGrowl(text, {
            header: header,
            theme: "cLight "+type,
            life: 5000,
            sticky: false,
            closeTemplate: ""
        });
    }

    $.yiiconversation.init = function(){
        $.yiiconversation.updateConversation();
    }
    
	
    $.yiiconversation.deleteConfirmation = function(url) {
        var html;
        var buttons = [];
		
        
        if($.yiiconversation.currentFolder=='trash' || $.yiiconversation.currentFolder=='spam')
        {
            buttons.push({
                text: $.yiiconversation.okDialogLabel,
                id: "btn-confirm",
                "class": "ui-button-ok",
                click: function (){ 
                    
                    $.yiiconversation.submitAjax(url);
                    $(this).dialog("close");
                }
            });
               
            buttons.push({
                text: $.yiiconversation.cancelDialogLabel,
                id: "btn-cancel",
                "class": "ui-button-cancel",
                click: function (){
                    $(this).dialog("close");
                }
            });
                
                
            html = '<div class="dialog-confirm">' + $.yiiconversation.deleteTxt + '</div>';
        }
        else {
            return false;
        }
        $( html ).dialog({
            resizable: false,
            height:400,
            modal: true,
            buttons: buttons,
            title: $.yiiconversation.deleteTitle
        });
        return true;
       
    }
    
    $.yiiconversation.spamConfirmation = function(url, type) {
        var html;
        var buttons = [];
        type= type || "conversation";
        
        buttons.push({
            text: $.yiiconversation.okDialogLabel,
            id: "btn-confirm",
            "class": "ui-button-ok",
            click: function (){ 
                if(type=="message"){
                    $.yiiconversation.submitMessageAjax(url, true);
                } else {
                    $.yiiconversation.submitAjax(url, true); 
                }
                
                $( this ).dialog( "close" );
            }
        });
               
        buttons.push({
            text: $.yiiconversation.cancelDialogLabel,
            id: "btn-cancel",
            "class": "ui-button-cancel",
            click: function (){
                if(type=="message"){
                    $.yiiconversation.submitMessageAjax(url);
                } else {
                    $.yiiconversation.submitAjax(url); 
                }
                
                $( this ).dialog("close");
            }
        });
        
        html = '<div class="dialog-confirm">' + $.yiiconversation.spamTxt + '</div>';
            
        $(html).dialog({
            resizable: false,
            width: 400,
            modal: true,
            buttons: buttons,
            title: $.yiiconversation.spamTitle
        });
        return true;
        
    }

    

})(jQuery); // jQuery
$.yiiconversation.init();
