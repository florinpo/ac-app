
(function($) {
    // track selected button
    $.productsManage.ajaxerror=0;
    $.productsManage.selectedIds = [];
    $.productsManage.filters = {};
    
    
    $.productsManage.updateProducts = function() {
        
        
        //checkbox bootstrap
        $('input.select-on-check-all, input.select-on-check').checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty',
            constructorCallback: null,
            defaultState: true,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });
        
        // check/uncheck all
        $(document).on('change', 'input.select-on-check-all', function(e) {
            var $checks = $('input.select-on-check');
            var $checksAll = $('input.select-on-check-all');
            var $rows = $('#product-grid tbody').find('tr');
            
            if ($(this).is(':checked')) {
                $rows.addClass('selected');
                $checks.prop('checked', true);
                $checksAll.prop('checked', true);
                $checks.checkbox({
                    checked: true
                });
                $('#deleteselected').removeClass('disabled');
            } else {
                $rows.removeClass('selected');
                $checks.prop('checked', false);
                $checksAll.prop('checked', false);
                $checks.checkbox({
                    checked: false
                });
                $('#deleteselected').addClass('disabled');
            }
        });
        
        $(document).on('change', 'input.select-on-check', function(){
            
            var $checksAll = $('input.select-on-check-all');
            var $elem = $(this);
            var $row = $elem.closest('tr');
            var rowsIds = $.productsManage.getSelectedRows();
            var rowsCount = $('#product-grid tbody tr').length;
            
            //console.log(rowsIds.length);
            
            if(rowsIds.length < rowsCount){
                $checksAll.prop('checked', false);
                $checksAll.checkbox({
                    checked: false
                }); 
            }
            
            if(rowsIds.length > 0){
                $('#deleteselected').removeClass('disabled');
            } else {
                $('#deleteselected').addClass('disabled');
            }
            
            if($elem.is(':checked')){
                $row.addClass('selected');
            } else {
                $row.removeClass('selected');
            }
            return false;
        });
        
        
        // grid button actions
        $('.btn-grid').unbind('click').click(function(){
            
            var rowsIds = $.productsManage.getSelectedRows();
            
            var btn_id = $(this).attr('id');
            
            // build the url
            var url = $.productsManage.productControllerUrl+btn_id;
                        
            url = jQuery.param.querystring(url, 'ajax=1');
            
            
            if(rowsIds.length > 0){
                
                if(btn_id == 'deleteselected') {
                    if($.productsManage.deleteConfirmation(url))
                        return true;
                }
                
                return $.productsManage.submitAjax(url);
            }
            
        });
        
        /***=== iPhone checkboxes  ===***/
        $('.yes_no :checkbox').iButton({
            labelOn: $.productsManage.yesLabel,
            labelOff: $.productsManage.noLabel,
            enableDrag: false
        });
        
     
        $(".icheck").on("change", function(e){
            
            var $elem = $(this);
            var id = $(this).attr('id').match(/\d+/)[0];
            
            var val;
             
            if($.productsManage.ajaxerror==1){
                // ajax failed, submit form without ajax
                return true;
            }
            
            if($elem.is(':checked')){
                val = 1;
            } else {
                val = 0;
            }
           
            // build URL
            var url = $.productsManage.productControllerUrl+'visiblehome';
            url = jQuery.param.querystring(url, 'ajax=1');
            
            
            return $.productsManage.ajaxHomeVisible(url, id, val);
             
        });
        
        // grid custom pagination ajax
        
        $('#product-grid-pagination a').unbind('click').click(function(e){
            
            var $elem = $(this);
            
            //console.log(counter);
            
            var url = $(this).attr('href');
            url = jQuery.param.querystring(url, 'ajax=product-grid');
            
            
            if(!$elem.hasClass('hidden')){
                
                $.fn.yiiListView.update("product-grid", {
                    url: url,
                    complete: function(){
                        $.productsManage.updatePagination(url);
                    }
                });
            }
            
            return false;
            
        });
        
        // filter links
        $('.filter-link').unbind('click').click(function(e){
           
            var id = $(this).attr('id');
            
            if(id == "create-asc" ){
                
                    $.productsManage.filters.date = "create-asc";
                
            } else if (id == "create-desc" ){               
               
                    $.productsManage.filters.date = "create-desc";
                
            } else if (id == "update-desc" ){
                
                    $.productsManage.filters.date = "update-desc";
                
            }
            
            if(id=="type-active"){
                
                    $.productsManage.filters.type = "active";
                
            } else if(id=="type-pending"){
                
                    $.productsManage.filters.type = "pending";
               
            } else if(id=="type-reqedit"){
                
                    $.productsManage.filters.type = "reqedit";
               
            }
            else if(id=="type-all"){
               
                    $.productsManage.filters.type = "all";
               
            }
            
            
            $.fn.yiiListView.update("product-grid", {
                data: jQuery.param($.productsManage.filters),
                complete: function(){
                    
                    $('.gr-filters .btn-group').removeClass('open');
                    if($.productsManage.filters.date == 'create-asc') {
                        $('.date').removeClass('checked');
                        $("#create-asc").addClass("checked");
                    }
                    if($.productsManage.filters.date == 'create-desc') {
                        $('.date').removeClass('checked');
                        $("#create-desc").addClass("checked");
                    }
                    if($.productsManage.filters.date == 'update-desc') {
                        $('.date').removeClass('checked');
                        $("#update-desc").addClass("checked");
                    }
                    
                    if($.productsManage.filters.type == 'active') {
                        $('.type').removeClass('checked');
                        $("#type-active").addClass("checked");
                    }
                    if($.productsManage.filters.type == 'pending') {
                        $('.type').removeClass('checked');
                        $("#type-pending").addClass("checked");
                    }
                    if($.productsManage.filters.type == 'reqedit') {
                        $('.type').removeClass('checked');
                        $("#type-reqedit").addClass("checked");
                    }
                    if($.productsManage.filters.type == 'all') {
                        $('.type').removeClass('checked');
                        $("#type-all").addClass("checked");
                    }
                    
                    
                    
                }
            });
            
            return false;
            
        });
    }
    
    // this function update the pagination-pn of the grid
    $.productsManage.updatePagination = function(url){
        
        var prevLink = $('#product-grid-pagination a.previous');
        var nextLink = $('#product-grid-pagination a.next');
        var total = $('#product-grid-pagination .total').text();
        var currentPage = getUrlVar("page", url);
        var rows_len =  $('#product-grid tbody tr').length;
        
        if(rows_len == 0){
            $('#product-grid-pagination').addClass('hidden');
        }
        
        console.log(rows_len);
        
        if(currentPage==''){
            currentPage = 1;
        }
            
        if(currentPage == 1) {
            prevLink.addClass('hidden');
            nextLink.removeClass('hidden');
        }
        if(currentPage > 1){
            prevLink.removeClass('hidden');
        }
        if(currentPage == total) {
            nextLink.addClass('hidden');
        }
        
       
            
       $('#product-grid-pagination .curent-page').text(currentPage);
        
    }
    
    
    //return an array of selected checkboxes
    $.productsManage.getSelectedRows = function()
    {
        return $('#product-grid').find('input.select-on-check:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    
    // this function handle the actions for selected rows [ex: deleteselected]
    $.productsManage.submitAjax = function(url){

        // gather selected inputs
        var rowsIds = $.productsManage.getSelectedRows();
        
        
        var data = {
            'ids[]': rowsIds,
            'YII_CSRF_TOKEN': $.productsManage.csrf
        };
        //console.log(data);
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    $.fn.yiiListView.update("product-grid", {
                        data: '',
                        complete: function(){
                            $.productsManage.updateConfirm("info", response.header, response.success);
                        }
                    });
                }
                else
                    $.productsManage.updateConfirm("error", response.header, response.error);
                return false;
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.productsManage.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    /**
     * Submit the ajax iphone buttons toggle action
     */
    $.productsManage.ajaxHomeVisible = function(url, id, val){
        var shopId = $('#pshop_'+id).val();
        var data = {
            'pid': id,
            'val': val,
            'shop':shopId,
            'YII_CSRF_TOKEN': $.productsManage.csrf
        };  
        
        //console.log(data);
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(response){
                if(response.success) {
                    console.log(response.success);
                }
                else {
                    $('input#ic-'+id).prop('checked', false);
                    $('input#ic-'+id).iButton("destroy");
                    $('.yes_no :checkbox').iButton({
                        labelOn: $.productsManage.yesLabel,
                        labelOff: $.productsManage.noLabel,
                        enableDrag: false
                    });
                    $.productsManage.updateConfirm("error", response.header, response.error);
                //console.log($('input#ic-'+id).attr('id')); 
                //return false;
                }
                
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.productsManage.ajaxerror=1;
                return false;
            }
        });
        return false;
    }
    
    // confirm delete action dialog
    $.productsManage.deleteConfirmation = function(url) {
        
        var html;
        var buttons = [];
        var rowsCount = $.productsManage.getSelectedRows().length;
        
        if($.productsManage.confirmDelete == 1)
        {
            buttons.push({
                text: $.productsManage.okDialogLabel,
                id: "btn-confirm",
                "class": "ui-button-ok",
                click: function (){ 
                    $.productsManage.submitAjax(url);
                    $(this).dialog("close");
                }
            });
               
            buttons.push({
                text: $.productsManage.cancelDialogLabel,
                id: "btn-cancel",
                "class": "ui-button-cancel",
                click: function (){
                    $(this).dialog("close");
                }
            });
            
            html = '<div class="dialog-confirm">' + rowsCount + ' ' + $.productsManage.deleteConfirmTxt + '</div>';
        }
        else {
            return false;
        }
        $(html).dialog({
            resizable: false,
            width: 400,
            modal: true,
            buttons: buttons,
            title: $.productsManage.deleteConfirmTitle
        });
        return true;
       
    }
    
    $.productsManage.updateConfirm = function(type, header, text) {
        
        $.jGrowl.defaults.closerTemplate = "<div class=\'cLight\'><div class=\'closerWrap\'>[ " + $.productsManage.notificationCloseLabel + " ]</div></div>";
        $.jGrowl.defaults.pool = 4;
        $.jGrowl(text, {
            header: header,
            theme: "cLight "+type,
            life: 6000,
            sticky: false,
            closeTemplate: ""
                
        });
        
    }
    
    $.productsManage.init = function(){
        $.productsManage.updateProducts();
    }
    

})(jQuery); // jQuery
$.productsManage.init();
