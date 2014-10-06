$(document).ready(function(){
   
// Check all checkboxes when the one in a table head is checked:

$('.check-all').click(
        function(){
            $(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
        }
    )

});
  
  
  