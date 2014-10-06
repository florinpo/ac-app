/*
 * The MIT License

Copyright (c) 2010, 2011, 2012, 2013 by Juergen Marsch

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 */

(function ($) { 
    "use strict";
    var pluginName = "candlestick", pluginVersion = "0.3";
    var options = {
        series: { 
            candlestick: {
                active: false,
                show: false,
                rangeWidth: 3,
                rangeColor: "transparent",
                upColor: "rgb(255,0,0)",
                downColor: "#45783b",
                neutralColor: "rgb(255,255,255)",
                lineWidth: "8px",
                highlight: {
                    opacity: 0.1
                },
                drawCandlestick: drawCandlestickDefault
            }
        }
    };
    var replaceOptions = { 
        series:{ 
            lines: {
                show:false
            }
        },
        legend:{
            show:false
        }
    };
    function drawCandlestickDefault(ctx,serie,data,hover){
        
        var clickTips, hoverTip;
        var plot = $(".chart").data("plot");
        var offset = plot.pointOffset({
            x: data[0], 
            y: data[1]
        });
        
        
        var bla =1;
        
        
        if(hover === true){
            
            $.plot.isHover = true;
            bla = 2;
            
            var c = "rgba(0,0,0," + serie.candlestick.highlight.opacity + ")";
            drawHover(ctx,serie,data,c);
            drawBody(ctx,serie,data);
        }
        else {
            drawContainer(ctx,serie,data);
        }
        
        console.log(bla);
        
        
        function drawContainer(ctx,serie,data){
            var x,y1,y2;
            x = serie.xaxis.p2c(data[0]);
            y1 = serie.yaxis.p2c(serie.yaxis.min);
            y2 = serie.yaxis.p2c(serie.yaxis.max);
            ctx.lineWidth = serie.candlestick.rangeWidth;
            ctx.beginPath();
            ctx.strokeStyle = "transparent";
            ctx.moveTo(x,y1);
            ctx.lineTo(x,y2);
            ctx.stroke();
        }
        function drawBody(ctx,serie,data){
            //console.log(serie);
            var x,y,c, minY, lineWidth;
            x = serie.xaxis.p2c(data[0]);
            y = serie.yaxis.p2c(data[1]);
            minY =  serie.yaxis.p2c(serie.yaxis.min);
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.strokeStyle = "#94b48d";
            ctx.moveTo(x,minY);
            ctx.lineTo(x,y);
            ctx.stroke();     
            
            ctx.beginPath();
            ctx.strokeStyle = serie.candlestick.downColor;
            ctx.lineWidth = 3;
            ctx.arc(x, y, 4, 0, 2 * Math.PI, false);
            ctx.fillStyle = serie.points.fillColor;
            ctx.fill();
            ctx.stroke();
            
            ctx.beginPath();
            ctx.strokeStyle = serie.candlestick.downColor;
            ctx.lineWidth = serie.candlestick.barWidth;
            ctx.moveTo(x, minY);
            ctx.lineTo(x, minY+2);
            ctx.stroke();
        }
        function drawHover(ctx,serie,data,c){
            var x, y, y1, y2, index;
            x = serie.xaxis.p2c(data[0] - serie.candlestick.barWidth / 2);
            y = serie.yaxis.p2c(data[1]);
            y1 = serie.yaxis.p2c(serie.yaxis.min);
            y2 = serie.yaxis.p2c(serie.yaxis.max);
            ctx.beginPath();
            ctx.strokeStyle = c;
            ctx.lineWidth = serie.candlestick.barWidth;
            ctx.moveTo(x,y1);
            ctx.lineTo(x,y2);
            ctx.stroke();
            
        //            var item = [data[0], data[1]];
        //            $.each(serie.data, function(i,element) {
        //                if(JSON.stringify(item)==JSON.stringify(element)) {
        //                    previousPoint = i;
        //                    itemData = item;
        //                } 
        //            });
            
            
        }
    }
    function init(plot) {
        var offset = null, opt = null, series = null;
        plot.hooks.processOptions.push(processOptions);
        function processOptions(plot,options){
            if (options.series.candlestick.active){
                $.extend(true,options,replaceOptions);
                opt = options;
                plot.hooks.processRawData.push(processRawData);
                plot.hooks.drawSeries.push(drawSeries);
            }
        }
        function processRawData(plot,s,data,datapoints){
            if(s.candlestick.show === true){
                s.nearBy.findItem = findNearbyItemCandlestick;
                s.nearBy.drawHover = drawHoverCandlestick;
            }
        }
        function drawSeries(plot, ctx, serie){
            var data;
            if(serie.candlestick.show === true){        
                if(typeof(serie.candlestick.lineWidth) === 'string'){
                    serie.candlestick.barWidth = parseInt(serie.candlestick.lineWidth,0);
                    serie.nearBy.width = serie.candlestick.barWidth;
                }
                else { 
                    var dp = serie.xaxis.p2c(serie.xaxis.min + serie.candlestick.lineWidth) - serie.xaxis.p2c(serie.xaxis.min);
                    serie.candlestick.barWidth = dp;
                    serie.nearBy.width = serie.candlestick.lineWidth;
                }
                offset = plot.getPlotOffset();   
                ctx.save();
                ctx.translate(offset.left,offset.top);
                for(var i = 0; i < serie.data.length; i++){
                    data = serie.data[i];
                    if(data[0] < serie.xaxis.min || data[0] > serie.xaxis.max) continue;
                    serie.candlestick.drawCandlestick(ctx,serie,data,false);
                }
                ctx.restore();
            }
        }
        function findNearbyItemCandlestick(mouseX, mouseY,i,serie){
            var item = null;
            
            if(serie.candlestick.show===true){
                if(opt.series.justEditing){
                    if(opt.series.justEditing[1].seriesIndex === i){
                        item = findNearbyItemEdit(mouseX,mouseY,i,serie);
                    }
                }
                else { 
                    if(opt.grid.editable){
                        item = findNearbyItemForEdit(mouseX,mouseY,i,serie);
                    }
                    else{
                        item = findNearbyItem(mouseX,mouseY,i,serie);
                       
                    }        
                }
            }
            return item;
            
            function findNearbyItemEdit(mouseX,mouseY,i,serie){
                var item = null;
                var j = opt.series.justEditing[1].dataIndex;
                if(j.length){
                    item = [i,j];
                }else{
                    item = [i,j];
                }
                return item;
            }
            function findNearbyItemForEdit(mouseX,mouseY,i,serie){
                var item = null;
                if(serie.candlestick.show === true){
                    for(var j = 0; j < serie.data.length; j++){
                        var x,y,dataitem;
                        dataitem = serie.data[j];
                        x = serie.xaxis.p2c(dataitem[0]) - serie.candlestick.barWidth / 2;
                        y = serie.yaxis.p2c(dataitem[1]) - serie.candlestick.rangeWidth / 2;
                       
                        if(between(mouseX,x,(x+serie.candlestick.barWidth))){
                            
                            if(between(mouseY,y,(y + serie.candlestick.rangeWidth))) {
                                item = [i,[j,1]];
                                serie.editMode='y';
                                serie.nearBy.findMode = 'circle';
                            }
                        }
                    }
                }
                return item;
            }
            function findNearbyItem(mouseX,mouseY,i,serie){
                var item = null;
                for(var j = 0; j < serie.data.length; j++){
                    var x,y1,y2,dataitem;
                    dataitem = serie.data[j];
                    x = serie.xaxis.p2c(dataitem[0]) - serie.candlestick.barWidth / 2;
                    y1 = serie.yaxis.p2c(serie.yaxis.min);
                    y2 = serie.yaxis.p2c(serie.yaxis.max);
                    if(between(mouseX,x,(x + serie.candlestick.barWidth))){
                        if(between(mouseY,y1,y2)){
                            item = [i,j];
                        } 
                    }
                }
                return item;
            }      
        }
        function drawHoverCandlestick(octx,serie,dataIndex){
            var data;
            octx.save();
            if(dataIndex.length){
                data = serie.data[dataIndex[0]];
            } else{
                data = serie.data[dataIndex];
            }
            serie.candlestick.drawCandlestick(octx,serie,data,true);
            octx.restore();      
        }
    }
    function createCandlestick(data){
        var min = [], max = [];
        for(var i = 0; i < data.data.length; i++){
            min.push([data.data[i][0],data.data[i][1]]);
            max.push([data.data[i][0],data.data[i][1]]);
        }
        var r = [ data,
        {
            label: "Max", 
            data: max, 
            lines:{
                show: false
            }, 
            candlestick:{
                show: false
            }, 
            nearBy: {
                findItem:null
            }
        },

        {
            label: "Min", 
            data: min, 
            lines:{
                show: false
            }, 
            candlestick:{
                show: false
            }, 
            nearBy: {
                findItem:null
            }
        }
        ];
        return r;
    }
    var between = $.plot.JUMlib.library.between;
    //$.plot.isHover = false;
    $.plot.candlestick = {};
    $.plot.candlestick.createCandlestick = createCandlestick;
    $.plot.plugins.push({
        init: init,
        options: options,
        name: pluginName,
        version: pluginVersion
    });
})(jQuery);