jQuery.fn.Deserialize = function (DataRaw) {
	function GetFieldType(Field){
		if(Field==undefined){
			return false;
		}
		return Field.tagName.toUpperCase() + ($(Field).attr('type')!=undefined?'['+$(Field).attr('type').toUpperCase()+']':'');
	}
	return this.each(function () {
		Data = DataRaw;
		Data = Data.split('&');
		var M;
		$(this).find('input:not([type=hidden]):not([type=image]):not([type=button]):not([type=submit]):not([type=radio]):not([type=checkbox]),textarea,select').val('');
		$(this).find('input[type=checkbox],input[type=radio]').removeAttr('checked');
		for(var i in Data) {
			M = Data[i].match(/^(.*?)=(.*?)$/);
			if(M!=null) {
				var Field = $('input[name="'+M[1]+'"]:not([type=hidden]):not([type=image]):not([type=button]):not([type=submit]),textarea[name="'+M[1]+'"],select[name="'+M[1]+'"],input[name="'+M[1]+'"][type=hidden][data-bx-comp-prop="true"]',this);
				var FieldType0 = GetFieldType(Field[0]);
				if(Field.length==1 && FieldType0!='INPUT[RADIO]') {
					switch(FieldType0){
						case 'INPUT[CHECKBOX]':
						case 'INPUT[RADIO]':
							if(Field.val()==M[2] || Field.attr('value')==undefined && M[2].toUpperCase()=='ON') {
								Field.attr('checked','checked');
							}
							break;
						default:
							Field.val(M[2]);
							break;
					}
				} else if(Field.length==2 && GetFieldType(Field[0])=='INPUT[HIDDEN]' && GetFieldType(Field[1])=='INPUT[CHECKBOX]') {
					if($(Field[1]).val()==M[2] || $(Field[1]).attr('value')==undefined && M[2].toUpperCase()=='ON') {
						$(Field[1]).attr('checked','checked');
					}
				} else if(Field.length==2 && GetFieldType(Field[0])=='SELECT' && GetFieldType(Field[1])=='INPUT[HIDDEN]') {
					$(Field[1]).val(M[2]);
				} else if (Field.length>0 && Field.length==Field.filter('input[type=radio]').length){
					Field.find('[value="'+M[2]+'"]').attr('checked','checked');
				}
			}
		}
	});
};





