(function ($) {
    'use strict';

    var 
			className = 'wda-filedrop',
			classNameLabel = className + '-label',
			classNameCaption = className + '-caption',
			classNameInput = className + '-input',
			classNameInputWrapper = classNameInput + '-wrapper',
			classNameWrapper = className + '-wrapper',
			classNameLoading = className + '-loading',
			classNameOver = className + '-over',
			classNameItems = className + '-items',
			classNameItem = className + '-item',
			classNameItemCellValue = classNameItem + '-cell-input',
			classNameItemCellDescr = classNameItem + '-cell-description',
			classNameItemCellButton = classNameItem + '-button',
			classNameItemInput = classNameItem + '-input',
			classNameItemDescription = classNameItem + '-description',
			classNameItemDelete = classNameItem + '-delete';

    //Extent jQuery.support to detect the support we need here
    $.support.fileDrop = (function () {
        return !!window.FileList;
    })();
		
    $.fn.fileDrop = function (optionsRaw) {

        return this.each(function(){
            var
							options = $.extend({}, $.fn.fileDrop.defaults, optionsRaw),
							dropWrapper = $(this),
							inProgressCount = 0,
							dropZone,
							dropZoneLabel,
							dropZoneCaption,
							dropZoneInputWrapper,
							dropZoneInput,
							dropItems,
							exitTimer;
    
						function stopEvent(e) {
							e.stopPropagation();
							e.preventDefault();
						}
						
						//Via: http://phpjs.org/functions/base64_decode/
						function base64_decode_(data) {
								/*jshint bitwise: false, eqeqeq:false*/
								var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
								var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
										ac = 0,
										dec = '',
										tmp_arr = [];

								if (!data) {
										return data;
								}

								data += '';

								do { // unpack four hexets into three octets using index points in b64
										h1 = b64.indexOf(data.charAt(i++));
										h2 = b64.indexOf(data.charAt(i++));
										h3 = b64.indexOf(data.charAt(i++));
										h4 = b64.indexOf(data.charAt(i++));

										bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

										o1 = bits >> 16 & 0xff;
										o2 = bits >> 8 & 0xff;
										o3 = bits & 0xff;

										if (h3 == 64) {
												tmp_arr[ac++] = String.fromCharCode(o1);
										} else if (h4 == 64) {
												tmp_arr[ac++] = String.fromCharCode(o1, o2);
										} else {
												tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
										}
								} while (i < data.length);

								dec = tmp_arr.join('');

								return dec;
						}

						function base64_decode(string) {
							var decoded;
							if($.isFunction(window.atob)){
								decoded = window.atob(string);
							}
							else{
								decoded = base64_decode_(string)
							}
							try {
								return decodeURIComponent(window.escape(decoded));
							}
							catch (exception) {
								return '';
							}
						}
						
						function bindEvents() {

								$(dropZone).bind('dragenter', function(e){
									stopEvent(e);
									$(dropZone).addClass(classNameOver);
								});

								$(dropZone).bind('dragover', function(e){
									stopEvent(e);
								});

								$(dropZone).bind('dragleave', function(e){
										stopEvent(e);
										clearTimeout(exitTimer);
										exitTimer = setTimeout(function() {
												$(dropZone).removeClass(classNameOver);
										}, 100);
								});

								$(dropZone).bind('drop', function(e, a){
										$(dropZone).removeClass(classNameOver);
										e.dataTransfer = e.dataTransfer ? e.dataTransfer : e.originalEvent.dataTransfer;
										if(e.dataTransfer){
											var fileList = e.dataTransfer.files;
											for (var i = 0; i <= fileList.length - 1; i++) {
												readFile(fileList[i]);
											}
										}
										stopEvent(e);
								});

								$(dropWrapper).bind('addcustomfile', function(e, file){
									addFileItem(file);
								});
						}

						function addFileItem(fileObject){
							var 
								divItems = $('.'+classNameItems, $(dropZone).parent()).first(),
								divItem = $('<div/>').addClass(classNameItem).appendTo(divItems),
								tableItem = $('<table/>').appendTo(divItem),
								tbodyItem = $('<tbody/>').appendTo(tableItem),
								trItem = $('<tr/>').appendTo(tbodyItem),
								tdValue = $('<td/>').appendTo(trItem).addClass(classNameItemCellValue),
									inputValue = $('<input name="'+options.inputNameValue+'" type="text"/>').val(fileObject.name)
										.appendTo(tdValue).addClass(classNameItemInput),
								tdDescr,
									inputDescr,
								tdButton,
									inputButton;
							if(options.withDescription){
								tdDescr = $('<td/>').appendTo(trItem).addClass(classNameItemCellDescr);
								inputDescr = $('<input name="'+options.inputNameDescr+'" type="text"/>').val(fileObject.description)
									.appendTo(tdDescr).addClass(classNameItemDescription);
							}
							tdButton = $('<td/>').appendTo(trItem).addClass(classNameItemCellButton);
							inputButton = $('<input type="button" value="&times;" />').appendTo(tdButton)
								.addClass(classNameItemDelete).bind('click', function(e){
									$(this).closest('.' + classNameItem).remove();
								});
							if(!options.multiple){
								divItem.siblings().remove();
							}
						}
						
						function readFile(file){
							var reader = new FileReader();
							$(reader).bind('load', function(e){
								checkLoading(true);
								$.ajax({
									url: options.ajaxUrl,
									type: 'post',
									data: {
										data: e.target.result,
										name: file.name,
										size: file.size,
										type: file.type,
										description: file.description
									},
									datatype: 'json',
									success: function(jsonResult) {
										checkLoading();
										if(jsonResult.File){
											addFileItem({
												name: jsonResult.File.Name,
												size: jsonResult.File.Size,
												type: jsonResult.File.Type
											});
										}
									},
									error: function(){
										checkLoading();
										alert('Error loading file \''+file.name+'\'');
									}
								});
							});
							reader.readAsDataURL(file);
						}
						
						function checkLoading(start){
								if(start) {
									inProgressCount++;
								}
								else{
									inProgressCount--;
								}
								if(inProgressCount == 0){
									dropWrapper.removeClass(classNameLoading);
								}
								else{
									dropWrapper.addClass(classNameLoading);
								}
							}
						
						if(dropWrapper.length && !dropWrapper.hasClass(dropWrapper)){
							dropWrapper = dropWrapper.addClass(classNameWrapper);
							dropZone = $('<div/>').addClass(className).appendTo(dropWrapper);
							dropZoneLabel = $('<label/>').addClass(classNameLabel).appendTo(dropZone);
							dropZoneCaption = $('<table>').addClass(classNameCaption).appendTo(dropZoneLabel)
								.append('<tbody><tr><td>'+options.caption+'</td></tr></tbody>');
							dropZoneInputWrapper = $('<div/>').addClass(classNameInputWrapper).appendTo(dropZoneLabel);
							dropZoneInput = $('<input/>').addClass(classNameInput).attr('type', 'file')
								.appendTo(dropZoneInputWrapper).bind('change', function(e){
									for(var i in this.files){
										if(typeof this.files[i] == 'object'){
											readFile(this.files[i]);
										}
									}
									this.value = '';
								});
							if(options.multiple){
								dropZoneInput.attr('multiple', 'multiple');
							}
							dropItems = $('<div/>').addClass(classNameItems).appendTo(dropWrapper);
							bindEvents(dropZone);
							if(options.files){
								for(var i in options.files){
									if(typeof options.files[i] == 'object'){
										addFileItem(options.files[i]);
									}
								}
							}
						}
						
        });
    };
		
    $.fn.fileDrop.defaults = {
			inputNameValue: 'value[]',
			inputNameDescr: 'description[]',
			caption: 'Drop files here or click',
			multiple: false,
			withDescription: false
		};

})(jQuery);