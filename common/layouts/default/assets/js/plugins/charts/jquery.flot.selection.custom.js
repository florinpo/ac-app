/*
Flot plugin for selecting regions.

The plugin defines the following options:

  selection: {
    mode: null or "x" or "y" or "xy",
    color: color
    navigate: bool [default=false]
  }

Selection support is enabled by setting the mode to one of "x", "y" or
"xy". In "x" mode, the user will only be able to specify the x range,
similarly for "y" mode. For "xy", the selection becomes a rectangle
where both ranges can be specified. "color" is color of the selection
(if you need to change the color later on, you can get to it with
plot.getOptions().selection.color).

When selection support is enabled, a "plotselected" event will be
emitted on the DOM element you passed into the plot function. The
event handler gets a parameter with the ranges selected on the axes,
like this:

  placeholder.bind("plotselected", function(event, ranges) {
    alert("You selected " + ranges.xaxis.from + " to " + ranges.xaxis.to)
    // similar for yaxis - with multiple axes, the extra ones are in
    // x2axis, x3axis, ...
  });

The "plotselected" event is only fired when the user has finished
making the selection. A "plotselecting" event is fired during the
process with the same parameters as the "plotselected" event, in case
you want to know what's happening while it's happening,

A "plotunselected" event with no arguments is emitted when the user
clicks the mouse to remove the selection.

Navigation support is enabled by setting navigate to true. In
navigation mode the user will be able to both make a selection
and to move a current selection by clicking inside it and dragging.
When navigation support is enabled the "plotnavigated" and
"plotnavigating" events can be expected.

The "plotnavigated" event is only fired when the user has finished
moving/navigating the selection. A "plotnavigating" event is fired
during the process of moving/navigating a selection. The same
arguments are used for the "plotnavigated" and "plotnavigating"
events as in "plotselected".

The plugin also adds the following methods to the plot object:

- setSelection(ranges, preventEvent)

  Set the selection rectangle. The passed in ranges is on the same
  form as returned in the "plotselected" event. If the selection mode
  is "x", you should put in either an xaxis range, if the mode is "y"
  you need to put in an yaxis range and both xaxis and yaxis if the
  selection mode is "xy", like this:

    setSelection({ xaxis: { from: 0, to: 10 }, yaxis: { from: 40, to: 60 } });

  setSelection will trigger the "plotselected" event when called. If
  you don't want that to happen, e.g. if you're inside a
  "plotselected" handler, pass true as the second parameter. If you
  are using multiple axes, you can specify the ranges on any of those,
  e.g. as x2axis/x3axis/... instead of xaxis, the plugin picks the
  first one it sees.
  
- clearSelection(preventEvent)

  Clear the selection rectangle. Pass in true to avoid getting a
  "plotunselected" event.

- getSelection()

  Returns the current selection in the same format as the
  "plotselected" event. If there's currently no selection, the
  function returns null.

*/

