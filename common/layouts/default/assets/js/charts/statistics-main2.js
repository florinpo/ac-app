(function($) {
    // track selected button
    $.statistics.ajaxerror=0;
    $.statistics.filters = {};
    $.statistics.clickTips;
    $.statistics.hoverTip;
    
    
    
    $.statistics.updateStatistics = function() {
        
        
        /***=== UI Datepicker ===***/
        
        // initial values;
        $('#date_from').val($.statistics.getStartDate());
        $('#date_to').val($.statistics.getEndDate());
        
        //$.statistics.ajaxTotalVisits($('#date_from').val(), $('#date_to').val());
        
        var dd = [[1388534400000,0],[1388620800000,0],[1388707200000,0],[1388793600000,0],[1388880000000,0],[1388966400000,0],[1389052800000,0],[1389139200000,0],[1389225600000,0],[1389312000000,0],[1389398400000,0],[1389484800000,0],[1389571200000,0],[1389657600000,2],[1389744000000,1],[1389830400000,1],[1389916800000,0],[1390003200000,0],[1390089600000,1],[1390176000000,0],[1390262400000,0]];
        
        $.statistics.drawGraph(dd);
       
    }
    
    /*
     * calculate the minDate of the date_to for the UI datepicker
     * strDate = '03.09.2011'
     */
    $.statistics.getEndDateMin = function(strDate) {
        var dateParts = strDate.split("-");

        var date = new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);
        date.setDate(date.getDate()+7);
        
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
    
    /*
     * generate the default start date
     */
    $.statistics.getStartDate = function (){
        var now = new Date();
        now.setDate(now.getDate() - 7); // 7days
        var day_from = ("0" + now.getDate()).slice(-2);
        var month_from = ("0" + (now.getMonth() + 1)).slice(-2);
        var dateFrom = day_from + '-' + (month_from) + '-' + now.getFullYear();
        return dateFrom;
    }
    
    $.statistics.ajaxTotalVisits = function (date_from, date_to) {
        
        var url = $.statistics.controllerUrl+'dayvisits?ajax=1';
        
        var data = {
            'date-from': date_from,
            'date-to': date_to,
            'YII_CSRF_TOKEN': $.statistics.csrf
        };
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
                    $.statistics.drawGraph(response.result);
                    $('.loading-wrapper').hide();
                    $('.loader-indicator').hide();
                } else {
                    $('.loading-wrapper').hide();
                    $('.loader-indicator').hide();
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
          
    }
    
    
    /**
     * var data - an array of points ex: [[x1, y1], [x2, y2]]
     * return an object of axis points grouped by months
     */
    
    $.statistics.monthGroups = function (data) {
        
        var months = jQuery.parseJSON($.statistics.monthNames);
        
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
                date: mname + ' / ' + y
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

            } else {
                document.body.style.cursor = 'auto';
            }

        });
        
       
    }
    
    
    /*
     *  draw graph chart function
     */
    $.statistics.drawGraph = function(points) {
        
        var plot;
        var ticks = [];
        
        $.each(points, function(j, v){
            var val = v[0];
            ticks.push(val);
        })
        
        var tMin = Math.min.apply(null, ticks) - (12*60*60*1000),
        tMax = Math.max.apply(null, ticks) + (12*60*60*1000);
        
        var data_placeholder = [
        {
            label:"Visits",
            data:points,
            points:{
                show: true,
                radius: 3,
                lineWidth: 2,
                fill: true,
                fillColor: "#ffffff"
            },
            lines: {
                show:true,
                fill: true, 
                fillColor: "rgba(148, 231, 130, 0.5)",
                lineWidth:1
            },
            color: "#69a55d"
        
        },
        ],

        options_placeholder = {
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
                min: tMin,
                max: tMax,
                ticks: ticks,
                tickLength: 7,
                tickColor: '#ccc',
                timeformat: "%d"
            },
            yaxis: {
                position:"left",
                color: "#fff",
                reserveSpace: true,
                min: 0, 
                autoscaleMargin: 0.1
            },
            grid: {
                borderWidth: 20,
                borderColor: 'transparent',
                backgroundColor: "#eee",
                hoverable: true
            //clickable: true
            }
            
        };

        $.statistics.clearTooltips();

        plot = $.plot($('#c-placeholder'), data_placeholder, options_placeholder);
        
        
        $('.flot-x1-axis').after('<div class="flot-x-axis flot-x2-axis xAxis x2Axis"></div>');
        
        $('.flot-x2-axis').css({
            'min-height': '20px', 
            'position': 'absolute', 
            'top': '0px', 
            'left': '0px', 
            'bottom': '0px', 
            'right': '0px', 
            'display': 'block'
        });

        var groupedPoints = $.statistics.monthGroups(points);
        
        //console.log(groupedPoints);
        
        $.each(groupedPoints, function(key, v){
            
            var minPoint = v[0];
            var offset = plot.pointOffset({
                x: minPoint.x - (12*60*60*1000), 
                y: minPoint.y
            });
            
            var leftL = offset.left;
            var topL = plot.height() + $('.flot-x1-axis .tickLabel').height();
            
            $('<div class="flot-tick-label tickLabel">'+ key +'</div>').appendTo('.flot-x2-axis')
            .css({
                'position':'absolute', 
                'top':topL, 
                'left':leftL
            });
            
        });
        
        
        var data_overview = [
        {
            label:"Visits",
            data:points,
            points:{
                show: false
            },
            lines: {
                show:true,
                fill: true, 
                fillColor: "rgba(148, 231, 130, 0.5)",
                lineWidth:0
            },
            color: "#69a55d"
        
        },
        ],
        options_overview = {
            legend: {
                show: false
            },
            series: {
                shadowSize: 0
            },

            xaxis: {
                ticks: [], 
                mode: "time", 
                minTickSize: [1, "day"]
            },
            yaxis: {
                ticks: [], 
                min: 0, 
                autoscaleMargin: 0.1
            },
            selection: {
                mode: "x",
                navigate:true,
                typeSize: 'day',
                minSize: 7
            },
            grid: {
                borderWidth: 1,
                borderColor: '#333',
                backgroundColor: "#eee"
            }
        };
        
        var overview = $.plot($("#c-overview"), data_overview, options_overview);
       
        overview.setSelection({
            xaxis: {
                from: 1389398400000, 
                to: 1390262400000
            }
        });
        
    // connect the overview with placeholder
    $("#c-placeholder").bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#c-placeholder"), data_placeholder,
            $.extend(true, {}, options_placeholder, {
                xaxis: {
                    min: ranges.xaxis.from, 
                    max: ranges.xaxis.to
                }
            }));
         
        plot.setupGrid();
        plot.draw();
        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    
    $("#c-overview").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
       
    });
    
     
     $.statistics.bindEvents(plot, overview);
     
}
    
$.statistics.init = function(){
    $.statistics.updateStatistics();
}
    

})(jQuery); // jQuery
$.statistics.init();