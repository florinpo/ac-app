
(function($) {
    // track selected button
    $.yiimailbox.ajaxerror=0;
    $.yiimailbox.linkGrid='';
    $.yiimailbox.filters = {};
    
    
    $.yiimailbox.updateMailbox = function(){
        
        
        ///// CHECKBOX TRANSFORM /////
        
        $('input[type="checkbox"]').checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty',
            constructorCallback: null,
            defaultState: false,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });
        
        // we check if we have any notification from the user session
        if($.yiimailbox.notification!=''){
            $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ " + $.yiimailbox.notificationCloseLabel + " ]</div></div>";
            $.jGrowl.defaults.pool = 4;
            $.jGrowl($.yiimailbox.notification, {
                header: $.yiimailbox.notificationHeader,
                theme: "cLight",
                life: 5000,
                sticky: false,
                closeTemplate: ""
            });
        }
        
        /*
        * Check/Uncheck All
        */
       
        $('.checkall').unbind('click').click(function(e){
            $('#mailbox').find(':checkbox').attr('checked','checked');
            $('#mailbox').find(':checkbox').checkbox({
                checked: true
            });
            
            $("#mailbox").find('tr').addClass('selected');
            
            $(this).addClass('inactive');
            $('.uncheckall').removeClass('inactive');
            return false;
        });
        
        $('.uncheckall').unbind('click').click(function(e){
            $('#mailbox').find(':checkbox').attr('checked',false);
            $('#mailbox').find(':checkbox').checkbox({
                checked: false
            });
            $("#mailbox").find('tr').removeClass('selected');
            $(this).addClass('inactive');
            $('.checkall').removeClass('inactive');
            return false;
        });

        $('#mailbox').delegate(':checkbox','change',function(){
            var $elem = $(this);
            var $tr = $elem.closest('tr');
            var convs = $.yiimailbox.getSelectedItems();
            var rowsCount = $('table.mailbox-items tr').length;
            if(convs.length < rowsCount){
                $('.uncheckall').addClass('inactive');
                $('.checkall').removeClass('inactive');
            }
            
            if($elem.is(':checked')){
                $tr.addClass('selected');
            }else{
                $tr.removeClass('selected');
            }
            return false;
        });
        
        // clickable rows
        $('table.mailbox-items tr td').each(function() {
            $(this).hover(
                function() {
                    $(this).parent().addClass('hover');
                    status = $(this).parent().find('a').attr('href');
                },
                function() {
                    $(this).parent().removeClass('hover');
                    status = '';
                });
                
            $(this).not('td.gridCheckbox').not('td.gridActions').click(function() {
                location = $(this).parent().find('a').attr('href');
            });
            $(this).not('td.gridCheckbox').css('cursor', 'pointer');
        });
        
        // we disable default right click for table rows
        $('table.mailbox-items tr').bind('contextmenu', function(e) {
            if (e.target.type !== 'checkbox' && !$(e.target).hasClass('btn-checkbox')) {
                $(':checkbox', this).trigger('click');
            }
            e.preventDefault();
        });
        
        // we add  hover class for table rows
        $("table.mailbox-items tr").hover(
            function () {
                $(this).addClass("hover");
            },
            function () {
                $(this).removeClass("hover");
            }
            );
            
        
        /*
	* Mailbox buttons
	*/
        $('.mailbox-btn').unbind('click').click(function(e){
            // recurses on ajax fail
            if($.yiimailbox.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            // build URL
            var url = $.yiimailbox.controllerUrl+$(this).attr('id');
                        
            url = jQuery.param.querystring(url, 'ajax=1');
            
            if($(this).attr('id') == 'permanentdelete') {
                if($.yiimailbox.deleteConfirmation(url))
                    return true;
            }
            
            return $.yiimailbox.submitAjax(url);
        });
        
        $('.mailbox-grid-btn').unbind('click').click(function(e){
            // recurses on ajax fail
            if($.yiimailbox.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            // build URL
            var url = $.yiimailbox.controllerUrl+$(this).attr('id');
                        
            url = jQuery.param.querystring(url, 'ajax=1');
            
            var convId = $(this).parent().parent().find('input:checkbox').val();
            $.yiimailbox.linkGrid = $(this);
            
            return $.yiimailbox.submitAjaxGrid(url, convId);
        });
        
        // filter links
        $('.filter-link').click(function(e){
            var id = $(this).attr('id');
            
            if(id == "date-asc" ){
                if($.yiimailbox.filters.date == null || $.yiimailbox.filters.date == "desc") {
                    $.yiimailbox.filters.date = "asc";
                } else {
                    $.yiimailbox.filters.date = "desc";
                }
            } else if (id == "date-desc" ){
                if($.yiimailbox.filters.date == null || $.yiimailbox.filters.date == "asc") {
                    $.yiimailbox.filters.date = "desc";
                } else {
                    $.yiimailbox.filters.date = "asc";
                }
            }
            
            if(id=="type-unread"){
                if($.yiimailbox.filters.type == null || $.yiimailbox.filters.type == "none") {
                    $.yiimailbox.filters.type = "unread";
                } else {
                    $.yiimailbox.filters.type = "none";
                }
            } else if(id=="type-flagged"){
                if($.yiimailbox.filters.type == null || $.yiimailbox.filters.type != "none") {
                    $.yiimailbox.filters.type = "flagged";
                } else {
                    $.yiimailbox.filters.type = "none";
                }
            }
            else if(id=="type-none"){
                if($.yiimailbox.filters.type == null || $.yiimailbox.filters.type != "") {
                    $.yiimailbox.filters.type = "none";
                }
            }
            
            
            $.fn.yiiListView.update("mailbox", {
                data: jQuery.param($.yiimailbox.filters),
                complete: function(){
                    
                    if($.yiimailbox.filters.date == 'asc') {
                        $('.date').removeClass('checked');
                        $("#date-asc").addClass("checked");
                    }
                    if($.yiimailbox.filters.date == 'desc') {
                        $('.date').removeClass('checked');
                        $("#date-desc").addClass("checked");
                    }
                    
                    if($.yiimailbox.filters.type == 'unread') {
                        $('.type').removeClass('checked');
                        $("#type-unread").addClass("checked");
                    }
                    if($.yiimailbox.filters.type == 'flagged') {
                        $('.type').removeClass('checked');
                        $("#type-flagged").addClass("checked");
                    }
                    if($.yiimailbox.filters.type == 'none') {
                        $('.type').removeClass('checked');
                        $("#type-none").addClass("checked");
                    }
                    
                }
            });
            
            return false;
            
        });
        
    }
    
    /**
	* Submit the ajax form for clicked buttons [flag|unflag, read|unread].
	*/
    $.yiimailbox.submitAjaxGrid = function(url, convid){
        
        var data = {
            'convs[]': convid,
            'folder': $.yiimailbox.currentFolder,
            'YII_CSRF_TOKEN': $.yiimailbox.csrf
        };
        //console.log(data)
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    
                    var link = $.yiimailbox.linkGrid;
                    var tr = link.parent().parent();
                    
                    if (link.attr('id')=='markread'){
                        tr.removeClass('msg-unread').addClass('msg-read');
                        link.attr('id', 'markunread');
                    } else if(link.attr('id')=='markunread') {
                        tr.removeClass('msg-read').addClass('msg-unread');
                        link.attr('id', 'markread');
                    }
                    
                    if (link.attr('id')=='addflag'){
                        tr.removeClass('not-flagged').addClass('flagged');
                        link.attr('id', 'removeflag');
                    } else if(link.attr('id')=='removeflag') {
                        tr.removeClass('flagged').addClass('not-flagged');
                        link.attr('id', 'addflag');
                    }
                    
                }
                else
                    $.yiimailbox.updateConfirm("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiimailbox.ajaxerror=1;
                return false;
            }
        });
        return false;
    }

    /**
    * Return an array of the selected (ie. checked) conversations
    */
    $.yiimailbox.getSelectedItems = function()
    {
        return $('#mailbox input:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }

    /**
	* Submit the ajax form for clicked buttons/drag-n-drop delete.
	*/
    $.yiimailbox.submitAjax = function(url){

        // gather selected inputs
        var convs = $.yiimailbox.getSelectedItems();
        if(convs.length == 0) {
            return false;
        }
        
        var data = {
            'convs[]': convs,
            'folder': $.yiimailbox.currentFolder,
            'YII_CSRF_TOKEN': $.yiimailbox.csrf
        };
        //console.log(data)
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    $.fn.yiiListView.update("mailbox", {
                        data: '',
                        complete: function(){
                            $.yiimailbox.updateConfirm("info", response.header, response.success);
                        }
                    });
                }
                else
                    $.yiimailbox.updateConfirm("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.yiimailbox.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    $.yiimailbox.updateConfirm = function(type, header, text) {
        $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ " + $.yiimailbox.notificationCloseLabel + " ]</div></div>";
        $.jGrowl(text, {
            header: header,
            theme: "cLight "+type,
            life: 6000,
            sticky: false,
            closeTemplate: ""
        });
    }
    
    
    // confirm delete action dialog
    $.yiimailbox.deleteConfirmation = function(url) {
        var html;
        var buttons = [];
        
        if($.yiimailbox.currentFolder=='trash' || $.yiimailbox.currentFolder=='spam')
        {
            buttons.push({
                text: $.yiimailbox.okDialogLabel,
                id: "btn-confirm",
                "class": "ui-button-ok",
                click: function (){ 
                    
                    $.yiimailbox.submitAjax(url);
                    $(this).dialog("close");
                }
            });
               
            buttons.push({
                text: $.yiimailbox.cancelDialogLabel,
                id: "btn-cancel",
                "class": "ui-button-cancel",
                click: function (){
                    $(this).dialog("close");
                }
            });
                
                
            html = '<div class="dialog-confirm">' + $.yiimailbox.deleteTxt + '</div>';
        }
        else {
            return false;
        }
        $( html ).dialog({
            resizable: false,
            width: 400,
            modal: true,
            buttons: buttons,
            title: $.yiimailbox.deleteTitle
        });
        return true;
       
    }
    
    
    
    

    $.yiimailbox.init = function(){
        $.yiimailbox.updateMailbox();
    }
    

})(jQuery); // jQuery
$.yiimailbox.init();
