/***=== Custom functions ===***/

/*
 * returns the difference in days from 2 dates objects
 */
function dateDiffInDays(a, b) {
    var _MS_PER_DAY = 1000 * 60 * 60 * 24; // 1 days in miliseconds
  
    // Discard the time and time-zone information.
    var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
    var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}
        
/*
 * returns the closest date from an array of timestamps dates
 */
function closestDate(dates, testDate) {
    var bestDate = dates.length;
    var bestDiff = -(new Date(0,0,0)).valueOf();
    var currDiff = 0;
    var i;

    for(i = 0; i < dates.length; ++i){
        currDiff = Math.abs(dates[i] - testDate);
        if(currDiff < bestDiff){
            bestDate = i;
            bestDiff = currDiff;
        }   
    }
    return dates[bestDate];
}

/*
 * return dd/mm/yy format from string timestamp
 */
function dateFromUTC (string) {
    var date = new Date(string);
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    return day + '/' + month + '/' + date.getFullYear();
}

/*
 * returns the timestamp from date dd-mm-yy
 */
function dateTs(date) {
    date=date.split("-");
    var newDate=date[1]+","+date[0]+","+date[2];
    return new Date(newDate + " UTC").getTime();
}

/*
 * strip the html tags from text
 */
function strip(html) {
    var tmp = document.createElement("div");
    tmp.innerHTML = html;

    if (tmp.textContent == '' && typeof tmp.innerText == 'undefined') {
        return '0';
    }
    return tmp.textContent || tmp.innerText;
}
/***
 * function to check if the value is a number
 **/
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

/***
 * returns get params value
 ***/
function getParameter(paramName) {
    var searchString = window.location.search.substring(1),
    i, val, params = searchString.split("&");

    for (i=0;i<params.length;i++) {
        val = params[i].split("=");
        if (val[0] == paramName) {
            return decodeURIComponent(val[1]);
        }
    }
    return null;
};
/*** returns the param value from a given url ***/
function getUrlVar(key, url){
    var result = new RegExp(key + "=([^&]*)", "i").exec(url);
    return result && result[1] || "";
}


function confirmationDeleteGrid(question,gridId, urlT, dataT, titleT, yesBtn, noBtn) {
    var titleB = "Confirm";
    if (titleT != null) {
        titleB = titleT;
    }
    
    var yesB = "Yes";
    if (yesBtn != null) {
        yesB = yesBtn;
    }
    var noB = "No";
    if (noBtn != null) {
        noB = noBtn;
    }
    
    var buttonOpts = {};
    buttonOpts[yesB] = function () {
        $(this).dialog("close");
        $.fn.yiiGridView.update(gridId, {
            type:'POST',
            url: urlT,
            data: dataT,
            success:function(data) {
                $.fn.yiiGridView.update(gridId);
            }
        });
    };
    buttonOpts[noB] = function () {
        $(this).dialog("close");
    };
    $('<div></div>')
    .html(question)
    .dialog({
        dialogClass: "confirm",
        draggable: true,
        modal: true,
        resizable: false,
        width: 300,
        autoOpen: true,
        title: titleB,
        buttons: buttonOpts,
        open: function(event, ui){
            $(this).parent().find('.ui-dialog-titlebar').append('<span class="icon"></span>');
        }
    }).attr({
        'class': 'confirm-alert'
    });
};



function errorAlert(txt,titleT,btn) {
    var titleB = "Alert";
    if (titleT != null) {
        titleB = titleT;
    }
    
    var okB = "Ok";
    if (btn != null) {
        okB = btn;
    }
   
    
    var buttonOpts = {};
    
    buttonOpts[okB] = function () {
        $(this).dialog("close");
    };
    $('<div></div>')
    .html(txt)
    .dialog({
        dialogClass: "error",
        draggable: true,
        modal: true,
        resizable: false,
        width: 300,
        autoOpen: true,
        title: titleB,
        buttons: buttonOpts,
        open: function(event, ui){
            $(this).parent().find('.ui-dialog-titlebar').append('<span class="icon"></span>');
        }
    }).attr({
        'class': 'error-alert'
    });
};


