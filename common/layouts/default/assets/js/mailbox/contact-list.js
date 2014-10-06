
(function($) {
    // track selected button
    $.yiicontactlist.ajaxerror=0;
    $.yiicontactlist.request = '';
    $.yiicontactlist.requestFlag = true;
    //$.yiicontactlist.urlParams = {};
    $.yiicontactlist.filters = {};
    
    
    $.yiicontactlist.updateCList = function(){
        
        ///// CHECKBOX TRANSFORM /////
        
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
        
        // we check if we have any notification from the user session
        if($.yiicontactlist.notification!=''){
            $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ " + $.yiicontactlist.notificationCloseLabel + " ]</div></div>";
            $.jGrowl($.yiicontactlist.notification, {
                header: $.yiicontactlist.notificationHeader,
                theme: "cLight",
                life: 5000,
                sticky: false,
                closeTemplate: ""
            });
        }
        
        if($.yiicontactlist.getItems().length>10){
            // we apply the scroll to the #clist-panel
            $('#clist-panel ul').slimScroll({
                height: '300px',
                wheelStep: 10,
                distance: '0px',
                railColor: '#CCC',
                railOpacity: 1,
                railVisible: true,
                alwaysVisible: true
            });
            $('.slimScrollBar').css({
                'border-radius':0
            });
            $('.slimScrollRail').css({
                'border-radius':0
            });
        }
        
        
        $('input#check-all').on("change", function () {
            $('ul.contacts-items').find('input[type=checkbox]').prop('checked', this.checked);
            var item = $("ul.contacts-items > li");
             
            if($(this).is(':checked')){
                item.addClass('selected');
                item.find(':checkbox').checkbox({
                    checked: true
                });
            }else{
                item.removeClass('selected');
                item.find(':checkbox').checkbox({
                    checked: false
                });
            }
            
        });
        
        $('#contact-list').delegate(':checkbox','change',function(e){
            e.stopPropagation();
            var $elem = $(this);
            var item = $elem.closest('li');
            var selectedContacts = $.yiicontactlist.getSelectedItems();
            var rowsCount = $('ul.contacts-items li').length;
            
            // if all checkbox selected then we add "checked" to the .checkAll
            if(selectedContacts.length < rowsCount){
                $('input#check-all').checkbox({
                    checked: false
                });
            } else {
                $('input#check-all').checkbox({
                    checked: true
                });
            }
            
            if(selectedContacts.length == 1) {
                $.yiicontactlist.ajaxViewContact(selectedContacts[0]);
                $('#container-view .loader').show();
            } else {
                $('#container-view .loader').hide();
                $('.contact-view').hide();
                $('.multiple-selected').hide();
                $('.default-view').show();
                if(typeof $.yiicontactlist.request ==='object'){
                    
                    //if(typeof  $ajax_request==='object') {$ajax_request.abort()};
                    $.yiicontactlist.request.abort();
                }
                
                if(selectedContacts.length > 1){
                    $('.default-view').hide();
                    $('.multiple-selected').show();
                    $('.multiple-selected h2 span').html(selectedContacts.length);

                }
            }
            
            //we apply the selected class for the list item
            if($elem.is(':checked')){
                item.addClass('selected');
                item.find(':checkbox').checkbox({
                    checked: true
                });
            } else{
                item.removeClass('selected');
                item.find(':checkbox').checkbox({
                    checked: false
                });
            }
            return false;
        });
        
        // we add  hover class for table rows
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
        
        
        $(document).on("click", ".cview-btn", function(e){
            
            var url = $.yiicontactlist.controllerUrl+$(this).attr('id')+'?ajax=1';
            
            if($(this).attr('id') == 'delete') {
                if($.yiicontactlist.deleteConfirmation(url))
                    return true;
            }
            
            return $.yiicontactlist.submitAjax(url);
          
        });
        
        /** Filter links
         *
         */
        
        $('.filter-link').click(function(e){
            var id = $(this).attr('id');
            
            if($(this).hasClass('checked')){
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
            
            //console.log($.yiicontactlist.filters);
            
            if(id == "name-desc" ){
                if($.yiicontactlist.filters.name == null || $.yiicontactlist.filters.name == "asc") {
                    $.yiicontactlist.filters.name = "desc";
                } else {
                    $.yiicontactlist.filters.name = "asc";
                   
                }
            }
            
            if(id == "has-shop" ){
                if($.yiicontactlist.filters.has_shop == null || $.yiicontactlist.filters.has_shop == "0") {
                    $.yiicontactlist.filters.has_shop = "1";
                
                } else {
                    $.yiicontactlist.filters.has_shop = "0";
                    $.yiicontactlist.filters.premium = "0";
                }
            }
            
            if(id == "premium") {
                if($.yiicontactlist.filters.premium == null || $.yiicontactlist.filters.premium == "0") {
                    $.yiicontactlist.filters.premium = "1";
                } else {
                    $.yiicontactlist.filters.premium = "0";
                }
            }
            
            
            
            
            $.fn.yiiListView.update("contact-list", {
                data: jQuery.param($.yiicontactlist.filters),
                complete: function(){
                    
                    if($.yiicontactlist.filters.name == 'desc') {
                        $("#name-desc").addClass("checked");
                    } 
                    
                    if($.yiicontactlist.filters.has_shop == 1) {
                        $("#has-shop").addClass("checked");
                        $("#premium").closest("li").removeClass("hidden");
                    }
                    
                    if($.yiicontactlist.filters.premium == 1) {
                        $("#premium").addClass("checked");
                    }
                    
                }
            });
            
            return false;
            
        });
        
       
    }
    
    
    /**
         * Return an array of selected checkbox items
         */
    $.yiicontactlist.getSelectedItems = function()
    {
        return $('ul.contacts-items li input:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    /**
         * Return an array items
         */
    $.yiicontactlist.getItems = function()
    {
        return $('ul.contacts-items li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
    
    
    
    // function to display the contact details
    $.yiicontactlist.ajaxViewContact = function(val) {
           
        var url = $.yiicontactlist.controllerUrl+'viewcontact?ajax=1';
        
        var data = {
            'cid': val,
            'YII_CSRF_TOKEN': $.yiicontactlist.csrf
        };
        
            
        $.yiicontactlist.request = $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                        
                    $('#container-view .loader').hide();
                    $("#container-view .default-view").hide();
                    $(".multiple-selected").hide();
                    var view = $(
                        "<div class=\'content\'>"
                        + "<h2>" + response.display_name + "</h2>"
                        + "<div class=\'thumbnail\'><img src=" + response.thumbnail_url + " alt=" + response.display_name + "></div>"
                        + "<div class=\'contact-details\'>" + response.display_name + "</div></div>"
                        );         
                        
                    // if the content div exist we delete it
                    if($(".contact-view .content").length>0){
                        $(".contact-view .content").remove();
                    }
                    $(".contact-view").append(view);
                    $(".contact-view").hide().fadeIn();
                    
                    //$(".cview-btn").bind("click");
                }
                else
                    $.yiicontactlist.updateView("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiicontactlist.ajaxerror=1;
                return false;
            }
        });
            
        return false;
    }
    
    
    /**
         * Submit the ajax message actions.
         */
    $.yiicontactlist.submitAjax = function(url){
        
        $.yiicontactlist.request = '';
        var cids = $.yiicontactlist.getSelectedItems();
        if(cids.length == 0) {
            return false;
        }
        
        var data = {
            'cids': cids,
            'YII_CSRF_TOKEN': $.yiicontactlist.csrf
        };
        
        //console.log(data)
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            beforeSend : function (){
                $('#container-view .loader').show();
            },
            success: function(response){
                if(response.success) {
                    $('#container-view .loader').hide();
                    if(response.redirect > 0){
                        window.location = response.redirect_url;
                    } else {
                        location.reload(); 
                    }
                }
                else
                    $.yiicontactlist.updateView("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiicontactlist.ajaxerror=1;
                return false;
            }
        });
        return false;
    }

    $.yiicontactlist.updateView = function(type, header, text) {
        $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ hide all notifications ]</div></div>";
        $.jGrowl(text, {
            header: header,
            theme: "cLight "+type,
            life: 5000,
            sticky: false,
            closeTemplate: ""
        });
    }

    $.yiicontactlist.init = function(){
        $.yiicontactlist.updateCList();
    }
    
	
    $.yiicontactlist.deleteConfirmation = function(url) {
        var html;
        var buttons = [];
		
        if($.yiicontactlist.confirmDelete==1) {
            
            buttons.push({
                text: $.yiicontactlist.confirmDialogLabel,
                id: "btn-confirm",
                "class": "ui-button-ok",
                click: function (){ 
                    $.yiicontactlist.submitAjax(url);
                    $(this).dialog( "close" );
                }
            });
               
            buttons.push({
                text: $.yiicontactlist.cancelDialogLabel,
                id: "btn-cancel",
                "class": "ui-button-cancel",
                click: function (){
                    $(this).dialog( "close" );
                }
            });
            
            html = '<div class="dialog-confirm">' + $.yiicontactlist.deleteConfirmTxt + '</div>';
            
           
            $(html).dialog({
                modal: true,
                resizable: false,
                width:400,
                title: $.yiicontactlist.deleteConfirmTitle,
                buttons: buttons
            });
            return true;
        }
        else 
            return false;
    }
    

})(jQuery); // jQuery
$.yiicontactlist.init();