(function ($) {
    function init(plot) {
        var selection = {
            first: {
                x: -1, 
                y: -1
            }, 
            second: {
                x: -1, 
                y: -1
            },
            show: false,
            active: false,
            xActive: true,
            yActive: true,
            navigate: false,
            to: null,
            from: null
        };

        // FIXME: The drag handling implemented here should be
        // abstracted out, there's some similar code from a library in
        // the navigation plugin, this should be massaged a bit to fit
        // the Flot cases here better and reused. Doing this would
        // make this plugin much slimmer.
        var savedhandlers = {};

        var mouseUpHandler = null;

        function onMouseMove(e) {

            if (selection.active) {
                plot.getPlaceholder().trigger("plotselecting", [ getSelection() ]);
                if (selection.xActive || selection.yActive) {
                    updateSelection(e);
                } else if (plot.getOptions().selection.navigate) {
                    moveSelection(e);
                }
            } else {
                updateCursor(e);
            }
        }

        function onMouseDown(e) {
            if (e.which != 1)  // only accept left-click
                return;

            // cancel out any text selections
            document.body.focus();

            // prevent text selection and drag in old-school browsers
            if (document.onselectstart !== undefined && savedhandlers.onselectstart == null) {
                savedhandlers.onselectstart = document.onselectstart;
                document.onselectstart = function () {
                    return false;
                };
            }
            if (document.ondrag !== undefined && savedhandlers.ondrag == null) {
                savedhandlers.ondrag = document.ondrag;
                document.ondrag = function () {
                    return false;
                };
            }

            if(plot.getOptions().selection.navigate == true) {
                selection.navigate = true;
                resizeSelection(e);
            }
            else {
                // redraw
                selection.navigate = false;
                setSelectionPos(selection.first, e);
                setSelectionPos(selection.second, e);
            }

            selection.active = true;

            // this is a bit silly, but we have to use a closure to be
            // able to whack the same handler again
            mouseUpHandler = function (e) {
                onMouseUp(e);
            };

            $(document).one("mouseup", mouseUpHandler);
        }

        function onMouseUp(e) {
            mouseUpHandler = null;

            // revert drag stuff for old-school browsers
            if (document.onselectstart !== undefined)
                document.onselectstart = savedhandlers.onselectstart;
            if (document.ondrag !== undefined)
                document.ondrag = savedhandlers.ondrag;

            // no more dragging
            selection.active = false;
            //updateSelection(e);

            if (selectionIsSane()) {
                triggerSelectedEvent();
            }
            else {
                // this counts as a clear
                plot.getPlaceholder().trigger("plotunselected", []);
                plot.getPlaceholder().trigger("plotselecting", [null]);
            }
            
            selection.xActive = true;
            selection.yActive = true;

            // no more navigating
            selection.navigate = false;

            return false;
        }

        function getSelection() {
            if (!selectionIsSane())
                return null;

            var r = {}, c1 = selection.first, c2 = selection.second;
            $.each(plot.getAxes(), function (name, axis) {
                if (axis.used) {
                    var p1 = axis.c2p(c1[axis.direction]), p2 = axis.c2p(c2[axis.direction]);
                    r[name] = {
                        from: Math.min(p1, p2), 
                        to: Math.max(p1, p2)
                    };
                }
            });
            return r;
        }

        function triggerSelectedEvent() {
            var r = getSelection();

            plot.getPlaceholder().trigger("plotselected", [r]);

            // backwards-compat stuff, to be removed in future
            if (r.xaxis && r.yaxis)
                plot.getPlaceholder().trigger("selected", [{
                    x1: r.xaxis.from, 
                    y1: r.yaxis.from, 
                    x2: r.xaxis.to, 
                    y2: r.yaxis.to
                }]);
        }

        function clamp(min, value, max) {
            return value < min ? min : (value > max ? max : value);
        }

        function setSelectionPos(pos, e) {
            var o = plot.getOptions();
            var offset = plot.getPlaceholder().offset();
            var plotOffset = plot.getPlotOffset();
            pos.x = clamp(0, e.pageX - offset.left - plotOffset.left, plot.width());
            pos.y = clamp(0, e.pageY - offset.top - plotOffset.top, plot.height());

            if (o.selection.mode == "y")
                pos.x = pos == selection.first ? 0 : plot.width();

            if (o.selection.mode == "x")
                pos.y = pos == selection.first ? 0 : plot.height();
        }

        function moveSelection(e) {
            
            var o = plot.getOptions();
            var mode = o.selection.mode;
            var offset = plot.getPlaceholder().offset();
            var plotOffset = plot.getPlotOffset();
            
            var typeSize = plot.getOptions().selection.typeSize;
            
            var series = plot.getData();
            var points = series[0].data;
            
            var offsets = [];
            
            $.each(points, function(i, v){
                var pOffset =  plot.pointOffset({
                    x: v[0], 
                    y: v[1]
                });
                offsets.push(pOffset.left);
            });
            

            if (mode == "y" || mode == "xy") {
                var deltaOldY = selection.second.y - selection.first.y;
                var oldY = selection.first.y + Math.floor(deltaOldY / 2);
                var moveY = e.pageY - offset.top - plotOffset.top - oldY;

                selection.first.y += moveY;
                if (deltaOldY > 0) {
                    selection.first.y = clamp(0, selection.first.y, plot.height() - deltaOldY);
                } else {
                    selection.first.y = clamp(0 - deltaOldY, selection.first.y, plot.height());
                }
                selection.second.y = selection.first.y + deltaOldY;
            }

            if (mode == "x" || mode == "xy") {
                var deltaOldX = selection.second.x - selection.first.x;
                var oldX = selection.first.x + Math.floor(deltaOldX / 2);
                var moveX = e.pageX - offset.left - plotOffset.left - oldX;
                

                selection.first.x += moveX;
                
                if (deltaOldX > 0) {
                    selection.first.x = clamp(0, selection.first.x, plot.width() - deltaOldX);
                } else {
                    selection.first.x = clamp(0 - deltaOldX, selection.first.x, plot.width());
                }
                
//                if(typeSize == 'day') {
//                    if ($.inArray(selection.first.x, offsets) == -1) {
//                         // not the most elegant solution but it works for
//                        selection.first.x  = closestPosX(selection.first.x, offsets) - 7.2;
//                    }
//                }
                
                selection.second.x = selection.first.x + deltaOldX;
                
            }
            
            if(selectionIsSane()){
                selection.show = true;
                plot.triggerRedrawOverlay();
            }
           
        }
        
        function resizeSelection(e) {
			  
            var mode = plot.getOptions().selection.mode;
            var pos = {};
            var minSize = plot.getOptions().selection.minSize;
		  
            setSelectionPos(pos, e);
            selection.xActive = false;
            selection.yActive = false;
		  
            if (mode == "y" || mode == "xy") {
                if (Math.abs(pos.y - selection.first.y) <= 1) {
                    selection.first.y = selection.second.y;
                    selection.second.y = pos.y;
                    selection.active = true;
                    selection.yActive = true;
                } else if (Math.abs(pos.y - selection.second.y) <= 1) {
                    selection.active = true;
                    selection.yActive = true;
                } 
                else if (isInBetween(selection.first.y, pos.y, selection.second.y)) {
                    selection.active = true;
                }
            }
		  
            if (mode == "x" || mode == "xy") {
                if (Math.abs(pos.x - selection.first.x) <= 1) {
                    selection.first.x = selection.second.x;
                    selection.second.x = pos.x;
                    selection.active = true;
                    selection.xActive = true;
                } else if (Math.abs(pos.x - selection.second.x) <= 1) {
                    selection.active = true;
                    selection.xActive = true;
                } 
                else if (isInBetween(selection.first.x, pos.x, selection.second.x)) {
                    selection.active = true;
                }
            }
		  
            if (selection.active) {
                selection.show = true;
                plot.triggerRedrawOverlay();
            }
            $(document).one("mouseup", onMouseUp);
        }

        function updateSelection(pos) {
            
            var typeSize = plot.getOptions().selection.typeSize;
            var series = plot.getData();
            var points = series[0].data;
            var dates = [];
            
            $.each(points, function(i,v){
                dates.push(v[0]);
            })
            
            if (pos.pageX == null)
                return;

            selection.active = true;
            setSelectionPos(selection.second, pos);
            
            
            if (selectionIsSane()) {
                selection.show = true;
                plot.triggerRedrawOverlay();
                
                selection.from = getSelection().xaxis.from;
                selection.to = getSelection().xaxis.to;
            }
            else {
                
                clearSelection(false);
                selection.active = false;
                
                if(typeSize == 'day') {
                    if ($.inArray(selection.from, dates) == -1) {
                        selection.from = closestDate(dates, selection.from);
                    } else if ($.inArray(selection.to, dates) == -1) {
                        selection.to = closestDate(dates, selection.to);
                    }   
                }
                
                setSelection({
                    xaxis: {
                        from: selection.from - (24*60*60*1000), 
                        to: selection.to
                    }
                })
            } 
                   
        }

        function clearSelection(preventEvent) {
            if (selection.show) {
                selection.show = false;
                plot.triggerRedrawOverlay();
                if (!preventEvent)
                    plot.getPlaceholder().trigger("plotunselected", []);
            }
        }
        
        function isInBetween(first, value, second) {
            return (first < value && value < second) || (second < value && value < first);
        }

        // function taken from markings support in Flot
        function extractRange(ranges, coord) {
            var axis, from, to, key, axes = plot.getAxes();
            
            var typeSize = plot.getOptions().selection.typeSize;
            var series = plot.getData();
            var points = series[0].data;
            var dates = [];
        
            $.each(points, function(i,v){
                dates.push(v[0]);
            })

            for (var k in axes) {
                axis = axes[k];
                if (axis.direction == coord) {
                    key = coord + axis.n + "axis";
                    if (!ranges[key] && axis.n == 1)
                        key = coord + "axis"; // support x1axis as xaxis
                    if (ranges[key]) {
                        from = ranges[key].from;
                        to = ranges[key].to;
                        break;
                    }
                }
            }

            // backwards-compat stuff - to be removed in future
            if (!ranges[key]) {
                axis = coord == "x" ? plot.getXAxes()[0] : plot.getYAxes()[0];
                from = ranges[coord + "1"];
                to = ranges[coord + "2"];
            }

            // auto-reverse as an added bonus
            if (from != null && to != null && from > to) {
                var tmp = from;
                from = to;
                to = tmp;
            }
            
            // if we use "day" as typeSize we need to be sure we work with the data
            if(typeSize == 'day') {
                if ($.inArray(from, dates) == -1) {
                    from = closestDate(dates, from);
                }
                if ($.inArray(to, dates) == -1) {
                    to = closestDate(dates, to);
                }   
            }
            
            return {
                from: from, 
                to: to, 
                axis: axis
            };

        }

        function setSelection(ranges, preventEvent) {
            var axis, range, o = plot.getOptions();
            if (o.selection.mode == "y") {
                selection.first.x = 0;
                selection.second.x = plot.width();
            }
            else {
                range = extractRange(ranges, "x");
                
                selection.first.x = range.axis.p2c(range.from);
                selection.second.x = range.axis.p2c(range.to);
            }

            if (o.selection.mode == "x") {
                selection.first.y = 0;
                selection.second.y = plot.height();
            }
            else {
                range = extractRange(ranges, "y");
                selection.first.y = range.axis.p2c(range.from);
                selection.second.y = range.axis.p2c(range.to);
            }

            selection.show = true;
            plot.triggerRedrawOverlay();
            if (!preventEvent && selectionIsSane()) {
                triggerSelectedEvent();
            }
        }
        
        
        function closestPosX(num, arr) {
            var curr = arr[0];
            var diff = Math.abs (num - curr);
            for (var val = 0; val < arr.length; val++) {
                var newdiff = Math.abs (num - arr[val]);
                if (newdiff < diff) {
                    diff = newdiff;
                    curr = arr[val];
                }
            }
            return curr;
        }
        
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

        function selectionIsSane() {
            var minSize = plot.getOptions().selection.minSize; 
            var typeSize = plot.getOptions().selection.typeSize;
            
           
            if(typeSize == 'day') {
                var r = {}, c1 = selection.first, c2 = selection.second;
                var xaxis = plot.getAxes().xaxis;
                var p1 = xaxis.c2p(c1['x']), p2 = xaxis.c2p(c2['x']);
                r = {
                    from: Math.min(p1, p2),
                    to: Math.max(p1, p2)
                };
                
                var d1 = new Date(r.from);
                var d2 = new Date(r.to);
                
                var diff = dateDiffInDays(d1, d2);
                return diff >= minSize - 1;
            
            } else {
                return Math.abs(selection.second.x - selection.first.x) >= minSize &&
                Math.abs(selection.second.y - selection.first.y) >= minSize;
            }
        
            
        }
        
        
        // returns the difference in days from 2 dates objects
        function dateDiffInDays(a, b) {
            var _MS_PER_DAY = 1000 * 60 * 60 * 24; // 1 days in miliseconds
  
            // Discard the time and time-zone information.
            var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
            var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

            return Math.floor((utc2 - utc1) / _MS_PER_DAY);
        }
        
        function updateCursor(e) {
            var o = plot.getOptions();
            var mode = o.selection.mode;
            var offset = plot.getPlaceholder().offset();
            var plotOffset = plot.getPlotOffset();
            var inside = true;
            var cursor = "";
	  
            if (mode == "y" || mode == "xy") {
                var y = clamp(0, e.pageY - offset.top - plotOffset.top, plot.height());
                if (Math.abs(y - selection.first.y) <= 1) {
                    if (y < selection.second.y) {
                        cursor += "N";
                    } else {
                        cursor += "S";
                    }
                } else if (Math.abs(y - selection.second.y) <= 1) {
                    if (y < selection.first.y) {
                        cursor += "N";
                    } else {
                        cursor += "S";
                    }
                } else {
                    inside &= isInBetween(selection.first.y, y, selection.second.y);
                }
            }
	  
            if (mode == "x" || mode == "xy") {
                var x = clamp(0, e.pageX - offset.left - plotOffset.left, plot.width());
                if (Math.abs(x - selection.first.x) <= 1) {
                    if (x < selection.second.x) {
                        cursor += "W";
                    } else {
                        cursor += "E";
                    }
                } else if (Math.abs(x - selection.second.x) <= 1) {
                    if (x < selection.first.x) {
                        cursor += "W";
                    } else {
                        cursor += "E";
                    }
                } else {
                    inside &= isInBetween(selection.first.x, x, selection.second.x);
                }
            }
	  
            if (cursor == "") {
                if (inside) {
                    cursor = "move";
                } else {
                    cursor = "default";
                }
            } else {
                cursor += "-resize";
            }
	  
            plot.getPlaceholder().css("cursor", cursor);
        }
        
        function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
            if (typeof stroke == "undefined" ) {
                stroke = true;
            }
            if (typeof radius === "undefined") {
                radius = 5;
            }
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            if (stroke) {
                ctx.stroke();
            }
            if (fill) {
                ctx.fill();
            }        
        }

        plot.clearSelection = clearSelection;
        plot.setSelection = setSelection;
        plot.getSelection = getSelection;

        plot.hooks.bindEvents.push(function (plot, eventHolder) {
            var o = plot.getOptions();
            if (o.selection.mode != null) {
                eventHolder.mousemove(onMouseMove);
                eventHolder.mousedown(onMouseDown);
            }
        });
        
        


        plot.hooks.drawOverlay.push(function (plot, ctx) {
            // draw selection
            if (selection.show && selectionIsSane()) {
                var plotOffset = plot.getPlotOffset();
                var o = plot.getOptions();
                //var minSize = plot.getOptions().selection.minSize;
                var plotHeight = plot.height();
                var plotWidth = plot.width();

                ctx.save();
                ctx.translate(plotOffset.left, plotOffset.top);

                var c = $.color.parse(o.selection.color);

                ctx.strokeStyle = c.scale('a', 0.8).toString();
                ctx.lineWidth = 1;
                ctx.lineJoin = "round";
                ctx.fillStyle = c.scale('a', 0.4).toString();

                var x = ~~Math.min(selection.first.x, selection.second.x)-.5,
                x2 = ~~Math.max(selection.first.x, selection.second.x)-.5, // for second trigger
                y = ~~Math.min(selection.first.y, selection.second.y)-.5,
                w = ~~Math.abs(selection.second.x - selection.first.x)+2,
                h = ~~Math.abs(selection.second.y - selection.first.y)+1;
               
                
                // draw the canvas overlay
                ctx.fillStyle = "rgba(255,255,255, 0.4)";
                ctx.beginPath();
                ctx.rect(0, 0, plotWidth, plotHeight);
                ctx.closePath();
                ctx.fill();
                ctx.restore();
               
                // drawing the mask
                ctx.save();
                ctx.beginPath();
                ctx.rect(x + plotOffset.left, y + plotOffset.top, w-0.5, h);
                ctx.closePath();
                ctx.globalCompositeOperation = 'destination-out';
                ctx.fill();
                ctx.restore();
                
                // draw the triggers
                ctx.save();
                ctx.fillStyle = c.scale('a', 0.8).toString();
                ctx.fillRect(x + plotOffset.left - .5, y + plotOffset.top, 1, h);
                ctx.fillRect(x2 + plotOffset.left + .5, y + plotOffset.top, 1, h);
                
                var tH = Math.round(h/3);
                ctx.fillStyle = "#eee";
                ctx.strokeStyle = c.scale('a', 2).toString();
                roundRect(ctx, (x-4.5 + plotOffset.left), (tH + plotOffset.top), 9, tH, 1, true); // first trigger
                roundRect(ctx, (x2-3.5 + plotOffset.left), (tH + plotOffset.top), 9, tH, 1, true); // second trigger
                
                ctx.fillStyle =c.scale('a', 1).toString();
                ctx.fillRect((x - 1.5 + plotOffset.left), (tH + plotOffset.top + 3), 1, tH-6);
                ctx.fillRect((x + 0.5 + plotOffset.left), (tH + plotOffset.top + 3), 1, tH-6);
                
                ctx.fillRect((x2 - 0.5 + plotOffset.left), (tH + plotOffset.top + 3), 1, tH-6);
                ctx.fillRect((x2 + 1.5 + plotOffset.left), (tH + plotOffset.top + 3), 1, tH-6);
               

                ctx.restore();
            }
        });

        plot.hooks.shutdown.push(function (plot, eventHolder) {
            eventHolder.unbind("mousemove", onMouseMove);
            eventHolder.unbind("mousedown", onMouseDown);

            if (mouseUpHandler)
                $(document).unbind("mouseup", mouseUpHandler);
        });
    }

    $.plot.plugins.push({
        init: init,
        options: {
            selection: {
                mode: null, // one of null, "x", "y" or "xy"
                color: "#000",
                navigate: false,
                typeSize: null, // if set to "days" will check for the minSize in days
                minSize: 5 // minimum number of pixels by default, it will check number of days if typeSize is "day"
            }
        },
        name: 'selection',
        version: '1.1'
    });
})(jQuery);