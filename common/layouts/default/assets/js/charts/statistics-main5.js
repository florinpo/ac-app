(function($) {
    // track selected button
    $.statistics.ajaxerror=0;
    $.statistics.filters = {};
    $.statistics.clickTips;
    $.statistics.hoverTip;
    $.statistics.data = null;
    $.statistics.ticks = null;
    $.statistics.from = null;
    $.statistics.to = null;
    
    
    $.statistics.refresh = false;
    
    
    
    $.statistics.updateStatistics = function() {
        
        
        console.log($.statistics.data);
        
        
        /***=== UI Datepicker ===***/
        
        //console.log($.statistics.dateFrom);
        
        // initial values;
        $('#date_from').val($.statistics.getStartDate());
        $('#date_to').val($.statistics.getEndDate());
        
        //$.statistics.ajaxTotalVisits($.statistics.dateFrom, $('#date_to').val());
        $.statistics.data_t = [[1383264000000,0],[1383350400000,0],[1383436800000,0],[1383523200000,0],[1383609600000,0],[1383696000000,0],[1383782400000,0],[1383868800000,0],[1383955200000,0],[1384041600000,0],[1384128000000,0],[1384214400000,0],[1384300800000,0],[1384387200000,0],[1384473600000,0],[1384560000000,0],[1384646400000,0],[1384732800000,0],[1384819200000,0],[1384905600000,0],[1384992000000,0],[1385078400000,0],[1385164800000,0],[1385251200000,0],[1385337600000,0],[1385424000000,0],[1385510400000,0],[1385596800000,0],[1385683200000,0],[1385769600000,0],[1385856000000,0],[1385942400000,0],[1386028800000,0],[1386115200000,0],[1386201600000,0],[1386288000000,0],[1386374400000,0],[1386460800000,0],[1386547200000,0],[1386633600000,0],[1386720000000,0],[1386806400000,0],[1386892800000,0],[1386979200000,0],[1387065600000,0],[1387152000000,0],[1387238400000,0],[1387324800000,0],[1387411200000,0],[1387497600000,0],[1387584000000,0],[1387670400000,0],[1387756800000,0],[1387843200000,0],[1387929600000,0],[1388016000000,0],[1388102400000,0],[1388188800000,0],[1388275200000,0],[1388361600000,0],[1388448000000,0],[1388534400000,0],[1388620800000,0],[1388707200000,0],[1388793600000,0],[1388880000000,0],[1388966400000,0],[1389052800000,0],[1389139200000,0],[1389225600000,0],[1389312000000,0],[1389398400000,0],[1389484800000,0],[1389571200000,0],[1389657600000,2],[1389744000000,1],[1389830400000,1],[1389916800000,0],[1390003200000,0],[1390089600000,1],[1390176000000,0],[1390262400000,0],[1390348800000,0],[1390435200000,0],[1390521600000,0],[1390608000000,0],[1390694400000,0],[1390780800000,0],[1390867200000,0]];
        
     
        //console.log(whole_data.length);
        var dd = [[1386979200000,0],[1387065600000,0],[1387152000000,0],[1387238400000,0],[1387324800000,0],[1387411200000,0],[1387497600000,0],[1387584000000,0],[1387670400000,0],[1387756800000,0],[1387843200000,0],[1387929600000,0],[1388016000000,0],[1388102400000,0],[1388188800000,0],[1388275200000,0],[1388361600000,0],[1388448000000,0],[1388534400000,0],[1388620800000,0],[1388707200000,0],[1388793600000,0],[1388880000000,0],[1388966400000,0],[1389052800000,0],[1389139200000,0],[1389225600000,0],[1389312000000,0],[1389398400000,0],[1389484800000,0],[1389571200000,0],[1389657600000,2],[1389744000000,1],[1389830400000,1],[1389916800000,0],[1390003200000,0],[1390089600000,1],[1390176000000,0],[1390262400000,0],[1390348800000,0],[1390435200000,0],[1390521600000,0],[1390608000000,0],[1390694400000,0],[1390780800000,0],[1390867200000,0]];
            
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
    
    /*
     *  // generate the 1st day of the month for ticks by month
     *  min the difference in days bettween months
     */
    $.statistics.firstDayMonth = function(ticks, min){
        
        min = typeof min !== 'undefined' ? min : 0;
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
        
        // we sort the new array
        r.sort(function(x, y){
            return y-x;
        });

        if(min>0){
            for(i=0; i<r.length-1; i++){
                var diff = Math.abs(dateDiffInDays(new Date(r[i+1]), new Date(r[i])));
                //console.log(diff);
                if(diff <= min) {
                    r.splice(i+1, 1);
                }
            }
        }
        
        return r;
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
    
    
    /*
     *  draw graph chart function
     */
    $.statistics.drawGraph = function(points) {
        
        $.statistics.data = points;
        
        var plot;
        var ticks = [];
        
        $.each(points, function(j, v){
            var val = v[0];
            ticks.push(val);
        });
        
        var tMin = Math.min.apply(null, ticks),
        tMax = Math.max.apply(null, ticks);
        
        var tMin = Math.min.apply(null, ticks),
        tMax = Math.max.apply(null, ticks);
       
        var data_placeholder = [
        {
            label:"Visits",
            data:points,
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
        ];

        var options_placeholder = {
            legend: {
                show: false
            },
            series: {
                shadowSize: 0
            },

            xaxes:[
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
                monthNames: jQuery.parseJSON($.statistics.monthNamesShort),
                ticks: $.statistics.firstDayMonth(ticks, 3),
                tickLength: 5,
                timeformat: "%b",
                tickWidth: 30
            //color:"#eee"
            }  
            ],
            yaxis: {
                position:"left",
                color: "#fff",
                min: 0, 
                autoscaleMargin: 0.1
            },
            grid: {
                borderWidth: 10,
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
                ticks: $.statistics.firstDayMonth(ticks,10),
                tickLength: 7,
                mode: "time", 
                minTickSize: [1, "month"],
                tickSize: [1, "month"],
                panRange: [1386979200000, 1390867200000]
            }
            ,
            yaxis: {
                ticks: [],
                panRange: false,
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
            },
            pan: {
                interactive: true
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
            
            //$.statistics.refresh = false;
           
            var x_from = ranges.xaxis.from;
            var x_to = ranges.xaxis.to;
            var new_ticks = [];
            var new_points = [];

           
           if ($.inArray(x_from, $.statistics.ticks) == -1) {
                x_from = closestDate($.statistics.ticks, x_from);
            } 
           else if ($.inArray(x_to, $.statistics.ticks) == -1) {
                x_to = closestDate(ticks, x_to);
            }
            
            $.each($.statistics.data, function(j, v){
                var val = v[0];
                if(val >= x_from && val <= x_to){
                    new_ticks.push(val);
                    new_points.push([v[0], v[1]]);
                }
            });
            
                
            //x_to = $.statistics.to;
            data_placeholder = [
            {
                label:"Visits",
                data:new_points,
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
            $.plot($("#c-placeholder"), data_placeholder,
                $.extend(true, {}, options_placeholder, {
                    xaxes:[{
                        min: x_from, 
                        max: x_to
                    }
                    ]
                }
                ));
                    
                    
            overview.setSelection(ranges, true);
             
            
        });
    
        $("#c-overview").bind("plotselected", function (event, ranges) {
            plot.setSelection(ranges);
            
            $.statistics.to = ranges.xaxis.to;
            
             // set the  pan range
            var axes = overview.getAxes();
            var min = axes.xaxis.min;
            var max = axes.xaxis.max;
            
            //overview.getOptions().pan.interactive = false;
            
            if(ranges.xaxis.from <=min || ranges.xaxis.from >=max){
                //overview.getOptions().pan.interactive = true;
                overview.getOptions().xaxis.panRange = [1383264000000, 1390867200000];
                overview.getOptions().xaxes[0].panRange = [1383264000000, 1390867200000];
                //$.statistics.refresh = true;
            }
        });
        
        
        $("#c-overview").bind("plotpan", function (event, pl) {
            
            var axes = pl.getAxes();          
            var min = axes.xaxis.min.toFixed(2);
            var max = axes.xaxis.max.toFixed(2);
            var from, to;
            
            var data = $.statistics.data_t;
            
            //console.log(min);
            
            var t = [];
            var diff = [];
            var diff_ticks = [];
            $.each(data, function(i, v){
                t.push(v[0]);
            });
            
            if ($.inArray(min, t) == -1) {
                from = closestDate(t, min);
            }
            if ($.inArray(max, t) == -1) {
                to = closestDate(t, max);
            }
            
            $.each(data, function(i, v){
                if(data[i][0] >= from && data[i][0] <= to) {
                    diff.push([data[i][0], data[i][1]]);
                    diff_ticks.push(data[i][0]);
                }
            });
            
            pl.setData([{
                data:diff,
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
            }]);
        
            $.statistics.data = diff;
            $.statistics.ticks = diff_ticks;
            $.statistics.from = from;
            
        
        });
        
        
     
       
        
    }
    
    $.statistics.init = function(){
        $.statistics.updateStatistics();
    }
    

})(jQuery); // jQuery
$.statistics.init();