(function($) {
    
    // === select box ===//
    
//    $(".jq-selectbox").selectbox({
//        //effect: "slide",
//        speed: 20
//    });

    
    //=== fix for ajax yii-pager ===//
    
    $(".yiiPager li").each(function(i, v){
        if($(this).hasClass('hidden')){
            var el=  $(this).find('a');
            el.click(function(){
                return false;
            })
        }
    });
    
    
    //=== Notification Boxes ===//
    $(".notification-box").append( "<a class='notification-close'></a>");
    // we want to autoclose after 5 seconds;
    setTimeout(function(){
        $(".notification-box").fadeOut();
    }, 8000);
   
    $(".notification-close").click(function(){
        $(this).parent().fadeOut();
        return false;
    });
    
    //=== inFieldLabels ===//
    if($().inFieldLabels){
        $(".labelIn label").css({
            "display":"block"
        });  
        $(".labelIn label").inFieldLabels({
            fadeDuration:80
        });
    }
    
    //=== Tabs navigation ===//
    if($().tabs){
        $(".tabs").tabs();
    }
    
    //=== search form dynamic drop down ===//
    $.fn.textWidth = function(){
        var html_calc = $('<span>' + $(this).html() + '</span>');
        html_calc.css('font-size',$(this).css('font-size')).hide();
        html_calc.prependTo('body');
        var width = html_calc.width();
        html_calc.remove();
        return width;
    }
    
    $.fn.expandCatCategs = function(id,toate,bloc){
        var i;
        for(i=bloc;i<toate;i++){
            if($('#cat_'+id+'_'+i).css('display')=='block'){
                $('#cat_'+id+'_'+i).css({
                    'display':'none'
                });
            }
            else{
                $('#cat_'+id+'_'+i).css({
                    'display':'block'
                });
            }
        }
           
        if($('#cat_'+id+'_'+(toate-2)).css('display')=='block'){
            $('#all_'+id).html("Nascondi");
            $('#all_'+id).attr('class', 'collapse');
        } else {
            $('#all_'+id).html("Mostra tutte");
            $('#all_'+id).attr('class', 'expand');
        }
    }
    
    var w = $("#keyword").width();		   
    var current_w = $("#search-options :selected").textWidth();
    current_w = current_w + 70;
    var ws = w - current_w;
			
    $("#keyword").css({
        //'padding-left': current_w, 
        'width':ws
    });
    
    
    //=== ui toogle accordion panels ===//
    
    $(".toogle-panel").addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
    .find(".toggler")
    .addClass("ui-accordion-header ui-helper-reset ui-state-default")
    .hover(function() {
        $(this).toggleClass("ui-state-hover");
    }).prepend('<span class="ui-icon"></span>');
    
    
    $(".toggler").click(function(e) {
        e.stopPropagation(); 
        $.fn.tooglePanel(this);
        $.fn.deactivateToogle(this);
        return false;
    });
    
    // we need to close the panel if we click outsite the panel
    $(document).click(function(e) {
        if ( !$(e.target).hasClass('ui-accordion-content-active') && !$(e.target).is('.toggle-inner a')) {
            var toHide = $(".toggle-inner");
            var toggler = toHide.parent().find('.toggler');
            $.fn.closePanel(toggler, toHide);
        }
    });
    
    $(".toggle-inner > a.close").click(function(e) {
        e.stopPropagation();
        var toHide = $(this).parent();
        var togglePanel = $(this).parent().parent();
        var toggler = togglePanel.find('.toggler');
        //alert(toggler.attr('class'));
        $.fn.closePanel(toggler, toHide);
    //return false;
    });
    
    // deactivate panels function if we want to open another
    $.fn.deactivateToogle = function(current) {
        $(".toggler").each(function( index, elem ){
            if (elem != current && $(elem).hasClass("ui-state-active")) {
                $.fn.tooglePanel(elem);
            }
        });
    };
    
    // toogle panel function
    $.fn.tooglePanel = function(elem) {
        var txtStr = $(elem).attr("title");
        $(elem).find('.toggle-inner').hide();
        $(elem).toggleClass("ui-accordion-header-active ui-state-active ui-state-default")
        .next().toggleClass("ui-accordion-content-active");
        $(elem).siblings('.toggle-inner').slideToggle(0);
        if ($('.view', elem).html() === "Chiudi Panelo")
            $('.view', elem).html(txtStr);
        else
            $('.view', elem).html("Chiudi Panelo");
    };
    
    // close toogle panel function
    $.fn.closePanel = function(toggler, toHide) {
        var txtStr = toggler.attr("title");
        toHide.hide();
        toggler.removeClass("ui-accordion-header-active ui-state-active")
        .find("> .ui-icon").next().removeClass("ui-accordion-content-active");
        $('.view', toggler).html(txtStr);
        toHide.accordion({
            active: false
        });
    };
    
    $(".toggle-inner").accordion({
        collapsible: true,
        header: "a.region",
        active: false,
        animate: false,
        autoHeight: false
    });
    
    $("#select-location-panel ul.provinces a").click(function(){
        $.fn.setProvince();
        console.log($(this).attr('id'));
        return false;
        
    });
    
 
    //=== dropdown menus ===//
    
    /* User drop down menu */
    var timerH;
    $('.user-drop-down-trigger').hover(function(){
        clearTimeout(timerH);
        $("ul.user-menu a.user-drop-down-trigger").css({
            'background-position': 'right -15px'
        });
        $(".user-drop-down-pannel").show(0); 
    }, function(){
        timerH = setTimeout(function(){
            $("ul.user-menu a.user-drop-down-trigger").css({
                'background-position': 'right 5px'
            });
            
        },100);
        $(".user-drop-down-pannel").delay(100).hide(0);
        
    });
    
    $('.user-drop-down-pannel').hover(function(){
        clearTimeout(timerH);
        $(this).show(0);
        $("ul.user-menu a.user-drop-down-trigger").css({
            'background-position': 'right -15px'
        });
        
    }, function(){
        timerH = setTimeout(function(){
            $("ul.user-menu a.user-drop-down-trigger").css({
                'background-position': 'right 5px'
            });
            
        },100);
        $(this).delay(100).hide(0);
    });
    
    //=== ajax requests functions ===//
    
    /*** change location ***/
    $.fn.setProvince = function () {
        console.log('it works');
    };
    
})(jQuery); // jQuery