/* jQuery UI sortable- v1.11.4 - 2015-04-24 */
(function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e(jQuery)})(function(e){function t(t,s){var n,a,o,r=t.nodeName.toLowerCase();return"area"===r?(n=t.parentNode,a=n.name,t.href&&a&&"map"===n.nodeName.toLowerCase()?(o=e("img[usemap='#"+a+"']")[0],!!o&&i(o)):!1):(/^(input|select|textarea|button|object)$/.test(r)?!t.disabled:"a"===r?t.href||s:s)&&i(t)}function i(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}e.ui=e.ui||{},e.extend(e.ui,{version:"1.11.4",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({scrollParent:function(t){var i=this.css("position"),s="absolute"===i,n=t?/(auto|scroll|hidden)/:/(auto|scroll)/,a=this.parents().filter(function(){var t=e(this);return s&&"static"===t.css("position")?!1:n.test(t.css("overflow")+t.css("overflow-y")+t.css("overflow-x"))}).eq(0);return"fixed"!==i&&a.length?a:e(this[0].ownerDocument||document)},uniqueId:function(){var e=0;return function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++e)})}}(),removeUniqueId:function(){return this.each(function(){/^ui-id-\d+$/.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(i){return t(i,!isNaN(e.attr(i,"tabindex")))},tabbable:function(i){var s=e.attr(i,"tabindex"),n=isNaN(s);return(n||s>=0)&&t(i,!n)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(t,i){function s(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===i?["Left","Right"]:["Top","Bottom"],a=i.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+i]=function(t){return void 0===t?o["inner"+i].call(this):this.each(function(){e(this).css(a,s(this,t)+"px")})},e.fn["outer"+i]=function(t,n){return"number"!=typeof t?o["outer"+i].call(this,t):this.each(function(){e(this).css(a,s(this,t,!0,n)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),disableSelection:function(){var e="onselectstart"in document.createElement("div")?"selectstart":"mousedown";return function(){return this.bind(e+".ui-disableSelection",function(e){e.preventDefault()})}}(),enableSelection:function(){return this.unbind(".ui-disableSelection")},zIndex:function(t){if(void 0!==t)return this.css("zIndex",t);if(this.length)for(var i,s,n=e(this[0]);n.length&&n[0]!==document;){if(i=n.css("position"),("absolute"===i||"relative"===i||"fixed"===i)&&(s=parseInt(n.css("zIndex"),10),!isNaN(s)&&0!==s))return s;n=n.parent()}return 0}}),e.ui.plugin={add:function(t,i,s){var n,a=e.ui[t].prototype;for(n in s)a.plugins[n]=a.plugins[n]||[],a.plugins[n].push([i,s[n]])},call:function(e,t,i,s){var n,a=e.plugins[t];if(a&&(s||e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType))for(n=0;a.length>n;n++)e.options[a[n][0]]&&a[n][1].apply(e.element,i)}};var s=0,n=Array.prototype.slice;e.cleanData=function(t){return function(i){var s,n,a;for(a=0;null!=(n=i[a]);a++)try{s=e._data(n,"events"),s&&s.remove&&e(n).triggerHandler("remove")}catch(o){}t(i)}}(e.cleanData),e.widget=function(t,i,s){var n,a,o,r,h={},l=t.split(".")[0];return t=t.split(".")[1],n=l+"-"+t,s||(s=i,i=e.Widget),e.expr[":"][n.toLowerCase()]=function(t){return!!e.data(t,n)},e[l]=e[l]||{},a=e[l][t],o=e[l][t]=function(e,t){return this._createWidget?(arguments.length&&this._createWidget(e,t),void 0):new o(e,t)},e.extend(o,a,{version:s.version,_proto:e.extend({},s),_childConstructors:[]}),r=new i,r.options=e.widget.extend({},r.options),e.each(s,function(t,s){return e.isFunction(s)?(h[t]=function(){var e=function(){return i.prototype[t].apply(this,arguments)},n=function(e){return i.prototype[t].apply(this,e)};return function(){var t,i=this._super,a=this._superApply;return this._super=e,this._superApply=n,t=s.apply(this,arguments),this._super=i,this._superApply=a,t}}(),void 0):(h[t]=s,void 0)}),o.prototype=e.widget.extend(r,{widgetEventPrefix:a?r.widgetEventPrefix||t:t},h,{constructor:o,namespace:l,widgetName:t,widgetFullName:n}),a?(e.each(a._childConstructors,function(t,i){var s=i.prototype;e.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete a._childConstructors):i._childConstructors.push(o),e.widget.bridge(t,o),o},e.widget.extend=function(t){for(var i,s,a=n.call(arguments,1),o=0,r=a.length;r>o;o++)for(i in a[o])s=a[o][i],a[o].hasOwnProperty(i)&&void 0!==s&&(t[i]=e.isPlainObject(s)?e.isPlainObject(t[i])?e.widget.extend({},t[i],s):e.widget.extend({},s):s);return t},e.widget.bridge=function(t,i){var s=i.prototype.widgetFullName||t;e.fn[t]=function(a){var o="string"==typeof a,r=n.call(arguments,1),h=this;return o?this.each(function(){var i,n=e.data(this,s);return"instance"===a?(h=n,!1):n?e.isFunction(n[a])&&"_"!==a.charAt(0)?(i=n[a].apply(n,r),i!==n&&void 0!==i?(h=i&&i.jquery?h.pushStack(i.get()):i,!1):void 0):e.error("no such method '"+a+"' for "+t+" widget instance"):e.error("cannot call methods on "+t+" prior to initialization; "+"attempted to call method '"+a+"'")}):(r.length&&(a=e.widget.extend.apply(null,[a].concat(r))),this.each(function(){var t=e.data(this,s);t?(t.option(a||{}),t._init&&t._init()):e.data(this,s,new i(a,this))})),h}},e.Widget=function(){},e.Widget._childConstructors=[],e.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(t,i){i=e(i||this.defaultElement||this)[0],this.element=e(i),this.uuid=s++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=e(),this.hoverable=e(),this.focusable=e(),i!==this&&(e.data(i,this.widgetFullName,this),this._on(!0,this.element,{remove:function(e){e.target===i&&this.destroy()}}),this.document=e(i.style?i.ownerDocument:i.document||i),this.window=e(this.document[0].defaultView||this.document[0].parentWindow)),this.options=e.widget.extend({},this.options,this._getCreateOptions(),t),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:e.noop,_getCreateEventData:e.noop,_create:e.noop,_init:e.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:e.noop,widget:function(){return this.element},option:function(t,i){var s,n,a,o=t;if(0===arguments.length)return e.widget.extend({},this.options);if("string"==typeof t)if(o={},s=t.split("."),t=s.shift(),s.length){for(n=o[t]=e.widget.extend({},this.options[t]),a=0;s.length-1>a;a++)n[s[a]]=n[s[a]]||{},n=n[s[a]];if(t=s.pop(),1===arguments.length)return void 0===n[t]?null:n[t];n[t]=i}else{if(1===arguments.length)return void 0===this.options[t]?null:this.options[t];o[t]=i}return this._setOptions(o),this},_setOptions:function(e){var t;for(t in e)this._setOption(t,e[t]);return this},_setOption:function(e,t){return this.options[e]=t,"disabled"===e&&(this.widget().toggleClass(this.widgetFullName+"-disabled",!!t),t&&(this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus"))),this},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_on:function(t,i,s){var n,a=this;"boolean"!=typeof t&&(s=i,i=t,t=!1),s?(i=n=e(i),this.bindings=this.bindings.add(i)):(s=i,i=this.element,n=this.widget()),e.each(s,function(s,o){function r(){return t||a.options.disabled!==!0&&!e(this).hasClass("ui-state-disabled")?("string"==typeof o?a[o]:o).apply(a,arguments):void 0}"string"!=typeof o&&(r.guid=o.guid=o.guid||r.guid||e.guid++);var h=s.match(/^([\w:-]*)\s*(.*)$/),l=h[1]+a.eventNamespace,u=h[2];u?n.delegate(u,l,r):i.bind(l,r)})},_off:function(t,i){i=(i||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,t.unbind(i).undelegate(i),this.bindings=e(this.bindings.not(t).get()),this.focusable=e(this.focusable.not(t).get()),this.hoverable=e(this.hoverable.not(t).get())},_delay:function(e,t){function i(){return("string"==typeof e?s[e]:e).apply(s,arguments)}var s=this;return setTimeout(i,t||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){e(t.currentTarget).addClass("ui-state-hover")},mouseleave:function(t){e(t.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){e(t.currentTarget).addClass("ui-state-focus")},focusout:function(t){e(t.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(t,i,s){var n,a,o=this.options[t];if(s=s||{},i=e.Event(i),i.type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),i.target=this.element[0],a=i.originalEvent)for(n in a)n in i||(i[n]=a[n]);return this.element.trigger(i,s),!(e.isFunction(o)&&o.apply(this.element[0],[i].concat(s))===!1||i.isDefaultPrevented())}},e.each({show:"fadeIn",hide:"fadeOut"},function(t,i){e.Widget.prototype["_"+t]=function(s,n,a){"string"==typeof n&&(n={effect:n});var o,r=n?n===!0||"number"==typeof n?i:n.effect||i:t;n=n||{},"number"==typeof n&&(n={duration:n}),o=!e.isEmptyObject(n),n.complete=a,n.delay&&s.delay(n.delay),o&&e.effects&&e.effects.effect[r]?s[t](n):r!==t&&s[r]?s[r](n.duration,n.easing,a):s.queue(function(i){e(this)[t](),a&&a.call(s[0]),i()})}}),e.widget;var a=!1;e(document).mouseup(function(){a=!1}),e.widget("ui.mouse",{version:"1.11.4",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var t=this;this.element.bind("mousedown."+this.widgetName,function(e){return t._mouseDown(e)}).bind("click."+this.widgetName,function(i){return!0===e.data(i.target,t.widgetName+".preventClickEvent")?(e.removeData(i.target,t.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):void 0}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&this.document.unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(t){if(!a){this._mouseMoved=!1,this._mouseStarted&&this._mouseUp(t),this._mouseDownEvent=t;var i=this,s=1===t.which,n="string"==typeof this.options.cancel&&t.target.nodeName?e(t.target).closest(this.options.cancel).length:!1;return s&&!n&&this._mouseCapture(t)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){i.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(t)!==!1,!this._mouseStarted)?(t.preventDefault(),!0):(!0===e.data(t.target,this.widgetName+".preventClickEvent")&&e.removeData(t.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(e){return i._mouseMove(e)},this._mouseUpDelegate=function(e){return i._mouseUp(e)},this.document.bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),t.preventDefault(),a=!0,!0)):!0}},_mouseMove:function(t){if(this._mouseMoved){if(e.ui.ie&&(!document.documentMode||9>document.documentMode)&&!t.button)return this._mouseUp(t);if(!t.which)return this._mouseUp(t)}return(t.which||t.button)&&(this._mouseMoved=!0),this._mouseStarted?(this._mouseDrag(t),t.preventDefault()):(this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,t)!==!1,this._mouseStarted?this._mouseDrag(t):this._mouseUp(t)),!this._mouseStarted)},_mouseUp:function(t){return this.document.unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,t.target===this._mouseDownEvent.target&&e.data(t.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(t)),a=!1,!1},_mouseDistanceMet:function(e){return Math.max(Math.abs(this._mouseDownEvent.pageX-e.pageX),Math.abs(this._mouseDownEvent.pageY-e.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}}),e.widget("ui.sortable",e.ui.mouse,{version:"1.11.4",widgetEventPrefix:"sort",ready:!1,options:{appendTo:"parent",axis:!1,connectWith:!1,containment:!1,cursor:"auto",cursorAt:!1,dropOnEmpty:!0,forcePlaceholderSize:!1,forceHelperSize:!1,grid:!1,handle:!1,helper:"original",items:"> *",opacity:!1,placeholder:!1,revert:!1,scroll:!0,scrollSensitivity:20,scrollSpeed:20,scope:"default",tolerance:"intersect",zIndex:1e3,activate:null,beforeStop:null,change:null,deactivate:null,out:null,over:null,receive:null,remove:null,sort:null,start:null,stop:null,update:null},_isOverAxis:function(e,t,i){return e>=t&&t+i>e},_isFloating:function(e){return/left|right/.test(e.css("float"))||/inline|table-cell/.test(e.css("display"))},_create:function(){this.containerCache={},this.element.addClass("ui-sortable"),this.refresh(),this.offset=this.element.offset(),this._mouseInit(),this._setHandleClassName(),this.ready=!0},_setOption:function(e,t){this._super(e,t),"handle"===e&&this._setHandleClassName()},_setHandleClassName:function(){this.element.find(".ui-sortable-handle").removeClass("ui-sortable-handle"),e.each(this.items,function(){(this.instance.options.handle?this.item.find(this.instance.options.handle):this.item).addClass("ui-sortable-handle")})},_destroy:function(){this.element.removeClass("ui-sortable ui-sortable-disabled").find(".ui-sortable-handle").removeClass("ui-sortable-handle"),this._mouseDestroy();for(var e=this.items.length-1;e>=0;e--)this.items[e].item.removeData(this.widgetName+"-item");return this},_mouseCapture:function(t,i){var s=null,n=!1,a=this;return this.reverting?!1:this.options.disabled||"static"===this.options.type?!1:(this._refreshItems(t),e(t.target).parents().each(function(){return e.data(this,a.widgetName+"-item")===a?(s=e(this),!1):void 0}),e.data(t.target,a.widgetName+"-item")===a&&(s=e(t.target)),s?!this.options.handle||i||(e(this.options.handle,s).find("*").addBack().each(function(){this===t.target&&(n=!0)}),n)?(this.currentItem=s,this._removeCurrentsFromItems(),!0):!1:!1)},_mouseStart:function(t,i,s){var n,a,o=this.options;if(this.currentContainer=this,this.refreshPositions(),this.helper=this._createHelper(t),this._cacheHelperProportions(),this._cacheMargins(),this.scrollParent=this.helper.scrollParent(),this.offset=this.currentItem.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},e.extend(this.offset,{click:{left:t.pageX-this.offset.left,top:t.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.helper.css("position","absolute"),this.cssPosition=this.helper.css("position"),this.originalPosition=this._generatePosition(t),this.originalPageX=t.pageX,this.originalPageY=t.pageY,o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt),this.domPosition={prev:this.currentItem.prev()[0],parent:this.currentItem.parent()[0]},this.helper[0]!==this.currentItem[0]&&this.currentItem.hide(),this._createPlaceholder(),o.containment&&this._setContainment(),o.cursor&&"auto"!==o.cursor&&(a=this.document.find("body"),this.storedCursor=a.css("cursor"),a.css("cursor",o.cursor),this.storedStylesheet=e("<style>*{ cursor: "+o.cursor+" !important; }</style>").appendTo(a)),o.opacity&&(this.helper.css("opacity")&&(this._storedOpacity=this.helper.css("opacity")),this.helper.css("opacity",o.opacity)),o.zIndex&&(this.helper.css("zIndex")&&(this._storedZIndex=this.helper.css("zIndex")),this.helper.css("zIndex",o.zIndex)),this.scrollParent[0]!==this.document[0]&&"HTML"!==this.scrollParent[0].tagName&&(this.overflowOffset=this.scrollParent.offset()),this._trigger("start",t,this._uiHash()),this._preserveHelperProportions||this._cacheHelperProportions(),!s)for(n=this.containers.length-1;n>=0;n--)this.containers[n]._trigger("activate",t,this._uiHash(this));return e.ui.ddmanager&&(e.ui.ddmanager.current=this),e.ui.ddmanager&&!o.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t),this.dragging=!0,this.helper.addClass("ui-sortable-helper"),this._mouseDrag(t),!0},_mouseDrag:function(t){var i,s,n,a,o=this.options,r=!1;for(this.position=this._generatePosition(t),this.positionAbs=this._convertPositionTo("absolute"),this.lastPositionAbs||(this.lastPositionAbs=this.positionAbs),this.options.scroll&&(this.scrollParent[0]!==this.document[0]&&"HTML"!==this.scrollParent[0].tagName?(this.overflowOffset.top+this.scrollParent[0].offsetHeight-t.pageY<o.scrollSensitivity?this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop+o.scrollSpeed:t.pageY-this.overflowOffset.top<o.scrollSensitivity&&(this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop-o.scrollSpeed),this.overflowOffset.left+this.scrollParent[0].offsetWidth-t.pageX<o.scrollSensitivity?this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft+o.scrollSpeed:t.pageX-this.overflowOffset.left<o.scrollSensitivity&&(this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft-o.scrollSpeed)):(t.pageY-this.document.scrollTop()<o.scrollSensitivity?r=this.document.scrollTop(this.document.scrollTop()-o.scrollSpeed):this.window.height()-(t.pageY-this.document.scrollTop())<o.scrollSensitivity&&(r=this.document.scrollTop(this.document.scrollTop()+o.scrollSpeed)),t.pageX-this.document.scrollLeft()<o.scrollSensitivity?r=this.document.scrollLeft(this.document.scrollLeft()-o.scrollSpeed):this.window.width()-(t.pageX-this.document.scrollLeft())<o.scrollSensitivity&&(r=this.document.scrollLeft(this.document.scrollLeft()+o.scrollSpeed))),r!==!1&&e.ui.ddmanager&&!o.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t)),this.positionAbs=this._convertPositionTo("absolute"),this.options.axis&&"y"===this.options.axis||(this.helper[0].style.left=this.position.left+"px"),this.options.axis&&"x"===this.options.axis||(this.helper[0].style.top=this.position.top+"px"),i=this.items.length-1;i>=0;i--)if(s=this.items[i],n=s.item[0],a=this._intersectsWithPointer(s),a&&s.instance===this.currentContainer&&n!==this.currentItem[0]&&this.placeholder[1===a?"next":"prev"]()[0]!==n&&!e.contains(this.placeholder[0],n)&&("semi-dynamic"===this.options.type?!e.contains(this.element[0],n):!0)){if(this.direction=1===a?"down":"up","pointer"!==this.options.tolerance&&!this._intersectsWithSides(s))break;this._rearrange(t,s),this._trigger("change",t,this._uiHash());break}return this._contactContainers(t),e.ui.ddmanager&&e.ui.ddmanager.drag(this,t),this._trigger("sort",t,this._uiHash()),this.lastPositionAbs=this.positionAbs,!1},_mouseStop:function(t,i){if(t){if(e.ui.ddmanager&&!this.options.dropBehaviour&&e.ui.ddmanager.drop(this,t),this.options.revert){var s=this,n=this.placeholder.offset(),a=this.options.axis,o={};a&&"x"!==a||(o.left=n.left-this.offset.parent.left-this.margins.left+(this.offsetParent[0]===this.document[0].body?0:this.offsetParent[0].scrollLeft)),a&&"y"!==a||(o.top=n.top-this.offset.parent.top-this.margins.top+(this.offsetParent[0]===this.document[0].body?0:this.offsetParent[0].scrollTop)),this.reverting=!0,e(this.helper).animate(o,parseInt(this.options.revert,10)||500,function(){s._clear(t)})}else this._clear(t,i);return!1}},cancel:function(){if(this.dragging){this._mouseUp({target:null}),"original"===this.options.helper?this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper"):this.currentItem.show();for(var t=this.containers.length-1;t>=0;t--)this.containers[t]._trigger("deactivate",null,this._uiHash(this)),this.containers[t].containerCache.over&&(this.containers[t]._trigger("out",null,this._uiHash(this)),this.containers[t].containerCache.over=0)}return this.placeholder&&(this.placeholder[0].parentNode&&this.placeholder[0].parentNode.removeChild(this.placeholder[0]),"original"!==this.options.helper&&this.helper&&this.helper[0].parentNode&&this.helper.remove(),e.extend(this,{helper:null,dragging:!1,reverting:!1,_noFinalSort:null}),this.domPosition.prev?e(this.domPosition.prev).after(this.currentItem):e(this.domPosition.parent).prepend(this.currentItem)),this},serialize:function(t){var i=this._getItemsAsjQuery(t&&t.connected),s=[];return t=t||{},e(i).each(function(){var i=(e(t.item||this).attr(t.attribute||"id")||"").match(t.expression||/(.+)[\-=_](.+)/);i&&s.push((t.key||i[1]+"[]")+"="+(t.key&&t.expression?i[1]:i[2]))}),!s.length&&t.key&&s.push(t.key+"="),s.join("&")},toArray:function(t){var i=this._getItemsAsjQuery(t&&t.connected),s=[];return t=t||{},i.each(function(){s.push(e(t.item||this).attr(t.attribute||"id")||"")}),s},_intersectsWith:function(e){var t=this.positionAbs.left,i=t+this.helperProportions.width,s=this.positionAbs.top,n=s+this.helperProportions.height,a=e.left,o=a+e.width,r=e.top,h=r+e.height,l=this.offset.click.top,u=this.offset.click.left,d="x"===this.options.axis||s+l>r&&h>s+l,c="y"===this.options.axis||t+u>a&&o>t+u,p=d&&c;return"pointer"===this.options.tolerance||this.options.forcePointerForContainers||"pointer"!==this.options.tolerance&&this.helperProportions[this.floating?"width":"height"]>e[this.floating?"width":"height"]?p:t+this.helperProportions.width/2>a&&o>i-this.helperProportions.width/2&&s+this.helperProportions.height/2>r&&h>n-this.helperProportions.height/2},_intersectsWithPointer:function(e){var t="x"===this.options.axis||this._isOverAxis(this.positionAbs.top+this.offset.click.top,e.top,e.height),i="y"===this.options.axis||this._isOverAxis(this.positionAbs.left+this.offset.click.left,e.left,e.width),s=t&&i,n=this._getDragVerticalDirection(),a=this._getDragHorizontalDirection();return s?this.floating?a&&"right"===a||"down"===n?2:1:n&&("down"===n?2:1):!1},_intersectsWithSides:function(e){var t=this._isOverAxis(this.positionAbs.top+this.offset.click.top,e.top+e.height/2,e.height),i=this._isOverAxis(this.positionAbs.left+this.offset.click.left,e.left+e.width/2,e.width),s=this._getDragVerticalDirection(),n=this._getDragHorizontalDirection();return this.floating&&n?"right"===n&&i||"left"===n&&!i:s&&("down"===s&&t||"up"===s&&!t)},_getDragVerticalDirection:function(){var e=this.positionAbs.top-this.lastPositionAbs.top;return 0!==e&&(e>0?"down":"up")},_getDragHorizontalDirection:function(){var e=this.positionAbs.left-this.lastPositionAbs.left;return 0!==e&&(e>0?"right":"left")},refresh:function(e){return this._refreshItems(e),this._setHandleClassName(),this.refreshPositions(),this},_connectWith:function(){var e=this.options;return e.connectWith.constructor===String?[e.connectWith]:e.connectWith},_getItemsAsjQuery:function(t){function i(){r.push(this)}var s,n,a,o,r=[],h=[],l=this._connectWith();if(l&&t)for(s=l.length-1;s>=0;s--)for(a=e(l[s],this.document[0]),n=a.length-1;n>=0;n--)o=e.data(a[n],this.widgetFullName),o&&o!==this&&!o.options.disabled&&h.push([e.isFunction(o.options.items)?o.options.items.call(o.element):e(o.options.items,o.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),o]);for(h.push([e.isFunction(this.options.items)?this.options.items.call(this.element,null,{options:this.options,item:this.currentItem}):e(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]),s=h.length-1;s>=0;s--)h[s][0].each(i);return e(r)},_removeCurrentsFromItems:function(){var t=this.currentItem.find(":data("+this.widgetName+"-item)");this.items=e.grep(this.items,function(e){for(var i=0;t.length>i;i++)if(t[i]===e.item[0])return!1;return!0})},_refreshItems:function(t){this.items=[],this.containers=[this];var i,s,n,a,o,r,h,l,u=this.items,d=[[e.isFunction(this.options.items)?this.options.items.call(this.element[0],t,{item:this.currentItem}):e(this.options.items,this.element),this]],c=this._connectWith();if(c&&this.ready)for(i=c.length-1;i>=0;i--)for(n=e(c[i],this.document[0]),s=n.length-1;s>=0;s--)a=e.data(n[s],this.widgetFullName),a&&a!==this&&!a.options.disabled&&(d.push([e.isFunction(a.options.items)?a.options.items.call(a.element[0],t,{item:this.currentItem}):e(a.options.items,a.element),a]),this.containers.push(a));for(i=d.length-1;i>=0;i--)for(o=d[i][1],r=d[i][0],s=0,l=r.length;l>s;s++)h=e(r[s]),h.data(this.widgetName+"-item",o),u.push({item:h,instance:o,width:0,height:0,left:0,top:0})},refreshPositions:function(t){this.floating=this.items.length?"x"===this.options.axis||this._isFloating(this.items[0].item):!1,this.offsetParent&&this.helper&&(this.offset.parent=this._getParentOffset());var i,s,n,a;for(i=this.items.length-1;i>=0;i--)s=this.items[i],s.instance!==this.currentContainer&&this.currentContainer&&s.item[0]!==this.currentItem[0]||(n=this.options.toleranceElement?e(this.options.toleranceElement,s.item):s.item,t||(s.width=n.outerWidth(),s.height=n.outerHeight()),a=n.offset(),s.left=a.left,s.top=a.top);if(this.options.custom&&this.options.custom.refreshContainers)this.options.custom.refreshContainers.call(this);else for(i=this.containers.length-1;i>=0;i--)a=this.containers[i].element.offset(),this.containers[i].containerCache.left=a.left,this.containers[i].containerCache.top=a.top,this.containers[i].containerCache.width=this.containers[i].element.outerWidth(),this.containers[i].containerCache.height=this.containers[i].element.outerHeight();return this},_createPlaceholder:function(t){t=t||this;var i,s=t.options;s.placeholder&&s.placeholder.constructor!==String||(i=s.placeholder,s.placeholder={element:function(){var s=t.currentItem[0].nodeName.toLowerCase(),n=e("<"+s+">",t.document[0]).addClass(i||t.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper");return"tbody"===s?t._createTrPlaceholder(t.currentItem.find("tr").eq(0),e("<tr>",t.document[0]).appendTo(n)):"tr"===s?t._createTrPlaceholder(t.currentItem,n):"img"===s&&n.attr("src",t.currentItem.attr("src")),i||n.css("visibility","hidden"),n},update:function(e,n){(!i||s.forcePlaceholderSize)&&(n.height()||n.height(t.currentItem.innerHeight()-parseInt(t.currentItem.css("paddingTop")||0,10)-parseInt(t.currentItem.css("paddingBottom")||0,10)),n.width()||n.width(t.currentItem.innerWidth()-parseInt(t.currentItem.css("paddingLeft")||0,10)-parseInt(t.currentItem.css("paddingRight")||0,10)))}}),t.placeholder=e(s.placeholder.element.call(t.element,t.currentItem)),t.currentItem.after(t.placeholder),s.placeholder.update(t,t.placeholder)},_createTrPlaceholder:function(t,i){var s=this;t.children().each(function(){e("<td>&#160;</td>",s.document[0]).attr("colspan",e(this).attr("colspan")||1).appendTo(i)})},_contactContainers:function(t){var i,s,n,a,o,r,h,l,u,d,c=null,p=null;for(i=this.containers.length-1;i>=0;i--)if(!e.contains(this.currentItem[0],this.containers[i].element[0]))if(this._intersectsWith(this.containers[i].containerCache)){if(c&&e.contains(this.containers[i].element[0],c.element[0]))continue;c=this.containers[i],p=i}else this.containers[i].containerCache.over&&(this.containers[i]._trigger("out",t,this._uiHash(this)),this.containers[i].containerCache.over=0);if(c)if(1===this.containers.length)this.containers[p].containerCache.over||(this.containers[p]._trigger("over",t,this._uiHash(this)),this.containers[p].containerCache.over=1);else{for(n=1e4,a=null,u=c.floating||this._isFloating(this.currentItem),o=u?"left":"top",r=u?"width":"height",d=u?"clientX":"clientY",s=this.items.length-1;s>=0;s--)e.contains(this.containers[p].element[0],this.items[s].item[0])&&this.items[s].item[0]!==this.currentItem[0]&&(h=this.items[s].item.offset()[o],l=!1,t[d]-h>this.items[s][r]/2&&(l=!0),n>Math.abs(t[d]-h)&&(n=Math.abs(t[d]-h),a=this.items[s],this.direction=l?"up":"down"));if(!a&&!this.options.dropOnEmpty)return;if(this.currentContainer===this.containers[p])return this.currentContainer.containerCache.over||(this.containers[p]._trigger("over",t,this._uiHash()),this.currentContainer.containerCache.over=1),void 0;a?this._rearrange(t,a,null,!0):this._rearrange(t,null,this.containers[p].element,!0),this._trigger("change",t,this._uiHash()),this.containers[p]._trigger("change",t,this._uiHash(this)),this.currentContainer=this.containers[p],this.options.placeholder.update(this.currentContainer,this.placeholder),this.containers[p]._trigger("over",t,this._uiHash(this)),this.containers[p].containerCache.over=1}},_createHelper:function(t){var i=this.options,s=e.isFunction(i.helper)?e(i.helper.apply(this.element[0],[t,this.currentItem])):"clone"===i.helper?this.currentItem.clone():this.currentItem;return s.parents("body").length||e("parent"!==i.appendTo?i.appendTo:this.currentItem[0].parentNode)[0].appendChild(s[0]),s[0]===this.currentItem[0]&&(this._storedCSS={width:this.currentItem[0].style.width,height:this.currentItem[0].style.height,position:this.currentItem.css("position"),top:this.currentItem.css("top"),left:this.currentItem.css("left")}),(!s[0].style.width||i.forceHelperSize)&&s.width(this.currentItem.width()),(!s[0].style.height||i.forceHelperSize)&&s.height(this.currentItem.height()),s},_adjustOffsetFromHelper:function(t){"string"==typeof t&&(t=t.split(" ")),e.isArray(t)&&(t={left:+t[0],top:+t[1]||0}),"left"in t&&(this.offset.click.left=t.left+this.margins.left),"right"in t&&(this.offset.click.left=this.helperProportions.width-t.right+this.margins.left),"top"in t&&(this.offset.click.top=t.top+this.margins.top),"bottom"in t&&(this.offset.click.top=this.helperProportions.height-t.bottom+this.margins.top)},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var t=this.offsetParent.offset();return"absolute"===this.cssPosition&&this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])&&(t.left+=this.scrollParent.scrollLeft(),t.top+=this.scrollParent.scrollTop()),(this.offsetParent[0]===this.document[0].body||this.offsetParent[0].tagName&&"html"===this.offsetParent[0].tagName.toLowerCase()&&e.ui.ie)&&(t={top:0,left:0}),{top:t.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:t.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"===this.cssPosition){var e=this.currentItem.position();return{top:e.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:e.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.currentItem.css("marginLeft"),10)||0,top:parseInt(this.currentItem.css("marginTop"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var t,i,s,n=this.options;"parent"===n.containment&&(n.containment=this.helper[0].parentNode),("document"===n.containment||"window"===n.containment)&&(this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,"document"===n.containment?this.document.width():this.window.width()-this.helperProportions.width-this.margins.left,("document"===n.containment?this.document.width():this.window.height()||this.document[0].body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]),/^(document|window|parent)$/.test(n.containment)||(t=e(n.containment)[0],i=e(n.containment).offset(),s="hidden"!==e(t).css("overflow"),this.containment=[i.left+(parseInt(e(t).css("borderLeftWidth"),10)||0)+(parseInt(e(t).css("paddingLeft"),10)||0)-this.margins.left,i.top+(parseInt(e(t).css("borderTopWidth"),10)||0)+(parseInt(e(t).css("paddingTop"),10)||0)-this.margins.top,i.left+(s?Math.max(t.scrollWidth,t.offsetWidth):t.offsetWidth)-(parseInt(e(t).css("borderLeftWidth"),10)||0)-(parseInt(e(t).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,i.top+(s?Math.max(t.scrollHeight,t.offsetHeight):t.offsetHeight)-(parseInt(e(t).css("borderTopWidth"),10)||0)-(parseInt(e(t).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top])
},_convertPositionTo:function(t,i){i||(i=this.position);var s="absolute"===t?1:-1,n="absolute"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,a=/(html|body)/i.test(n[0].tagName);return{top:i.top+this.offset.relative.top*s+this.offset.parent.top*s-("fixed"===this.cssPosition?-this.scrollParent.scrollTop():a?0:n.scrollTop())*s,left:i.left+this.offset.relative.left*s+this.offset.parent.left*s-("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():a?0:n.scrollLeft())*s}},_generatePosition:function(t){var i,s,n=this.options,a=t.pageX,o=t.pageY,r="absolute"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,h=/(html|body)/i.test(r[0].tagName);return"relative"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&this.scrollParent[0]!==this.offsetParent[0]||(this.offset.relative=this._getRelativeOffset()),this.originalPosition&&(this.containment&&(t.pageX-this.offset.click.left<this.containment[0]&&(a=this.containment[0]+this.offset.click.left),t.pageY-this.offset.click.top<this.containment[1]&&(o=this.containment[1]+this.offset.click.top),t.pageX-this.offset.click.left>this.containment[2]&&(a=this.containment[2]+this.offset.click.left),t.pageY-this.offset.click.top>this.containment[3]&&(o=this.containment[3]+this.offset.click.top)),n.grid&&(i=this.originalPageY+Math.round((o-this.originalPageY)/n.grid[1])*n.grid[1],o=this.containment?i-this.offset.click.top>=this.containment[1]&&i-this.offset.click.top<=this.containment[3]?i:i-this.offset.click.top>=this.containment[1]?i-n.grid[1]:i+n.grid[1]:i,s=this.originalPageX+Math.round((a-this.originalPageX)/n.grid[0])*n.grid[0],a=this.containment?s-this.offset.click.left>=this.containment[0]&&s-this.offset.click.left<=this.containment[2]?s:s-this.offset.click.left>=this.containment[0]?s-n.grid[0]:s+n.grid[0]:s)),{top:o-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.scrollParent.scrollTop():h?0:r.scrollTop()),left:a-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():h?0:r.scrollLeft())}},_rearrange:function(e,t,i,s){i?i[0].appendChild(this.placeholder[0]):t.item[0].parentNode.insertBefore(this.placeholder[0],"down"===this.direction?t.item[0]:t.item[0].nextSibling),this.counter=this.counter?++this.counter:1;var n=this.counter;this._delay(function(){n===this.counter&&this.refreshPositions(!s)})},_clear:function(e,t){function i(e,t,i){return function(s){i._trigger(e,s,t._uiHash(t))}}this.reverting=!1;var s,n=[];if(!this._noFinalSort&&this.currentItem.parent().length&&this.placeholder.before(this.currentItem),this._noFinalSort=null,this.helper[0]===this.currentItem[0]){for(s in this._storedCSS)("auto"===this._storedCSS[s]||"static"===this._storedCSS[s])&&(this._storedCSS[s]="");this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")}else this.currentItem.show();for(this.fromOutside&&!t&&n.push(function(e){this._trigger("receive",e,this._uiHash(this.fromOutside))}),!this.fromOutside&&this.domPosition.prev===this.currentItem.prev().not(".ui-sortable-helper")[0]&&this.domPosition.parent===this.currentItem.parent()[0]||t||n.push(function(e){this._trigger("update",e,this._uiHash())}),this!==this.currentContainer&&(t||(n.push(function(e){this._trigger("remove",e,this._uiHash())}),n.push(function(e){return function(t){e._trigger("receive",t,this._uiHash(this))}}.call(this,this.currentContainer)),n.push(function(e){return function(t){e._trigger("update",t,this._uiHash(this))}}.call(this,this.currentContainer)))),s=this.containers.length-1;s>=0;s--)t||n.push(i("deactivate",this,this.containers[s])),this.containers[s].containerCache.over&&(n.push(i("out",this,this.containers[s])),this.containers[s].containerCache.over=0);if(this.storedCursor&&(this.document.find("body").css("cursor",this.storedCursor),this.storedStylesheet.remove()),this._storedOpacity&&this.helper.css("opacity",this._storedOpacity),this._storedZIndex&&this.helper.css("zIndex","auto"===this._storedZIndex?"":this._storedZIndex),this.dragging=!1,t||this._trigger("beforeStop",e,this._uiHash()),this.placeholder[0].parentNode.removeChild(this.placeholder[0]),this.cancelHelperRemoval||(this.helper[0]!==this.currentItem[0]&&this.helper.remove(),this.helper=null),!t){for(s=0;n.length>s;s++)n[s].call(this,e);this._trigger("stop",e,this._uiHash())}return this.fromOutside=!1,!this.cancelHelperRemoval},_trigger:function(){e.Widget.prototype._trigger.apply(this,arguments)===!1&&this.cancel()},_uiHash:function(t){var i=t||this;return{helper:i.helper,placeholder:i.placeholder||e([]),position:i.position,originalPosition:i.originalPosition,offset:i.positionAbs,item:i.currentItem,sender:t?t.element:null}}})});
/* Textchange */
(function(a){a.event.special.textchange={setup:function(c,b){a(this).data("lastValue","true"===this.contentEditable?a(this).html():a(this).val());a(this).bind("keyup.textchange",a.event.special.textchange.handler);a(this).bind("cut.textchange paste.textchange input.textchange",a.event.special.textchange.delayedHandler)},teardown:function(c){a(this).unbind(".textchange")},handler:function(c){a.event.special.textchange.triggerIfChanged(a(this))},delayedHandler:function(c){var b=a(this);setTimeout(function(){a.event.special.textchange.triggerIfChanged(b)},
25)},triggerIfChanged:function(a){var b="true"===a[0].contentEditable?a.html():a.val();b!==a.data("lastValue")&&(a.trigger("textchange",[a.data("lastValue")]),a.data("lastValue",b))}};a.event.special.hastext={setup:function(c,b){a(this).bind("textchange",a.event.special.hastext.handler)},teardown:function(c){a(this).unbind("textchange",a.event.special.hastext.handler)},handler:function(c,b){""===b&&b!==a(this).val()&&a(this).trigger("hastext")}};a.event.special.notext={setup:function(c,b){a(this).bind("textchange",
a.event.special.notext.handler)},teardown:function(c){a(this).unbind("textchange",a.event.special.notext.handler)},handler:function(c,b){""===a(this).val()&&a(this).val()!==b&&a(this).trigger("notext")}}})(jQuery);
/* Alt Hot Key */
$.alt = function(Key, Callback) {
	$(document).keydown(function(E) {
		if(E.altKey && E.keyCode == Key.charCodeAt(0)) {
			return Callback.apply(this)
		}
	});
};
//
function Log(Message) {
	//
}
function WDA_ShowMessage(Title, Message, Type) {
	$('#wda_message').html('');
	if (Title!=false && Message.length>0) {
		var HTML = '<div class="adm-info-message-wrap adm-info-message-'+(Type=='ERROR'?'red':'green')+'">'+
			'<div class="adm-info-message">'+
				'<div class="adm-info-message-title"><b>'+Title+'</b></div>'+
				Message+
				'<div class="adm-info-message-icon"></div>'+
			'</div>'+
		'</div>';
		$('#wda_message').html(HTML);
	};
}
//
function WDA_SetCheckboxValue(Checkbox, Value){
	if (Value=='Y' || Value==true || Value=='true') {
		$(Checkbox).attr('checked','checked');
	} else {
		$(Checkbox).removeAttr('checked');
	}
}
//
function WDA_StrRepeat(Input,Multiplier) {
	var Buffer = '';
	for (i=0;i<Multiplier;i++){
		Buffer += Input;
	}
	return Buffer;
}
function WDA_FilterRevision(IBlockID) {
	$('#wda_filters').find('.item').each(function(){
		var Code = $(this).find('.f_p2').val();
		if (IBlockID==false || Code.length>0 && $('#wda_filter_param option[value='+Code+']').length==0) {
			$(this).click();
		}
	});
}
function WDA_OnChangeIBlockID(Select, Callback) {
	Select = $(Select);
	var IBlockID = Select.val();
	Log('onWdaBeforeIBlockChange');
	BX.onCustomEvent('onWdaBeforeIBlockChange', [Select, IBlockID]);
	if (IBlockID>0) {
		WDA_SetAdminUrl(WDA_GetCurPageParam('IBLOCK_ID='+IBlockID,['IBLOCK_ID']));
		$.ajax({
			url: WDA.Url + '?lang='+WDA.Lang+'&change_iblock=Y&iblock_id='+IBlockID,
			type: 'GET',
			data: '',
			datatype: 'json',
			success: function(JSON) {

				// Update sections
				$('#wda_select_sections').find('option').not('[value=""]').remove();
				if (JSON.SECTIONS!=undefined) {
					for (var i in JSON.SECTIONS) {
						if (!JSON.SECTIONS.hasOwnProperty(i)) continue;
						var SectionName = WDA_StrRepeat("&nbsp;&nbsp;&nbsp;&nbsp;",JSON.SECTIONS[i].DEPTH_LEVEL-1) + '['+JSON.SECTIONS[i].ID+'] '+JSON.SECTIONS[i].NAME;
						$('#wda_select_sections').append('<option value="'+JSON.SECTIONS[i].ID+'">'+SectionName+'</option>');
					}
					$('#wda_select_sections').val('');
				}
				// Update filter fields
				$('#wda_filter_param').find('option').not('[value=""]').remove();
				$('#wda_filter_param').find('optgroup').remove();
				if (JSON.FILTER_FIELDS!=undefined) {
					for (var i in JSON.FILTER_FIELDS) {
						if (!JSON.FILTER_FIELDS.hasOwnProperty(i)) continue;
						$('#wda_filter_param').append('<option value="'+JSON.FILTER_FIELDS[i].WDA_CODE+'" data-name="'+JSON.FILTER_FIELDS[i].WDA_NAME+'" data-type="'+JSON.FILTER_FIELDS[i].WDA_TYPE+'" data-group="'+JSON.FILTER_FIELDS[i].WDA_GROUP+'">'+JSON.FILTER_FIELDS[i].WDA_NAME_FULL+'</option>');
					}
					// Grouping
					var Groups = {};
					var EmptyOption = $('#wda_filter_param option[value=""]');
					$('#wda_filter_param option').not('[value=""]').each(function(){
						var Group = $(this).attr('data-group');
						Groups[Group] = 'Y';
					});
					for (var Group in Groups) {
						if (!Groups.hasOwnProperty(Group)) continue;
						var GroupName = JSON.GROUPS[Group]['NAME'];
						var GroupSort = JSON.GROUPS[Group]['SORT'];
						EmptyOption.before('<optgroup label="'+GroupName+'" data-group="'+Group+'" data-sort="'+GroupSort+'"></optgroup>');
					}
					$('#wda_filter_param > option').not('[value=""]').not('[data-group=""]').each(function(){
						var Group = $(this).attr('data-group');
						$('#wda_filter_param optgroup[data-group='+Group+']').append($(this));
					});
					EmptyOption.insertBefore($('#wda_filter_param optgroup').eq(0));
					// Sort groups
					$('#wda_filter_param optgroup[data-sort]').each(function(){
						var Sort1 = parseInt($(this).attr('data-sort'));
						if (isNaN(Sort1) || Sort1<0) {
							Sort1 = 0;
						}
						var OptGroup1 = $(this);
						$('#wda_filter_param optgroup[data-sort]').each(function(){
							var OptGroup2 = $(this);
							if (!OptGroup2.is(OptGroup1)) {
								var Sort2 = parseInt($(this).attr('data-sort'));
								if (isNaN(Sort2) || Sort2<0) {
									Sort2 = 0;
								}
								if (Sort2<Sort1) {
									OptGroup2.insertBefore(OptGroup1);
								}
							}
						});
					});
					$('#wda_select_action').change();
				}
				// Filter revision
				WDA_FilterRevision(IBlockID);
				// Update notifier
				WDA_StartCheckTimeout();
				// Custom events
				Log('onWdaAfterIBlockChange');
				BX.onCustomEvent('onWdaAfterIBlockChange', [Select, IBlockID]);
				// Execute callback
				if(typeof Callback == 'function'){
					Callback(Select, IBlockID);
				}
			}
		});
	} else {
		$('#wda_filter_param').find('optgroup').remove();
		$('#wda_select_action').val('').change();
		$('#wda_select_sections option').not('[value=""]').remove();
		WDA_FilterRevision(false);
	}
}
function WDA_OnChangeAction(Select, Callback) {
	WDA_DisableControls();
	WdaCurrentAction = $(Select).val();
	Log('onWdaBeforeActionChange');
	BX.onCustomEvent('onWdaBeforeActionChange');
	if (WdaCurrentAction!='') {
		WDA_SetAdminUrl(WDA_GetCurPageParam('ACTION='+WdaCurrentAction,['ACTION']));
		$('#wda_action_params').addClass('loading').html('');
		var FormData = $('#wda_form').serialize();
		$.ajax({
			url: WDA.Url + '?lang='+WDA.Lang+'&show_action_settings=Y&action='+WdaCurrentAction,
			type: 'POST',
			data: FormData,
			success: function(HTML) {
				$('#wda_action_params').html(HTML).find('input[type=checkbox]').each(function(){
					BX.adminFormTools.modifyCheckbox(this);
				});
				// Custom events
				Log('onWdaAfterActionChange');
				BX.onCustomEvent('onWdaAfterActionChange');
				// Execute callback
				if(typeof Callback == 'function'){
					Callback(Select, WdaCurrentAction);
				}
				// Ending
				$('#wda_action_params').removeClass('loading');
				WDA_EnableControls();
			},
			error: function(){
				$('#wda_action_params').removeClass('loading');
			}
		});
	} else {
		WDA_EnableControls();
	}
}
function WDA_EscapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&apos;");
}
function WDA_AddFilter(Data) {
	var Filters = $('#wda_filters');
	var Param1 = Data[0];
	var Param2 = Data[1];
	var Equal1 = Data[2];
	var Equal2 = Data[3];
	var Value1 = Data[4].replace(/&([a-z]+);/,'{html_$1}');
	var Value2 = Data[5].replace(/&([a-z]+);/,'{html_$1}');
	var NewFilter =
		'<div class="item" title="'+WDA.Messages.FilterItemTitle+'">'+
			'<span class="param">'+WDA_EscapeHtml(Param1)+'<input type="hidden" name="f_p1[]" class="f_p1" value="'+WDA_EscapeHtml(Param1)+'" /><input type="hidden" name="f_p2[]" class="f_p2" value="'+WDA_EscapeHtml(Param2)+'" /></span>'+' '+
			'<span class="equal">'+Equal1+'<input type="hidden" name="f_e1[]" class="f_e1" value="'+Equal1+'" /><input type="hidden" name="f_e2[]"class="f_e2" value="'+Equal2+'" /></span>'+' '+
			'<span class="value">'+WDA_EscapeHtml(Value1.replace(/{html_([a-z]+)}/,'<span>&amp;</span><span>$1</span><span>;</span>'))+'<input type="hidden" name="f_v1[]" class="f_v1" value="'+WDA_EscapeHtml(Value1)+'" /><input type="hidden" name="f_v2[]"class="f_v2" value="'+WDA_EscapeHtml(Value2)+'" /></span>'+
		'</div>';
	Filters.find('#filter_no_filters').hide();
	Filters.append(NewFilter);
	WDA_StartCheckTimeout();
}
function WDA_DisableControls() {
	$('#wda_submit').attr('disabled','disabled').removeClass('adm-btn-green');
}
function WDA_EnableControls() {
	$('#wda_submit').removeAttr('disabled').addClass('adm-btn-green');
}
function WDA_ShowCancelButton() {
	$('#wda_cancel').show().removeClass('disabled').removeAttr('disabled');
}
function WDA_HideCancelButton() {
	$('#wda_cancel').hide();
}
function WDA_CheckFilterResults() {
	$('#filter_check_status').html('').addClass('loading');
	WDA_DisableControls();
	var FormData = $('#wda_form').serialize();
	$.ajax({
		url: WDA.Url + '?lang='+WDA.Lang+'&check_filter_results=Y',
		type: 'POST',
		data: FormData,
		datatype: 'json',
		success: function(JSON) {
			var StatusBlock = $('#filter_check_status');
			if (JSON.count>0) {
				StatusBlock.html('<div class="full" title="'+JSON.count+'">'+JSON.count_approximately+'</div>');
			} else if (JSON.count==0) {
				StatusBlock.html('<div class="none" title="'+JSON.count+'">'+JSON.count_approximately+'</div>');
			}
			StatusBlock.removeClass('loading');
			WDA_EnableControls();
		}
	});
}
function WDA_StartCheckTimeout() {
	if (WdaCheckTimeoutDelay>0) {
		clearTimeout(WdaCheckTimeout);
		WdaCheckTimeout = setTimeout(function(){
			WDA_CheckFilterResults();
		},WdaCheckTimeoutDelay);
	}
}
function WDA_LoadPropertyEnums(PropertyID) {
	var Select = $('#wda_filter_value_list');
	Select.attr('disabled','disabled').addClass('disabled');
	var IBlockID = parseInt($('#wda_select_iblock').val());
	if (IBlockID>0) {
		$.ajax({
			url: WDA.Url + '?lang='+WDA.Lang+'&load_property_enums=Y&iblock_id='+IBlockID+'&property_id='+PropertyID,
			type: 'POST',
			data: '',
			datatype: 'json',
			success: function(JSON) {
				var Select = $('#wda_filter_value_list');
				Select.find('option').remove();
				if (JSON.ITEMS!=undefined) {
					for (var i in JSON.ITEMS) {
						if (!JSON.ITEMS.hasOwnProperty(i)) continue;
						Select.append('<option value="'+JSON.ITEMS[i].ID+'" data-name="'+JSON.ITEMS[i].VALUE+'">'+JSON.ITEMS[i].VALUE+'</option>');
					}
				}
				Select.removeAttr('disabled').removeClass('disabled');
			}
		});
	}
}
function WDA_LoadTextType() {
	var Select = $('#wda_filter_value_list');
	Select.find('option').remove();
	Select.append('<option value="text" data-name="text">text</option>');
	Select.append('<option value="html" data-name="html">html</option>');
}
function WDA_Submit() {
	WdaCanSubmit = true;
	Log('onWdaBeforeSubmit');
	BX.onCustomEvent('onWdaBeforeSubmit');
	if (WdaCanSubmit==true) {
		WDA_ShowMessage(false);
		if ($('#wda_select_iblock').val()!='') {
			WDA_StartAction(FormData);
		} else {
			WDA_ShowMessage(WDA.Messages.IBlockNotSelectedTitle,WDA.Messages.IBlockNotSelectedMessage,'ERROR');
		}
	}
}
function WDA_Process(Start){
	Start = Start==true ? 'Y' : 'N';
	var FormData = $('#wda_form').serialize();
	WdaStop = false;
	WDA_ShowProgressBar();
	WDA_ShowCancelButton();
	WdaProcessRequest = $.ajax({
		url: WDA.Url + '?lang='+WDA.Lang+'&process=Y&start='+Start,
		type: 'POST',
		data: FormData,
		datatype: 'json',
		success: function(JSON) {
			if (JSON==null || typeof JSON != 'object') {
				WDA_ActionError(1);
			} else if (WdaStop) {
				WDA_EnableControls();
				WDA_HideProgressBar();
				WDA_HideCancelButton();
			} else {
				if (JSON.done==true) {
					WDA_EndAction(JSON.count, JSON.succeed, JSON.failed);
				} else if (JSON.next==true) {
					WDA_ContinueAction(JSON.index, JSON.count);
				} else {
					WDA_ActionError(2);
				}
			}
		},
		error: function(){
			WDA_ActionError(3);
		}
	});
}
function WDA_StartAction(){
	WDA_DisableControls();
	WDA_Process(true);
}
function WDA_ContinueAction(Index, Count){
	WDA_SetProgressBarPosition(Index, Count);
	setTimeout(function(){
		WDA_Process(false);
	},WDA.StepPause*1000);
}
function WDA_EndAction(Count, Succeed, Failed){
	WDA_EnableControls();
	WDA_HideCancelButton();
	WDA_HideProgressBar();
	var Title = WDA.Messages.SuccessTitle.replace('#1#',Count).replace('#2#',Succeed).replace('#3#',Failed);
	var Message = WDA.Messages.SuccessMessage.replace('#1#',Count).replace('#2#',Succeed).replace('#3#',Failed);
	WDA_ShowMessage(Title, Message);
}
function WDA_ActionError(ErrorType){
	WDA_EnableControls();
	WDA_HideCancelButton();
	WDA_HideProgressBar();
	WDA_ShowMessage(WDA.Messages.ErrorTitle, WDA.Messages.ErrorMessage, 'ERROR');
}
function WDA_ShowProgressBar() {
	$('#wda_progressbar').css('display','inline-block').find('.wda_bar');
}
function WDA_HideProgressBar() {
	$('#wda_progressbar').hide().find('.wda_bar').css('width','0%');
	$('#wda_progressbar').hide().find('.wda_text').text('');
	$('#wda_cancel').hide();
}
function WDA_SetProgressBarPosition(Index, Count) {
	var Percent = Index * 100 / Count;
	if (isNaN(Percent)) {
		Percent = 0;
	}
	Percent = Math.round(Percent);
	$('#wda_progressbar').find('.wda_bar').css('width',Percent+'%');
	$('#wda_progressbar').find('.wda_text').text(Index + ' / ' + Count);
}
function WDA_SetAdminUrl(URL){
	if (WdaPageLoaded) {
		window.history.pushState('','',URL);
	}
}
function WDA_GetCurPageParam(g,h){
	var d=window.location.href.split('#')[0],e="",a="",k=d.indexOf("?"),b=[],f={},c,m=0,l;-1<k?(e=d.substring(0,k),a=d.substring(k+1)):e=d;if(0<a.length)for(l in b=a.split("&"),b)if(b.hasOwnProperty(l)){a=b[l].split("=");c=a[0];a=a[1];Found=!1;for(Key1 in h)if(h.hasOwnProperty(Key1)&&h[Key1]==c){Found=!0;break}Found||(f[c]=a)}a="";for(c in f)f.hasOwnProperty(c)&&(m++,a+="&"+c+"="+f[c]);a=a.substring(1);0<g.length&&(a=0<a.length?a+("&"+g):g);b=e;if(0<m||0<a.length)b=e+"?"+a;return b
};
function WDA_LoadProfile(ProfileID){
	var Profile = window['WdaProfile'+ProfileID];
	if (typeof Profile == 'object') {
		$('#wda_select_iblock').val(Profile['IBLOCK_ID']);
		$('html,body').animate({scrollTop:$('#wda_settigns_edit_table').offset().top-50},500);
		WDA_OnChangeIBlockID($('#wda_select_iblock')[0],function(Select,IBlockID){
			// Load profile main code here
			$('#wda_select_sections').val(Profile['SECTIONS_ID'].split(','));
			if(Profile['WITH_SUBSECTIONS']=='Y') {
				$('#wda_include_subsections').attr('checked','checked');
			} else {
				$('#wda_include_subsections').removeAttr('checked');
			}
			//
			$('#wda_filters').find('.item').remove();
			// Заполнение блока с фильтрами
			var Filters = Profile['FILTER'].split('&');
			var D = {}, Indexes = {}, M;
			for(var i in Filters) {
				M = Filters[i].match(/^f_([e|p|v])([1|2])\[(\d+)\]=(.*?)$/)
				if(M!=null){
					if(D[M[1]+M[2]]==undefined) {
						D[M[1]+M[2]] = []
					}
					D[M[1]+M[2]][M[3]] = M[4];
					Indexes[M[3]] = 'Y';
				}
			}
			for (var i in Indexes) {
				var p1 = D['p1'][i],
						p2 = D['p2'][i],
						e1 = D['e1'][i],
						e2 = D['e2'][i],
						v1 = D['v1'][i],
						v2 = D['v2'][i];
				try{p1 = decodeURI(p1);}catch(e){}
				try{p2 = decodeURI(p2);}catch(e){}
				try{e1 = decodeURI(e1);}catch(e){}
				try{e2 = decodeURI(e2);}catch(e){}
				try{v1 = decodeURI(v1);}catch(e){}
				try{v2 = decodeURI(v2);}catch(e){}
				WDA_AddFilter([
					p1,
					p2,
					e1,
					e2,
					v1,
					v2
				]);
			}
			window.WdaProfileData = Profile;
			$('#wda_select_action').val(Profile['ACTION']);
			WDA_OnChangeAction($('#wda_select_action')[0],function(){
				var ActionParamsInputs = $('#wda_action_params').Deserialize(Profile['~PARAMS']).find('input[type=checkbox],input[type=text],input[type=radio],select').change().trigger('textchange');
				function CallbackEventHanlder(){
					ActionParamsInputs = $('#wda_action_params').Deserialize(Profile['~PARAMS']).find('input[type=checkbox],input[type=text],input[type=radio],select').not('[data-callback="wda_field_callback"]').change().trigger('textchange');
					BX.removeCustomEvent(window,'wda_field_callback',CallbackEventHanlder);
				}
				BX.addCustomEvent(window,'wda_field_callback',CallbackEventHanlder);
				if(typeof window['WDA_Params_Update_'+Profile['ACTION']] == 'function'){
					window['WDA_Params_Update_'+Profile['ACTION']].call();
				}
			});
		});
	}
}
$(document).ready(function(){
	$('#wda_select_sections').change(function(){
		Log('onWdaSectionChange');
		BX.onCustomEvent('onWdaSectionChange', [$(this)]);
		WDA_StartCheckTimeout();
	});
	$('#wda_include_subsections').change(function(){
		WDA_StartCheckTimeout();
	});
	$('#wda_form').submit(function(Event){
		Event.preventDefault();
	});
	$('#wda_select_iblock').change(function(){
		WDA_OnChangeIBlockID(this);
	});
	$('#wda_filters').delegate('.item','click',function(){
		$(this).fadeOut(200,function(){
			$(this).remove();
			if ($('#wda_filters').find('.item').length==0) {
				$('#filter_no_filters').show();
			}
			WDA_StartCheckTimeout();
		});
	});
	$('#wda_filter_add #wda_filter_param').change(function(){
		var PropType = $(this).find('option:selected').attr('data-type');
		if (PropType!=undefined) {
			var PropCode = $(this).val();
			if (PropType==undefined) PropType = '';
			var SelectEqual = $('#wda_filter_equal');
			var SelectEqualValue = SelectEqual.val();
			SelectEqual.find('option').not('[value=""]').remove();
			PropType = PropType.toUpperCase();
			var PropTypeArray = PropType.split(':');
			PropType1 = PropTypeArray[0]+(PropTypeArray[1]==undefined?'':':'+PropTypeArray[1]);
			var PropTypeShort = PropTypeArray[0];
			var ComparisonTypes = {};
			if (PropTypeArray.length==2 && WdaComparisonTypes[PropType1]!=undefined) {
				ComparisonTypes = WdaComparisonTypes[PropType1];
			} else if (PropTypeArray.length==2 && WdaComparisonTypes[PropTypeShort]!=undefined) {
				ComparisonTypes = WdaComparisonTypes[PropTypeShort];
			} else if (PropTypeArray.length==1 && WdaComparisonTypes[PropTypeShort]!=undefined) {
				ComparisonTypes = WdaComparisonTypes[PropTypeShort];
			}
			if (PropTypeShort=='L') {
				$('#wda_filter_value').attr('data-type','list');
				$('#wda_filter_value_list').show();
				$('#wda_filter_value_text').hide();
				var PropertyID = parseInt(PropCode.match(/PROPERTY_(\d+)/i)[1]);
				if (PropertyID>0) {
					WDA_LoadPropertyEnums(PropertyID);
				}
			} else if (PropTypeShort=='T') {
				$('#wda_filter_value').attr('data-type','list');
				$('#wda_filter_value_list').show();
				$('#wda_filter_value_text').hide();
				if (PropCode.match(/_TEXT_TYPE/i)) {
					WDA_LoadTextType();
				}
			} else {
				$('#wda_filter_value').attr('data-type','text');
				$('#wda_filter_value_list').hide();
				$('#wda_filter_value_text').show();
			}
			if (PropType1=='S:DATETIME') {
				$('#wda_filter_value .calendar-icon').show();
			} else {
				$('#wda_filter_value .calendar-icon').hide();
			}
			if (PropType1=='N:INT') {
				$('#wda_filter_value_text').removeAttr('data-float');
				$('#wda_filter_value_text').attr('data-int','Y');
			} else if (PropType1=='N' || PropType1=='P') {
				$('#wda_filter_value_text').removeAttr('data-int');		
				$('#wda_filter_value_text').attr('data-float','Y');
			} else {
				$('#wda_filter_value_text').removeAttr('data-int');
				$('#wda_filter_value_text').removeAttr('data-float');
			}
			$('#wda_filter_value_list').val('');
			$('#wda_filter_value_text').val('');
			for (var i in ComparisonTypes) {
				SelectEqual.append('<option value="'+i+'" data-value="'+ComparisonTypes[i].VALUE+'">'+ComparisonTypes[i].NAME+'</option>');
			}
			SelectEqual.val(SelectEqualValue).change();
		} else {
			$('#wda_filter_equal option').not('[value=""]').remove();
		}
	}).change();
	$('#wda_filter_add #wda_filter_equal').change(function(){
		var ShowValue = $(this).find('option:selected').attr('data-value')=='Y';
		if (ShowValue) {
			$('#wda_filter_value').show();
		} else {
			$('#wda_filter_value').hide();
		}
	});
	$('#wda_filter_add #wda_filter_value').delegate('input','keydown',function(Event){
		if (Event.which==13) {
			$('#wda_filter_add_button').click();
		}
	});
	$('#wda_form').delegate('input[type=text]','keypress',function(Event){
		var Event = Event || window.event;
		var Key = Event.keyCode || Event.which;
		Key = String.fromCharCode(Key);
		var Regex = false;
		if ($(this).attr('data-int')=='Y') {
			Regex = /[0-9]/;
		} else if ($(this).attr('data-float')=='Y') {
			Regex = /[0-9]|\./;
		}
		if (Regex!=false && !Regex.test(Key) || Key=='.' && $(this).val().indexOf('.')>-1) {
			Event.returnValue = false;
			if(Event.preventDefault) Event.preventDefault();
		}
	});
	$('#wda_filter_add input[type=button]').click(function(){
		var Param = $('#wda_filter_param');
		var Equal = $('#wda_filter_equal');
		var ValueText = $('#wda_filter_value_text');
		var ValueList = $('#wda_filter_value_list');
		var ValueType = $('#wda_filter_value').attr('data-type');
		var Param1 = Param.find('option:selected').attr('data-name');
		var Param2 = Param.val();
		var Equal1 = Equal.find('option:selected').text();
		var Equal2 = Equal.val();
		var Value1 = ValueType=='list' ? ValueList.find('option:selected').attr('data-name') : ValueText.val();
		var Value2 = ValueType=='list' ? ValueList.val() : ValueText.val();
		var ShowValue = Equal.find('option:selected').attr('data-value')=='Y';
		if (Param2.length==0 || Equal2.length==0) {
			if (ShowValue && $.trim(Value1)=='' || !ShowValue) {
				return false;
			}
		}
		WDA_AddFilter([Param1,Param2,Equal1,Equal2,Value1,Value2]);
		ValueText.val('').parent().hide();
		Equal.val('');
		Param.val('').change();
	});
	$('#wda_submit').click(function(){
		WDA_Submit();
	});
	$('#wda_select_action').change(function(){
		WDA_OnChangeAction(this);
	});
	$('#wda_select_action_refresh').on('click',function(){
		$('#wda_select_action').change();
	});
	$('#wda_form').delegate('#wda_cancel','click',function(){
		$(this).addClass('disabled').attr('disabled','disabled');
		WdaStop = true;
		if (WdaProcessRequest && WdaProcessRequest.readyState!=4) {
			WdaProcessRequest.abort();
		}
	});
	//
	WDA_StartCheckTimeout();
	if ($('#wda_select_iblock').val()!='') {
		$('#wda_select_iblock').change();
	} else if ($('#wda_select_action').val()!='') {
		$('#wda_select_action').change();
	}
	$.alt('X', function() {
		WDA_Submit();
	});
	WdaCheckTimeoutDelay = 150;
	WdaPageLoaded = true;
	// save profile
	$('#wda_save_profile').click(function(E){
		var Errors = [];
		var IBlockID = $('#wda_select_iblock').val();
		var Action = $('#wda_select_action').val();
		if(IBlockID.length==0) {
			Errors.push(WDA.Messages.ProfileSaveErrorEmptyIBlock);
		} else if (Action.length==0) {
			Errors.push(WDA.Messages.ProfileSaveErrorEmptyAction);
		}
		if(Errors.length==0) {
			var ProfileName = prompt(WDA.Messages.ProfileSavePrompt, WDA.Messages.ProfileSavePromptDefaultName);
			if(ProfileName!=null && ProfileName.length>0) {
				if(ProfileName.length>255) {
					ProfileName = ProfileName.substr(255);
				}
				$.ajax({
					url: WDA.Url + '?lang='+WDA.Lang+'&save_profile=Y',
					type: 'POST',
					data: $('#wda_form').serialize()+'&profile_name='+encodeURIComponent($.trim(ProfileName)),

					datatype: 'json',
					success: function(JSON) {
						//console.log(JSON); // ToDo
					}
				});
			}
		} else {
			alert(Errors.join('\n'));
		}
	});
});
var WdaCheckTimeout,
		WdaCheckTimeoutDelay,
		WdaStop = false,
		WdaCurrentAction = false,
		WdaProcessRequest = false,
		WdaCanSubmit,
		WdaPageLoaded;