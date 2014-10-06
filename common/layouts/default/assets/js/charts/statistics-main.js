(function($) {
    // track selected button
    $.statistics.ajaxerror=0;
    $.statistics.ajaxupdate=1;
    $.statistics.filters = {};
    $.statistics.clickTips;
    $.statistics.hoverTip;
    $.statistics.data = null;
    $.statistics.ticks = null;
    
    $.statistics.updateStatistics = function() {
        
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
            var $rows = $('#statistics-grid tbody').find('tr');
            
            if ($(this).is(':checked')) {
                $rows.addClass('selected');
                $checks.prop('checked', true);
                $checksAll.prop('checked', true);
                $checks.checkbox({
                    checked: true
                });
                $('#plotselected').removeClass('disabled');
            } else {
                $rows.removeClass('selected');
                $checks.prop('checked', false);
                $checksAll.prop('checked', false);
                $checks.checkbox({
                    checked: false
                });
                $('#plotselected').addClass('disabled');
            }
        });
        
        $(document).on('change', 'input.select-on-check', function(){
            
            var $checksAll = $('input.select-on-check-all');
            var $elem = $(this);
            var $row = $elem.closest('tr');
            var rowsIds = $.statistics.getSelectedRows();
            var rowsCount = $('#statistics-grid tbody tr').length;
            
            //console.log(rowsIds.length);
            
            if(rowsIds.length < rowsCount){
                $checksAll.prop('checked', false);
                $checksAll.checkbox({
                    checked: false
                }); 
            }
            
            if(rowsIds.length > 0){
                $('#plotselected').removeClass('disabled');
            } else {
                $('#plotselected').addClass('disabled');
            }
            
            if($elem.is(':checked')){
                $row.addClass('selected');
            } else {
                $row.removeClass('selected');
            }
            return false;
        });
        
        // pagination grid links
        $('#statistics-grid-pagination a').unbind('click').click(function(e){
            
            var $elem = $(this);
            
            var url = $(this).attr('href');
            url = jQuery.param.querystring(url, 'ajax=statistics-grid');
            
            
            if(!$elem.hasClass('hidden')){
                
                $.fn.yiiListView.update("statistics-grid", {
                    url: url,
                    complete: function(){
                        $.statistics.updatePagination(url);
                    }
                });
            }
            
            return false;
            
        });
        
        // grid button actions
        $('.btn-grid').unbind('click').click(function(){
            
            var rowsIds = $.statistics.getSelectedRows();
            
            var btn_id = $(this).attr('id');
            
            // build the url
            var url = $.statistics.controllerUrl+'dayvisits?ajax=1';
                        
            url = jQuery.param.querystring(url, 'ajax=1');
            
            
            if(rowsIds.length > 0){
                
                
                //console.log(rowsIds)
                $.statistics.ajaxUpdateChart($('#date_from').val(), $('#date_to').val(), true);
                
            //return $.productsManage.submitAjax(url);
            }
            
        });
        
        
        /***=== set UI Datepicker ===***/
        if($.statistics.ajaxupdate > 0) {
            
            $('#date_from').val($.statistics.getStartDate());
            $('#date_to').val($.statistics.getEndDate());
            
            $.statistics.ajaxUpdateChart($('#date_from').val(), $('#date_to').val());
        }
        
        $('.ds-btn').unbind('click').click(function(){
            
            var id = $(this).attr('id');
            
            $('#date_from').val($.statistics.getStartDate(id));
            $('#date_to').val($.statistics.getEndDate());
            
            $("#date_to").datepicker( "option", "minDate", $.statistics.getEndDateMin($.statistics.getStartDate(id)));
            $.statistics.ajaxUpdateGrid();
            $.statistics.ajaxupdate=0;
            $('.ds-btn').removeClass('active');
            $(this).addClass('active');
            return false;
        });
        
        
        /*** select metric options [visits or views] ***/
        $(document).on("change", "#m-choices input", function(e) {
            
            $("#m-choices input").checkbox({
                checked: false
            });
            
            $(this).checkbox({
                checked: true
            });
            
           $.statistics.plotSelected();
        })
        
        // search button
        $('#filter-submit').unbind('click').click(function(){
            $.statistics.ajaxUpdateGrid();
            $.statistics.ajaxupdate=0;
            return false;
        });
        
        
        
    }
    
    // this function update the pagination-pn of the grid
    $.statistics.updatePagination = function(url){
        
        var prevLink = $('#statistics-grid-pagination a.previous');
        var nextLink = $('#statistics-grid-pagination a.next');
        var total = $('#statistics-grid-pagination .total').text();
        var currentPage = getUrlVar("page", url);
        var rows_len =  $('#statistics-grid tbody tr').length;
        
        if(rows_len == 0){
            $('#statistics-grid-pagination').addClass('hidden');
        }
        
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
            
        $('#statistics-grid-pagination .curent-page').text(currentPage);
        
    }
    
    $.statistics.ajaxUpdateGrid = function() {
        var keyword = $('input#keyword').val();
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        
        if(date_from.length == 0 || date_to.length == 0) {
            date_from = $.statistics.getStartDate();
            $('#date_from').val($.statistics.getStartDate());
            date_to = $.statistics.getEndDate();
            $('#date_to').val($.statistics.getEndDate());
        }
        
        var l_data = {
            'date-from': date_from,
            'date-to': date_to,
            'q': keyword
        };      
        $.fn.yiiListView.update("statistics-grid", {
            data: l_data,
            complete: function(){
                $.statistics.ajaxUpdateChart($('#date_from').val(), $('#date_to').val());
                $('.date-info').text($.statistics.dateMonthString($('#date_from').val()) + ' - ' + $.statistics.dateMonthString($('#date_to').val()))
                
            }
        });
        $.statistics.ajaxupdate=0;
    }
    
    
    
    /*
     * calculate the minDate of the date_to for the UI datepicker
     * strDate = '03.09.2011'
     */
    $.statistics.getEndDateMin = function(strDate) {
        var dateParts = strDate.split("-");

        var date = new Date(dateParts[2], (dateParts[1]), dateParts[0]);
        date.setDate(date.getDate()+6);
        
        var day_from = ("0" + date.getDate()).slice(-2);
        var month_from = ("0" + (date.getMonth() + 1)).slice(-2);
        var result_date = day_from + '-' + (month_from) + '-' + date.getFullYear();
        return result_date;
    }
    
    /* 
     *  generate the default end date 
     */
    $.statistics.getEndDate = function (){
        var now = new Date();
        var day_to = ("0" + now.getDate()).slice(-2);
        var month_to = ("0" + (now.getMonth() + 1)).slice(-2);
        var dateTo = day_to + '-' + (month_to) + '-' + now.getFullYear();
        return dateTo;
    }
    
    $.statistics.dateMonthString = function(date){
        var months = jQuery.parseJSON($.statistics.monthNamesShort);
        date=date.split("-");
        var newDate=date[1]+","+date[0]+","+date[2];
        newDate = new Date(newDate);
        
        var day = newDate.getDate();
        var month = months[newDate.getMonth()];
        var date_res = day + ' ' + month + ' ' + newDate.getFullYear();
        return date_res;
    }
    
    
    /*
     * generate the default start date
     */
    $.statistics.getStartDate = function (type){
        var date, days_num;
        
        type = typeof type !== 'undefined' ? type : 'week';
        
        if(type=='month')
            days_num = 30
        else if(type=='quarter')
            days_num = 88
        else 
            days_num = 6
        
    
        date = new Date();
        
        date.setDate(date.getDate() - days_num); // 7days
        var day_from = ("0" + date.getDate()).slice(-2);
        var month_from = ("0" + (date.getMonth() + 1)).slice(-2);
        var dateFrom = day_from + '-' + (month_from) + '-' + date.getFullYear();
        return dateFrom;
    }
    
    $.statistics.ajaxUpdateChart = function (date_from, date_to, selected) {
        
        selected = typeof selected !== 'undefined' ? selected : false;
        
        var url = $.statistics.controllerUrl+'dayvisits?ajax=1';
        var rowsId;
        
        if(selected == true)
            rowsId = $.statistics.getSelectedRows();
        else 
            rowsId = ($.statistics.getRowsId().length>0) ? $.statistics.getRowsId() : 0;
        
        var data = {
            'ids[]': rowsId,
            'date-from': date_from,
            'date-to': date_to,
            'q': $('input#keyword').val(),
            //'q': $('input#keyword').val(),
            'YII_CSRF_TOKEN': $.statistics.csrf
        };
        
        console.log(data);
        
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            beforeSend : function (){
                $('.loading-wrapper').show();
                $('.loader-indicator').show();
            },
            success: function(response){
                if(response.success) {
                    
                    
                    var points = {};
                    
                    // views
                    var views = [];
                    var l_views = response.views.length;
                    //console.log(l);
                    
                    var q_views = (Math.floor(l_views/31));
                    
                    if(l_views > 89){
                        for (var i = 0; i < l_views - q_views; i += q_views) {
                            views.push(response.views[i]);
                        }
                    }
                    
                    else {
                        views = response.views;
                    }
                    
                    var visits = [];
                    var l_visits = response.visits.length;
                    //console.log(l);
                    
                    var q_visits = (Math.floor(l_visits/31));
                    
                    if(l_visits > 89){
                        for (var i = 0; i < l_visits - q_visits; i += q_visits) {
                            visits.push(response.visits[i]);
                        }
                    }
                    
                    else {
                        visits = response.visits;
                    }
                    
                    points.visits = visits;
                    points.views = views;
                    
                    
                    $.statistics.drawGraph(points);
                    $('.loading-wrapper').hide();
                    $('.loader-indicator').hide();
                        
                //console.log(response.result.length)
                    
                } else {
                    $('.loading-wrapper').hide();
                    $('.loader-indicator').hide();
                }
            },
            error:
            // submit form without ajax
            function(response){
                //return false;
                $.statistics.ajaxerror=1;
                return false;
            }
        }); 
    }
    
   
    //return an array of selected checkboxes
    $.statistics.getSelectedRows = function()
    {
        return $('#statistics-grid').find('input.select-on-check:checked').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    //return an array of selected checkboxes
    $.statistics.getRowsId = function()
    {
        return $('#statistics-grid').find('input.select-on-check').map(function(i,n) {
            return $(n).val();
        }).get(); //get converts it to an array
    }
    
    
    $.statistics.monthGroups = function (data) {
        
        var months = jQuery.parseJSON($.statistics.monthNamesShort);
        
        var pointsObj = [];
        $.each(data, function(j, v){
            var val = v[0];
            var d = new Date(val);
            var m = d.getMonth();
            var y = d.getFullYear();
            var mname = months[m];
            pointsObj.push({
                y: v[1], 
                x: v[0], 
                date: mname
            })
        });
        
        var pointsGrouped = {};

        for (var i = 0; i < pointsObj.length; ++i) {
            var obj = pointsObj[i];

            if (pointsGrouped[obj.date] == undefined) 
                pointsGrouped[obj.date] = [];

            pointsGrouped[obj.date].push({
                x: obj.x, 
                y: obj.y
            });
        }
        
        return pointsGrouped;
        
    }
    
    $.statistics.yearGroups = function (data) {
        
        var pointsObj = [];
        $.each(data, function(j, v){
            var val = v[0];
            var d = new Date(val);
            var m = d.getMonth();
            var y = d.getFullYear();
            pointsObj.push({
                y: v[1], 
                x: v[0], 
                date: y
            })
        });
        
        var pointsGrouped = {};

        for (var i = 0; i < pointsObj.length; ++i) {
            var obj = pointsObj[i];

            if (pointsGrouped[obj.date] == undefined) 
                pointsGrouped[obj.date] = [];

            pointsGrouped[obj.date].push({
                x: obj.x, 
                y: obj.y
            });
        }
        
        return pointsGrouped;
        
    }
    
    /** 
     * tooltip function
     */
    $.statistics.toolTipHTML = function(date, counter, series ) {
        var html = '';
        html += '<div class="chart-tooltip">';
        if (series)
            html += '<div class="res">';
        html += '<span>' + series + '</span> ';
        html += '<span class="counter">' + counter + '</span>';
        html += '</div>';
        html += '<div class="date">';
        html += '<span>' + date + '</span>';
        html += '</div>';
        html += '</div>';
        return html;
    }
    
    /*
     * clear Tooltips
     **/
    $.statistics.clearTooltips = function() {

        if ( $.statistics.hoverTip )
            $.statistics.hoverTip.remove();

        if ( $.statistics.clickTips )
            $.each( $.statistics.clickTips, function( i, t) {
                t.remove();
            });

        $.statistics.hoverTip = null;
        $.statistics.clickTips = null;
    }
    
    /**
     * bind events function [ for now just the hover ]
     **/
    $.statistics.bindEvents = function ( plot ) {

        $('.chart-placeholder').on('plothover', function ( event, pos, item ) {

            var ofs = {
                height: 0, 
                width: 0
            },
            fmtd,
            date;

            if ($.statistics.clickTips ) return;

            $.statistics.clearTooltips();

            if (item) {
                document.body.style.cursor = 'pointer';
                
                fmtd = item.series.data[item.dataIndex][1];
                
                date = dateFromUTC(item.series.data[item.dataIndex][0])

                $.statistics.hoverTip = $($.statistics.toolTipHTML( date, fmtd, item.series.label ));

                $('body').append($.statistics.hoverTip);

                ofs.height = $.statistics.hoverTip.outerHeight();
                ofs.width = $.statistics.hoverTip.outerWidth();

                $.statistics.hoverTip.offset({
                    left: item.pageX - ofs.width / 2, 
                    top: item.pageY - ofs.height - 15
                });

            }
            else {
                document.body.style.cursor = 'auto';
            }

        });
    }
    
    $.statistics.drawMonthLabels = function(plot, points) {
        
        var placeholder_id = plot.getPlaceholder().attr('id');
        
        $('#' + placeholder_id + ' .flot-x1-axis').after('<div class="flot-x-axis flot-x2-axis xAxis x2Axis"></div>');
        
        $('.flot-x2-axis').css({
            'position': 'absolute',
            'top': '0px', 
            'left': '0px', 
            'bottom': '0px', 
            'right': '0px', 
            'display': 'block'
        });
                    
        var monthsGroup = $.statistics.monthGroups(points);
        var yearsGroup = $.statistics.yearGroups(points);
        
        var len = $.map(monthsGroup, function(n, i) {
            return i;
        }).length;
        
        var pointsGroup = (len<=11) ? monthsGroup : yearsGroup;
            
        $.each(pointsGroup, function(key, v){
            var minPoint, posT, posL, pos, days_offset;
            
            minPoint = v[0];
           
            var offset = plot.pointOffset({
                x: minPoint.x, 
                y: minPoint.y
            });
            
            if(points.length>31)
                days_offset = 2;
            else
                days_offset = 0;
                
            var minLength = (points.length / 31) +  days_offset;
            posL = offset.left;
            posT = plot.height()+35;
            
            if(v.length >= minLength){
                $('<div class="flot-tick-label tickLabel">'+ key +'</div>').appendTo('.flot-x2-axis')
                .css({
                    'position':'absolute', 
                    'top':posT,
                    'left': posL
                });
            }
        });
        
        
        $('.flot-x2-axis .tickLabel').each(function(i, v){
            var pos, w;
            if(i % 2 == 0) {
                $(this).addClass('even');
            } else {
                $(this).addClass('odd');
            }
                
            pos = $(this).position();
            w = ($('#' + placeholder_id).width()  -  pos.left) - 14;
                
            $(this).width(w);
                
        });
        
    }
    
    
    /*
     *  draw graph chart function
     */
    $.statistics.drawGraph = function(result) {
        
        $.statistics.clearTooltips();
        
        var plot;
        
        //console.log(result);
        
        var t_max = dateTs($.statistics.getEndDate());
       
        
      
        $.statistics.data_placeholder = [
            {
            label:"Visits",
            data:result.visits,
            points:{
                show: true,
                radius: 3,
                lineWidth: 0,
                fill: true,
                fillColor: "#69a55d"
            },
            lines: {
                show:true,
                fill: true, 
                fillColor: "rgba(148, 231, 130, 0.6)",
                lineWidth:1
            },
            color: "#69a55d"
        },
        {
            label:"Views",
            data:result.views,
            points:{
                show: true,
                radius: 3,
                lineWidth: 0,
                fill: true,
                fillColor: "#95b58e"
            },
            lines: {
                show:true,
                fill: true, 
                fillColor: "rgba(173, 202, 166, 0.6)",
                lineWidth:1
            },
            color: "#95b58e"
        
        }
        
        ];

        $.statistics.options_placeholder = {
            legend: {
                show: false
            },
            series: {
                shadowSize: 0
            },
            xaxis:
            {
                mode:"time",
                minTickSize: [1, "day"],
                tickLength: 0,
                tickColor: '#ccc',
                timeformat: "%d"
            //max: t_max
            },
            yaxis: {
                position:"left",
                color: "#fff",
                min: 0, 
                autoscaleMargin: 0.1
            },
            grid: {
                borderWidth: 1,
                borderColor: 'transparent',
                backgroundColor: "#eee",
                hoverable: true
            }
            
        };
        
        var choiceContainer = $("#m-choices");
        
        $("#m-choices").empty();
        
        $.each($.statistics.data_placeholder, function(key, val) {
            choiceContainer.append("<span class='m-op' id='op-" + key + "'><input type='checkbox' name='" + key +
                "'></input>" +
                "<label for='id" + key + "'>"
                + val.label + "</label></span>");
            // we check the 1st checkbox by default
            if(key == 0){
                $('#op-'+key+' input').prop('checked', true);
            }
        });
        
        // checkbox bootstrap
        $("#m-choices input").checkbox({
            buttonStyle: 'btn-checkbox',
            checkedClass: 'icon-radio',
            uncheckedClass: 'icon-radio-empty',
            constructorCallback: null,
            defaultState: true,
            defaultEnabled: true,
            checked: false,
            enabled: true
        });

        $.statistics.plotSelected();
        
        // finally we bind the tooltip events
        $.statistics.bindEvents(plot);
    }
    
    
    $.statistics.plotSelected = function(){
        
        var data_sel = [];
        var container = $("#m-choices");
      

        container.find("input:checked").each(function () {
            var key = $(this).attr("name");
            if (key && $.statistics.data_placeholder[key]) {
                data_sel.push($.statistics.data_placeholder[key]);
            }
        });

        if (data_sel.length > 0) {
                
            var plot = $.plot($('#c-placeholder'), data_sel, $.statistics.options_placeholder);
                
            $.statistics.drawMonthLabels(plot, data_sel[0].data);
        }
        
    }
    
    
    $.statistics.init = function(){
        $.statistics.updateStatistics();
    }
    

})(jQuery); // jQuery
$.statistics.init();