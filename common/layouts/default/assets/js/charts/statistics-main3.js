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
        
        var dd = [[1387497600000,0],[1387584000000,0],[1387670400000,0],[1387756800000,0],[1387843200000,0],[1387929600000,0],[1388016000000,0],[1388102400000,0],[1388188800000,0],[1388275200000,0],[1388361600000,0],[1388448000000, 0],[1388534400000,0],[1388620800000,0],[1388707200000,0],[1388793600000,0],[1388880000000,0],[1388966400000,0],[1389052800000,0],[1389139200000,0],[1389225600000,0],[1389312000000,0],[1389398400000,0],[1389484800000,0],[1389571200000,0],[1389657600000,2],[1389744000000,1],[1389830400000,1],[1389916800000,0],[1390003200000,0],[1390089600000,1],[1390176000000,0],[1390262400000,0]];
        
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
    
    
    $.statistics.firstDayMonth = function(ticks){
        //console.log(ticks);
        var pointsObj = [];
        var r = [];
        
        var min, max;
        
        $.each(ticks, function(i,val){
            var d = new Date(val);
            var m = d.getMonth();
            var y = d.getFullYear();
            pointsObj.push({
                month: m+1,
                date: val
            });
        });
        
        var pointsGrouped = {};

        for (var i = 0; i < pointsObj.length; ++i) {
            var obj = pointsObj[i];

            if (pointsGrouped[obj.month] == undefined){
                pointsGrouped[obj.month] = [];
            }   
            pointsGrouped[obj.month].push(obj.date);
        }
        
        $.each(pointsGrouped, function(i,v){
            r.push(v[0]);
        });
        
        $.each(r, function(i, v){
            var previous=r[i==0?r.length-1:i-1];
            var current = new Date(r[i]);
            var next = new Date(r[i==r.length-1?0:i+1]);
            
            var day_difference = Math.abs(dateDiffInDays(current, next));
            
            if(day_difference <=4 && ticks.length >= 10){
                r.splice(i+1, 1);
            }
            
        });
        
        return r;
        
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
        var ticks = [];

        for (var i = 0; i < pointsObj.length; ++i) {
            var obj = pointsObj[i];

            if (pointsGrouped[obj.date] == undefined) 
                pointsGrouped[obj.date] = [];

            pointsGrouped[obj.date].push({
                x: obj.x, 
                y: obj.y
            });
        }
        
        $.each(pointsGrouped, function(i,v){
            //console.log(v[0].x);
            ticks.push(v[0].x);
            
        })
        
        return ticks;
        
    //return pointsGrouped;
        
        
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
        });
        
        var tMin = Math.min.apply(null, ticks),
        tMax = Math.max.apply(null, ticks);
        
        var months = $.statistics.firstDayMonth(ticks);
       
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
        {
            data:points,
            points:{
                show: false
            },
            lines: {
                show: false
            },
            color: "#69a55d",
            xaxis: 2
        
        },
        ],

        options_placeholder = {
            legend: {
                show: false
            },
            series: {
                shadowSize: 0
            },

            xaxis:[
            {
                mode:"time",
                minTickSize: [1, "day"],
                tickLength: 0,
                tickColor: '#ccc',
                timeformat: "%d"
            },
            {
                mode:"time",
                minTickSize: [1, "month"],
                tickSize: [1, "month"],
                ticks: months,
                tickLength: 5,
                timeformat: "%b"
            }  
            ],
            yaxis: {
                position:"left",
                color: "#fff",
                reserveSpace: true,
                min: 0, 
                autoscaleMargin: 0.1
            },
            grid: {
                //borderWidth: 20,
                borderColor: 'transparent',
                backgroundColor: "#eee",
                hoverable: true
            }
            
        };

        $.statistics.clearTooltips();

        plot = $.plot($('#c-placeholder'), data_placeholder, options_placeholder);
        
        
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
        
        }
        
        ],
        options_overview = {
            legend: {
                show: false
            },
            series: {
                shadowSize: 0
            },
            xaxis: 
            {
                ticks: months,
                tickLength: 7,
                mode: "time", 
                //minTickSize: [1, "month"],
                tickSize: [1, "month"]
            }
            ,
            yaxis: {
                ticks: [], 
                min: 0
            },
            selection: {
                mode: "x",
                navigate:true,
                typeSize: 'day',
                minSize: 7
            },
            grid: {
                borderWidth: 0,
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
            
            var x_from = ranges.xaxis.from;
            var x_to = ranges.xaxis.to;
            var new_ticks = [];
            var new_points = [];
            
            if ($.inArray(x_from, ticks) == -1) {
                x_from = closestDate(ticks, x_from);
            } else if ($.inArray(x_to, ticks) == -1) {
                x_to = closestDate(ticks, x_to);
            }
            
            $.each(points, function(j, v){
                var val = v[0];
                if(val >= x_from && val <= x_to){
                    new_ticks.push(val);
                    new_points.push([v[0], v[1]]);
                }
            });
            
            var new_months = $.statistics.firstDayMonth(new_ticks);
            
            var data_new_placeholder = [
            {
                label:"Visits",
                data:new_points,
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
            {
                data:new_points,
                points:{
                    show: false
                },
                lines: {
                    show: false
                },
                color: "#69a55d",
                xaxis: 2
            },
            ];

            // do the zooming
            $.plot($("#c-placeholder"), data_new_placeholder,
                $.extend(true, {}, options_placeholder, {
                    xaxes:[{
                        min: x_from, 
                        max: x_to
                    },
                    {
                        ticks: new_months
                    }]
                    
                }
                ));
           
            $('.flot-x2-axis .tickLabel').each(function(i, v){
                var pos = $(this).position();
                var w = $(this).outerWidth();
                $(this).css('left', pos.left + (w/2) + 'px');
            });
                    
            overview.setSelection(ranges, true);
            
        });
        
        
    
        $("#c-overview").bind("plotselected", function (event, ranges) {
            plot.setSelection(ranges);
            
        });
        
        $('.flot-x2-axis .tickLabel').each(function(i, v){
            var pos = $(this).position();
            var w = $(this).outerWidth();
            $(this).css('left', pos.left + (w/2) + 'px');
        });
        
       
        //$('.flot-x2-axis .tickLabel').css({'left':label_left_offset})
     
        $.statistics.bindEvents(plot, overview);
        
    }
    
    $.statistics.init = function(){
        $.statistics.updateStatistics();
    }
    

})(jQuery); // jQuery
$.statistics.init();