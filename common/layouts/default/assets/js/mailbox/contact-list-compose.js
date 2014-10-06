
(function($) {
    // track selected button
    $.yiicontactlist.ajaxerror=0;
    $.yiicontactlist.request = '';
    
    $.yiicontactlist.updateCList = function(){
        
        ///// CHECKBOX TRANSFORM /////
        
        $('input[type="checkbox"]').checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-checkbox-checked',
            uncheckedClass: 'icon-checkbox-unchecked',
            constructorCallback: null,
            defaultState: true,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });
        
        if($.yiicontactlist.getItems().length > 10){
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
                $('#selected-view .empty').hide();
                $('.contacts li').each(function(i){
                    $(this).remove();   
                });
                
                $('ul.contacts-items input:checked').each(function(i, v){
                    var label = $(this).closest('li').find('.cUser').text();
                    var value = $(this).val();
                        
                    var li = $('<li id =cid-' + value + '>' + label + '</li>');
                    $('ul.contacts').append(li);
                });
                
            } else {
                item.removeClass('selected');
                item.find(':checkbox').checkbox({
                    checked: false
                });
                
                $('#selected-view .empty').show();
                $('.contacts li').each(function(i){
                    $(this).remove();   
                })
            }
            
        });
        
        
        $('ul.contacts-items input[type=checkbox]').on('change',function(e){
            ///$('#contact-list').delegate(':checkbox','change',function(e){
            
            var $elem = $(this);
            var item = $elem.closest('li');
            var val = $elem.val();
            var label = item.text();
            var selectedContacts = $.yiicontactlist.getSelectedIds();
            var rowsCount = $('#contact-list ul li').length;
            
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
            
            if(selectedContacts.length > 0) {
                $('#selected-view .empty').hide();
            } else {
                $('#selected-view .empty').show();
            }
            
            //we apply the selected class for the list item
            if($elem.is(":checked")){
                
                item.addClass("selected");
                var li = $('<li id =cid-' + val + '>' + label + '</li>');
                $('ul.contacts').append(li);
                
            } else {
                item.removeClass('selected');
                $('ul.contacts li#cid-'+val).remove();
            }
            
            return false;
        });
       
    }
    
    
    /**
	* Return an array of selected checkbox items
	*/
    $.yiicontactlist.getSelectedIds = function()
    {
        return $('ul.contacts-items li input:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    /**
	* Return the array of items of the objects li
	*/
    $.yiicontactlist.getItems = function()
    {
        return $('ul.contacts-items li').map(function(i,n) {
            return $(n);
        }).get(); //get converts it to an array
    }
    
    /**
	* Return the array of items of the objects li with checkbox child selected
	*/
    $.yiicontactlist.getSelectedItems = function()
    {
        var selected = [];
        $('ul.contacts-items li input:checked').each(function(i,n) {
            selected.push($(n).closest('li'));
        }); 
        return selected;
    }
    
    /**
	* Submit the ajax message actions.
	*/
    $.yiicontactlist.submitAjax = function(url){
        
        //        var link = $.yiicontactlist.linkMessage;
        //        var item = link.closest('li');
        //        var id = item.attr('id').match(/\d+/); // return the number from string
        var cids = $.yiicontactlist.getSelectedIds();
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
    

    $.yiicontactlist.init = function(){
        $.yiicontactlist.updateCList();
    }
    

})(jQuery); // jQuery
$.yiicontactlist.init();
