// *********************************************************************************************************************
// General
// *********************************************************************************************************************

/**
 *	Hot keys
 */
$.alt = function(Key, Callback) {
	$(document).keydown(function(E) {
		if(E.altKey && E.keyCode == Key.charCodeAt(0)) {
			return Callback.apply(this);
		}
	});
};

// Execute console [Alt+X]
$.alt('X', function() {
	var buttonStart = $('input[data-role="wda-button-start"]');
	if(buttonStart.length && !buttonStart.is('[disabled]')){
		buttonStart.trigger('click');
	}
	return false;
});

// Save profile [Alt+S]
$.alt('S', function() {
	wdaPopupSaveProfile.Open();
	return false;
});

// Search [Alt+F]
$.alt('F', function() {
	$('a[data-role="filter-results-link"]').trigger('click');
	return false;
});

// Load profile [Alt+O]
$.alt('O', function() {
	wdaPopupLoadProfile.Open();
	return false;
});

/**
 *	Add checking Array.isArray (check if object is array or not)
 */
if(typeof Array.isArray === 'undefined') {
  Array.isArray = function(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
  }
};

// *********************************************************************************************************************
// Functions
// *********************************************************************************************************************

/**
 *	Ajax-request general
 *	Examples:
 *	ajaxAction = 'change_iblock';
 *	ajaxAction = ['change_iblock', 'custom_subaction'];
 */
var wdaAjaxObjects = {};
function wdaAjax(ajaxAction, post, callbackSuccess, callbackError, hideLoader){
	var lang = phpVars.LANGUAGE_ID,
		profileId = $('input[data-role="profile-id"]').val(),
		entityType = $('div[data-role="entity-type"] input[type="radio"]:checked').val(),
		iblockId = $('select[data-role="iblock-id"]').val(),
		sectionId = $('input[data-role="iblock-sections-id"]').val(),
		selectSections = $('input[data-role="select-sections"]').is(':checked') ? 'Y' : 'N',
		maxDepth = $('select[data-role="sections-max-depth"]').val(),
		includeSubsections = $('input[data-role="include-subsections"]').is(':checked') ? 'Y' : 'N',
		pluginCode = $('select[data-role="plugin"]').val(),
		filterData = $('div[data-role="iblock-filter"] input[name="filter"]').val(),
		ajaxActionSub = '',
		ajaxActionFull = '',
		full = false,
		postTmp = [],
		ajax;
	//
	if(typeof post != 'object'){
		console.error('Variable post must be an object!');
		post = {};
	}
	if(!Array.isArray(post)){
		postTmp = [];
		for(var i in post){
			postTmp.push({
				name: i,
				value: post[i]
			});
		}
		post = postTmp;
	}
	postTmp = [];
	for(var i in post){
		if(typeof post[i] == 'object' && !Array.isArray(post[i]) && post[i].name == 'full' && post[i].value == 'Y'){
			full = true;
		}
		else{
			postTmp.push(post[i]);
		}
	}
	post = postTmp;
	if(full) {
		post.push({name: 'filter', value: filterData.length ? filterData : '-'});
		post.push({name: 'sections_id', value: sectionId.length ? sectionId : '-'});
	}
	//
	if(hideLoader!==true) {
		BX.showWait();
	}
	if($.isArray(ajaxAction)) {
		ajaxActionSub = ajaxAction[1];
		ajaxAction = ajaxAction[0];
	}
	ajaxActionFull = ajaxAction + (ajaxActionSub.length ? '_' + ajaxActionSub : '');
	if(wdaAjaxObjects[ajaxActionFull] && wdaAjaxObjects[ajaxActionFull].readyState != 4){
		wdaAjaxObjects[ajaxActionFull].abort();
	}
	ajax = $.ajax({
		url: wdaHttpBuildQuery(location.pathname, {
			profile_id: profileId,
			entity_type: entityType,
			iblock_id: iblockId,
			select_sections: selectSections,
			max_depth: maxDepth,
			include_subsections: includeSubsections,
			plugin_code: pluginCode,
			ajax_action: ajaxAction,
			ajax_action_sub: ajaxActionSub,
			lang: lang
		}),
		type: 'POST',
		data: post,
		datatype: 'json',
		success: function(arJson, textStatus, jqXHR){
			if(typeof callbackSuccess == 'function') {
				jqXHR._ajax_action = ajaxAction;
				callbackSuccess(jqXHR, textStatus, arJson);
			}
			if(arJson.DebugMessage){
				wdaPopupDebug.Open(arJson.DebugMessage);
			}
			else{
				wdaPopupDebug.Close();
			}
			if(typeof arJson != 'object'){
				wdaPopupError.Open(jqXHR);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			jqXHR._ajax_action = ajaxAction;
			if(jqXHR.statusText != 'abort') {
				console.error(errorThrown);
				console.error(textStatus);
				console.error(jqXHR);
				if(typeof callbackError == 'function') {
					callbackError(jqXHR, textStatus, errorThrown);
				}
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
	wdaAjaxObjects[ajaxActionFull] = ajax;
	return ajax;
}

/**
 *	Analog to php-function http_build_query()
 */
function wdaHttpBuildQuery(url, params){
	var query = Object.keys(params)
   .map(function(k) {return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);})
    .join('&');
	return url + (query.length ? (url.indexOf('?') == -1 ? '?' : '&') + query : '');
}

/**
 *	Analog to Bitrix CMain::getCurPageParam
 */
function wdaGetCurPageParam(strAdd, arRemove, bAtTheEnd){
	var arData = [];
		arDataTmp = [],
		arGetParts = location.search.substr(1).split('&'),
		strQuery = '';
	strAdd = typeof strAdd == 'string' ? strAdd : strAdd.toString();
	arRemove = typeof arRemove == 'object' ? arRemove : [arRemove];
	bAtTheEnd = bAtTheEnd === true ? true : false;
	for(var i in arGetParts){
		if(arGetParts.hasOwnProperty(i) && arGetParts[i].length){
			var item = arGetParts[i].split('=');
			arDataTmp.push({
				name: item[0],
				value: decodeURIComponent(item[1])
			});
		}
	}
	for(var i in arDataTmp){
		var strName = arDataTmp[i].name.split('[')[0],
			bDelete = false;
		for(var j in arRemove){
			if(arRemove[j] == strName){
				bDelete = true;
				break;
			}
		}
		if(!bDelete){
			arData.push(arDataTmp[i]);
		}
	}
	for(var i in arData){
		strQuery += '&' + arData[i].name + '=' + encodeURIComponent(arData[i].value);
	}
	strQuery = strQuery.substr(1);
	if(bAtTheEnd){
		strQuery = (strQuery.length ? strQuery + '&' : '') + strAdd;
	}
	else{
		strQuery = strAdd + (strQuery.length ? '&' + strQuery : '');
	}
	if(strQuery.substr(0, 1) == '&'){
		strQuery = strQuery.substr(1);
	}
	if(strQuery.length){
		strQuery = '?' + strQuery;
	}
	return location.href.split('?')[0] + strQuery;
}

/**
 *	Change url
 */
function wdaChangeUrl(key, value){
	if(document.readyState == 'complete') {
		value = (typeof value == 'number' && value > 0 || typeof value == 'string' && value.length 
			? key+'='+encodeURIComponent(value) : '');
		var newUrl = wdaGetCurPageParam(value, [key]);
		window.history.pushState('', '', newUrl);
		if(key == 'profile_id') {
			wdaChangeUrl('entity_type', null);
		}
	}
}

/**
 *	Set popup content and set height 100%
 */
function wdaSetPopupContent(popup, content){
	$(popup.PARTS.CONTENT_DATA).children('.bx-core-adm-dialog-content-wrap-inner').children().html(content);
	$('.bx-core-adm-dialog-content-wrap-inner', popup.DIV).css({
		'height': '100%',
		'-webkit-box-sizing': 'border-box',
			 '-moz-box-sizing': 'border-box',
						'box-sizing': 'border-box'
	}).children().css({
		'height': '100%'
	});
	$('input[type=text],input[type=email],input[type=password],textarea', popup.PARTS.CONTENT_DATA)
		.first().focus().each(function(){
			var length = $(this).val().length;
			this.setSelectionRange(length, length);
		});
}

/**
 *	Serialize all current actions/plugins to js object
 */
function wdaSerializePluginsData(){
	return $('div[data-role="plugin-serialize-container"] :input')
		.add($('div[data-role="task-settings-container"] :input')).serializeArray();
}

/**
 *	Execute
 */
function wdaExecute(start, resume){
	var	progressBar = $('[data-role="execute-progress-bar"]'),
		post = [];
	start: start ? true : false;
	resume: resume ? true : false;
	if(!$('div[data-role="plugin-settings"]').length){
		wdaShowMessage(BX.message('WDA_CANNOT_START_NO_ACTIONS'));
		return false;
	}
	if(start){
		if(!wdaCheckCanStart()){
			return false;
		}
		post = wdaSerializePluginsData();
		post.push({name: 'start', value: 'Y'});
		$('[data-role="wda-button-resume"]').hide();
	}
	post.push({name: 'full', value: 'Y'});
	if(start || resume){
		wdaEnableControls(false, false, false, true);
		wdaProgressBar(progressBar, 0);
	}
	window.wdaAjaxExecute = wdaAjax('execute', post, function(jqXHR, textStatus, arJson){
		wdaProgressBar(progressBar, arJson.Percent, [arJson.Index, arJson.Count]);
		if(arJson.Continue){
			wdaExecute(false);
			wdaProgressBar(progressBar, arJson.Percent, [arJson.Index, arJson.Count]);
		}
		else if(arJson.ErrorText != undefined){
			console.error(arJson);
			console.error(textStatus);
			console.error(jqXHR);
			wdaPopupError.Open(arJson.ErrorText);
			wdaEnableControls(true, true, true, false);
			wdaProgressBar(progressBar, false);
			$('[data-role="wda-button-resume"]').removeAttr('disabled');
		}
		else if(arJson.Success){
			wdaEnableControls(true, true, false, false);
			$('[data-role="wda-button-resume"]').hide();
			if(typeof arJson.ResultsHtml == 'string' && arJson.ResultsHtml.length) {
				wdaPopupResults.Open(arJson.ResultsHtml);
			}
			if(!arJson.Count){
				wdaProgressBar(progressBar);
			}
		}
		else {
			wdaEnableControls(true, true, false, false);
			wdaProgressBar(progressBar, false);
			wdaPopupError.Open(jqXHR);
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaEnableControls(true, true, false, false);
		wdaProgressBar(progressBar, false);
		wdaPopupError.Open(jqXHR);
	});
}

/**
 *	Disable/enable controls
 */
function wdaEnableControls(enableAll, enableStart, enableResume, enableStop){
	var controls = $('[data-disabling-control="true"]'),
		btnStart = $('[data-role="wda-button-start"]'),
		btnResume = $('[data-role="wda-button-resume"]'),
		btnStop = $('[data-role="wda-button-stop"]');
	//
	if(enableAll === true){
		controls.removeAttr('disabled');
	}
	else if(enableAll === false){
		controls.attr('disabled', 'disabled');
	}
	//
	if(enableStart === true){
		btnStart.removeAttr('disabled');
	}
	else if(enableStart === false){
		btnStart.attr('disabled', 'disabled');
	}
	//
	if(enableResume === true){
		btnResume.show().removeAttr('disabled');
	}
	else if(enableResume === false){
		btnResume.attr('disabled', 'disabled');
	}
	//
	if(enableStop === true){
		btnStop.removeAttr('disabled');
	}
	else if(enableStop === false){
		btnStop.attr('disabled', 'disabled');
	}
}

/**
 *	Stop process
 */
function wdaStop(){
	wdaEnableControls(true, true, true, false);
	if(window.wdaAjaxExecute){
		window.wdaAjaxExecute.abort();
	}
}

/**
 *	Show progress bar
 *	value is a percent (float)
 *	value = undefined => hide progress bar
 *	value = false => set error view
 */
function wdaProgressBar(progressBar, value, progress){
	if(progressBar.length) {
		if(!progressBar.children('div[data-role="progress-bar-inner"]').length){
			progressBar.append('<div class="wda-progress-bar-strip" data-role="progress-bar-inner" />');
		}
		if(!progressBar.children('div[data-role="progress-bar-text"]').length){
			progressBar.append('<div class="wda-progress-bar-text" data-role="progress-bar-text" />');
		}
		if(value === undefined){
			progressBar.removeClass('wda-progress-bar-visible');
		}
		else{
			progressBar.addClass('wda-progress-bar-visible').removeClass('wda-progress-bar-success');
			var progressInner = progressBar.children('div[data-role="progress-bar-inner"]'),
				progressText = progressBar.children('div[data-role="progress-bar-text"]');
			if(value === false){
				progressBar.addClass('wda-progress-bar-error');
			} else{
				value = parseFloat(value);
				if(isNaN(value) || value < 0){
					value = 0;
				}
				if(value > 100){
					value = 99.99;
				}
				progressBar.removeClass('wda-progress-bar-error')
				progressInner.css({width: value+'%'});
				if(Array.isArray(progress)){
					progressText.text(value.toFixed(2) + ' % (' + progress[0] + ' / ' + progress[1] + ')');
				}
				else{
					progressText.text(value.toFixed(2) + ' %');
				}
				if(value == 100){
					progressBar.addClass('wda-progress-bar-success');
				}
			}
		}
	}
}

/**
 *	Load saved profile
 */
function wdaLoadProfile(profileId, callback){
	var url = location.pathname + '?profile_id=' + profileId + '&lang=' + phpVars.LANGUAGE_ID;
	if(typeof callback == 'function'){
		callback(profileId);
	}
	location.href = url;
}

/**
 *	Save profile
 */
function wdaSaveProfile(id, name, code, sort, description, type, callback){
	var post = wdaSerializePluginsData();
	post.push({name: 'full', value: 'Y'});
	post.push({name: 'profile_id', value: id});
	post.push({name: 'profile_name', value: name});
	post.push({name: 'profile_code', value: code});
	post.push({name: 'profile_sort', value: sort});
	post.push({name: 'profile_description', value: description});
	post.push({name: 'profile_type', value: type});
	wdaAjax('profile_save', post, function(jqXHR, textStatus, arJson){
		if(arJson.Success){
			wdaChangeUrl('profile_id', arJson.ProfileId);
			wdaChangeUrl('iblock_id', null);
			wdaChangeUrl('plugin', null);
			$('input[data-role="profile-id"]').val(arJson.ProfileId);
			if(typeof arJson.ProfileTitle == 'string'){
				wdaSetPageProfileTitle(arJson.ProfileTitle);
			}
			if(typeof callback == 'function'){
				callback();
			}
		}
		else if (arJson.ErrorText != undefined && arJson.ErrorText.length){
			wdaPopupError.Open(arJson.ErrorText);
		}
		else if (arJson.MessageText != undefined && arJson.MessageText.length){
			wdaShowMessage(arJson.MessageText);
		}
		else{
			wdaPopupError.Open(jqXHR);
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}

/**
 *	Scroll fix for set button in visible area
 */
function wdaScrollFix(){
	$(document).scrollTop($(document).scrollTop()+1);
	$(document).scrollTop($(document).scrollTop()-1);
}

/**
 *	Set sort to pinner plugins
 */
function wdaSortPlugins(){
	var sort = 0;
	$('div[data-role="plugin-settings"]').each(function(){
		sort++;
		$('input[data-role="fieldset-sort"]', this).val(sort);
	});
}

/**
 *	Generate random string (length = 32)
 */
function wdaGenerateString(){
	var result = '',
		length = 32,
		characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	for (var i=0; i<length; i++) {
		result += characters.charAt(Math.floor(Math.random() * characters.length));
	}
	return result;
}

/**
 *	Get plugin code by id
 */
function getPlugin(id){
	return $('#'+id).closest('div[data-role="plugin-settings"]').find('input[data-role="fieldset-plugin"]').val();
}

/**
 *	Stylize checkboxes
 */
function wdaReplaceCheckboxes(div){
	$('input[type="checkbox"]', div).each(function(){
		BX.adminFormTools.modifyCheckbox(this);
	});
}

/**
 *	jQuery select2
 */
function wdaSelect2(select, config){
	var select2 = $(select);
	if(!select2.hasClass('select2-hidden-accessible')){
		// Prepare
		select2.parent().css('position', 'relative');
		// Get width
		var div = $('<div/>').css({height: '0', overflow: 'hidden', width: screen.width}).appendTo($('body')),
			selectTmp = select2.clone().removeAttr('id').appendTo(div),
			width = selectTmp.width();
		selectTmp.remove();
		// Config
		config = $.extend({}, {
			dropdownAutoWidth: true,
			dropdownParent: select2.parent(),
			language: phpVars.LANGUAGE_ID,
			matcher:function(params, item){
				function optionIsMatch(option, search){
					return [option.text, option.id, option.title].join(' ').toUpperCase().indexOf(searchText) != -1;
				}
				if(params.term == undefined || !params.term.length){
					return item;
				}
				if(item.id == ''){
					return null;
				}
				var searchText = params.term.toUpperCase();
				if(item.element.tagName.toLowerCase() == 'option'){
					if(optionIsMatch(item.element, searchText)) {
						return item;
					}
				}
				else if(item.element.tagName.toLowerCase() == 'optgroup' && Array.isArray(item.children)){
					var filteredOptions = [];
					$.each(item.children, function (index, option) {
						if(optionIsMatch(option, searchText)) {
							filteredOptions.push(option);
						}
					});
					if(filteredOptions.length) {
						var modifiedItem = $.extend({}, item, true);
						modifiedItem.children = filteredOptions;
						return modifiedItem;
					}
				}
				return null;
			}
		}, config);
		select2.select2(config);
		select2.next('.select2').css({'min-width': width + 10, 'max-width': '600px'});
	}
	return select2;
}

/**
 *	
 */
function wdaGetUserTitle(userId, container, mode){
	if(mode == undefined){
		mode = '';
	}
	var
		post = {
			user_id: userId,
			user_title_mode: mode
		};
	wdaAjax('get_user', post, function(jqXHR, textStatus, arJson){
		if(arJson.Success){
			$(container).html(arJson.UserTitle);
		}
		else{
			wdaPopupError.Open(jqXHR);
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	}, true);
}

/**
 *	Handler on start process
 *	Must return true on success, or <string> on error with message, or false on error without message
 */
function wdaOnStartHandler(id, callback){
	if(!window.wdaOnStartHandlers){
		window.wdaOnStartHandlers = [];
	}
	window.wdaOnStartHandlers.push({
		id: id,
		callback: callback
	});
}

/**
 *	Can the process be started?
 *	@returns boolean
 */
function wdaCheckCanStart(){
	var canStart = true,
		messageHeader = BX.message('WDA_CANNON_START'),
		messageBody = '';
	if(Array.isArray(window.wdaOnStartHandlers)){
		var id,
			callback,
			div,
			title,
			callbackResult;
		for(var i in window.wdaOnStartHandlers){
			id = window.wdaOnStartHandlers[i].id;
			callback = window.wdaOnStartHandlers[i].callback;
			if(typeof callback == 'function'){
				div = $('div[data-role="plugin-settings"][data-id="'+id+'"]');
				title = $.trim($('div[data-role="wda-fieldset"] [data-role="wda-fieldset-toggle"]', div).text());
				if(div.length){
					callbackResult = callback(id, div, title);
					if(callbackResult !== true){
						//messageBody += "\n" + '- ' + title + (typeof callbackResult == 'string' ? ': ' + callbackResult : '');
						messageBody += "\n" + '- ' + '"' + title+ '"' + 
							(typeof callbackResult == 'string' ? ': ' + "\n" + callbackResult : '');
						canStart = false;
					}
				}
			}
		}
	}
	if(messageBody.length){
		alert(messageHeader + messageBody);
	}
	return canStart;
}

/**
 *	Show message
 */
function wdaShowMessage(message){
	alert(message);
}

/**
 *	Show section include subsections notice (for non-D7)
 */
function wdaShowSectionIncludeSubsectionsNotice(){
	let show = false;
	if($('input[data-role="include-subsections"]').prop('checked')){
		if($('div[data-role="entity-type"] input[type="radio"][name="entity-type"][value="section"]').prop('checked')){
			show = true;
		}
	}
	$('div[data-role="wda_section_include_subsections_notice"]').toggle(show);
}

/**
 *	Set current profile title
 */
function wdaSetPageProfileTitle(title){
	let
		panel = $('#wda_context_button_profiles').closest('.adm-detail-toolbar'),
		role = 'wda_page_profile_title',
		className = 'wda-page-plugin-title',
		span = panel.children('span[data-role="'+role+'"]');
	if(!span.length){
		span = $('<span data-role="'+role+'" class="'+className+'" />');
		panel.append(span);
	}
	if(title.length){
		title = BX.message('WDA_PAGE_PROFILE_TITLE') + ' ' + title;
	}
	span.html(title);
}

function wdaHideNotSelectedSections(flag){
	let
		select = $('select[data-role="iblock-sections-id"]');
	select.children('span').children('option').unwrap();
	if(flag){
		select.children('option').not(':selected').wrap('<span style="display:none">');
	}
	$('a[data-role="wda_sections_select"]').filter('[data-select="show"],[data-select="shown"]')
		.attr('data-select', flag ? 'shown' : 'show').toggleClass('adm-btn-active', flag);
}

// *********************************************************************************************************************
// Popups
// *********************************************************************************************************************

/**
 *	POPUP: save to profile
 */
var wdaPopupSaveProfile;
wdaPopupSaveProfile = new BX.CDialog({
	ID: 'wdaPopupSaveProfile',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 300,
	width: 500
});
wdaPopupSaveProfile.Open = function(){
	this.SetTitle(BX.message('WDA_POPUP_PROFILE_SAVE_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
wdaPopupSaveProfile.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupSaveProfile.LoadContent = function(){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'profile_save'}
		];
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	//
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		$('form', thisPopup.PARTS.CONTENT_DATA).bind('submit', function(e){
			e.preventDefault();
			$('#wda_profile_save_save').trigger('click');
		});
		thisPopup.SetNavButtons();
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaPopupSaveProfile.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		this.SetButtons(
			[{
				'name': BX.message('WDA_POPUP_SAVE'),
				'id': 'wda_profile_save_save',
				'className': 'adm-btn-green',
				'action': function(){
					var thisPopup = this.parentWindow,
						content = thisPopup.PARTS.CONTENT_DATA,
						id = $('input[data-role="profile-id"]', content).val(),
						name = $('input[data-role="profile-save-name"]', content).val(),
						code = $('input[data-role="profile-save-code"]', content).val(),
						sort = $('input[data-role="profile-save-sort"]', content).val(),
						description = $('textarea[data-role="profile-save-description"]', content).val(),
						type = $('div[data-role="profile-save-type"] input[type="radio"]:checked', content).val();
					wdaSaveProfile(id, name, code, sort, description, type, function(){
						thisPopup.Close();
					});
				}
			}, {
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_profile_save_cancel',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
		container.append('<div style="clear:both"/>');
	}
}
$(document).delegate('form[data-role="wdi_profile_save_form"]', 'submit', function(e){
	e.preventDefault();
	$('input[name="wda_profile_save_save"]').trigger('click');
});

/**
 *	POPUP: load from profile
 */
var wdaPopupLoadProfile;
wdaPopupLoadProfile = new BX.CDialog({
	ID: 'wdaPopupLoadProfile',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 400,
	width: 960
});
wdaPopupLoadProfile.Open = function(){
	this.SetTitle(BX.message('WDA_POPUP_PROFILE_LOAD_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
wdaPopupLoadProfile.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupLoadProfile.LoadContent = function(){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'profile_load'}
		];
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	//
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		$('.adm-list-table-cell-sort-initial').trigger('click').trigger('click');
		thisPopup.SetNavButtons();
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaPopupLoadProfile.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		this.SetButtons(
			[{
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_profile_load_cancel',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
		container.append('<span data-role="wda-popup-profile-load-notice"><b>'+BX.message('WDA_POPUP_PROFILE_LOAD_NOTICE')+'</b></span>');
		container.append('<div style="clear:both"/>');
	}
}

/**
 *	POPUP: preview
 */
var wdaPopupPreview;
wdaPopupPreview = new BX.CDialog({
	ID: 'wdaPopupPreview',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 300,
	width: 800
});
wdaPopupPreview.Open = function(){
	this.SetTitle(BX.message('WDA_POPUP_PREVIEW_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
wdaPopupPreview.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupPreview.LoadContent = function(){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'preview'},
			{name: 'full', value: 'Y'},
		];
	//
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		thisPopup.SetNavButtons(false, arJson.ShowBy);
		var summaryNew = $('div[data-role="preview-summary"]', thisPopup.PARTS.CONTENT_DATA),
			summaryOld = $('div[data-role="preview-summary"]', thisPopup.PARTS.BUTTONS_CONTAINER).html('');
		if(summaryNew.length){
			summaryOld.html(summaryNew.html());
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaPopupPreview.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('').attr('data-role', 'preview-buttons');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		this.SetButtons(
			[{
				'Button': function(parentWindow){
					return $('<div data-role="preview-summary" class="wda-preview-summary"/>').get(0);
				}
			}, {
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_profile_preview_cancel',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
		container.append('<div style="clear:both"/>');
	}
}
/**
 *	POPUP: save to profile
 */
var wdaPopupProfileCron;
wdaPopupProfileCron = new BX.CDialog({
	ID: 'wdaPopupProfileCron',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 350,
	width: 900
});
wdaPopupProfileCron.Open = function(profileId){
	this.SetTitle(BX.message('WDA_POPUP_PROFILE_CRON_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent(profileId);
}
wdaPopupProfileCron.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupProfileCron.LoadContent = function(profileId){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'profile_cron'},
			{name: 'profile_id', value: profileId}
		];
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	//
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		thisPopup.SetNavButtons();
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaPopupProfileCron.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		var buttons = [];
		if(!$('input[data-role="cron-tasks-cannot-autoset"]', this.PARTS.CONTENT_DATA).length){
			buttons.push({
				'name': BX.message('WDA_POPUP_SAVE'),
				'id': 'wda_profile_cron_save',
				'className': 'adm-btn-green',
				'action': function(){
					var thisPopup = this.parentWindow,
						content = thisPopup.PARTS.CONTENT_DATA,
						profileId = $('input[data-role="cron-tasks-profile-id"]', thisPopup.PARTS.CONTENT_DATA).val(),
						post = $(':input', thisPopup.PARTS.CONTENT_DATA).not('.adm-list-table-row-hidden :input').serializeArray();
					post.push({name: 'profile_id', value: profileId});
					wdaAjax('cron_tasks_save', post, function(jqXHR, textStatus, arJson){
						if(arJson.Success){
							thisPopup.Close();
							wdaPopupLoadProfile.LoadContent();
						}
						else if (arJson.ErrorText != undefined && arJson.ErrorText.length){
							wdaPopupError.Open(arJson.ErrorText);
						}
						else{
							wdaPopupError.Open(jqXHR);
						}
					}, function(jqXHR, textStatus, errorThrown){
						wdaPopupError.Open(jqXHR);
					});
				}
			});
			buttons.push({
			'name': BX.message('WDA_POPUP_ADD_MORE'),
			'id': 'wda_profile_cron_add_more',
			'action': function(){
				var id = wdaGenerateString(),
					tasks = $(this.btn).closest('div.bx-core-window')
						.find('div[data-role="cron-tasks"] table.adm-list-table tbody'),
					classRowHidden = 'adm-list-table-row-hidden',
					rowHidden = tasks.children('tr.'+classRowHidden);
				rowNewHtml = rowHidden.clone().removeClass(classRowHidden).get(0).outerHTML;
				rowNewHtml = rowNewHtml.replace(/__EXTERNAL_ID__/g, id);
				tasks.find('tr[data-role="cron-tasks-line-empty"]').before(rowNewHtml);
			}
		});
		}
		buttons.push({
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_profile_cron_cancel',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			});
		this.SetButtons(buttons);
		container.append('<div style="clear:both"/>');
	}
}

/**
 *	POPUP: error text
 */
var wdaPopupError;
wdaPopupError = new BX.CDialog({
	ID: 'wdaPopupError',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 400,
	width: 1000
});
wdaPopupError.Open = function(error){
	this.SetTitle(BX.message('WDA_POPUP_ERROR_TITLE'));
	this.SetNavButtons();
	this.Show();
	this.LoadContent(error);
}
wdaPopupError.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupError.LoadContent = function(error){
	if(typeof error == 'object'){
		var jqXHR = error;
		error = jqXHR.responseText.replace(/<pre>/g, '<pre class="wda-error-text">');
		if(!error.length){
			var statusText = jqXHR.statusText;
			if(statusText == 'OK'){
				statusText = BX.message('WDA_UNKNOWN_ERROR');
			}
			error = '<pre class="wda-error-text">'+statusText+'</pre>'
		}
	}
	this.SetContent(error);
}
wdaPopupError.SetNavButtons = function(){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('<input type="button" value="0" style="visibility:hidden;" />');
	this.SetButtons(
		[{
			'name': BX.message('WDA_POPUP_CLOSE'),
			'id': 'wda_error_close',
			'className': 'wda-button-right',
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	);
	container.append('<div style="clear:both"/>');
}

/**
 *	POPUP: debug text
 */
var wdaPopupDebug;
wdaPopupDebug = new BX.CDialog({
	ID: 'wdaPopupDebug',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 400,
	width: 1000
});
wdaPopupDebug.Open = function(error){
	this.SetTitle(BX.message('WDA_POPUP_DEBUG_TITLE'));
	this.SetNavButtons();
	this.Show();
	this.LoadContent(error);
}
wdaPopupDebug.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupDebug.LoadContent = function(error){
	if(typeof error == 'object'){
		var jqXHR = error;
		error = jqXHR.responseText.replace(/<pre>/g, '<pre class="wda-error-text">');
		if(!error.length){
			error = '<pre class="wda-error-text">'+jqXHR.statusText+'</pre>'
		}
	}
	this.SetContent(error);
}
wdaPopupDebug.SetNavButtons = function(){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('<input type="button" value="0" style="visibility:hidden;" />');
	this.SetButtons(
		[{
			'name': BX.message('WDA_POPUP_CLOSE'),
			'id': 'wda_debug_close',
			'className': 'wda-button-right',
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	);
	container.append('<div style="clear:both"/>');
}

/**
 *	POPUP: results text
 */
var wdaPopupResults;
wdaPopupResults = new BX.CDialog({
	ID: 'wdaPopupResults',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 300,
	width: 600
});
wdaPopupResults.Open = function(error){
	this.SetTitle(BX.message('WDA_POPUP_RESULTS_TITLE'));
	this.SetNavButtons();
	this.Show();
	this.LoadContent(error);
}
wdaPopupResults.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaPopupResults.LoadContent = function(html){
	this.SetContent(html);
}
wdaPopupResults.SetNavButtons = function(){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('<input type="button" value="0" style="visibility:hidden;" />');
	this.SetButtons(
		[{
			'name': BX.message('WDA_POPUP_CLOSE'),
			'id': 'wda_profile_results_cancel',
			'className': 'wda-button-right',
			'action': function(){
				this.parentWindow.Close();
			}
		}]
	);
	container.append('<div style="clear:both"/>');
}

/**
 *	POPUP: support
 */
var wdaHelp;
wdaHelp = new BX.CDialog({
	ID: 'wdaHelp',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 320,
	width: 700
});
wdaHelp.Open = function(entity, pluginCode){
	this.entity = entity;
	this.pluginCode = pluginCode;
	this.SetTitle(BX.message('WDA_POPUP_HELP_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
wdaHelp.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaHelp.LoadContent = function(){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'help'},
			{name: 'help_entity', value: this.entity},
			{name: 'help_plugin', value: this.pluginCode}
		];
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	//
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		thisPopup.SetTitle(BX.message('WDA_POPUP_HELP_TITLE_2').replace(/#PLUGIN_NAME#/, arJson.PluginName));
		$('select', thisPopup.PARTS.CONTENT_DATA).each(function(){
			wdaSelect2($(this), {
				dropdownParent: $(this).parent()
			});
		});
		thisPopup.SetNavButtons();
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaHelp.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		this.SetButtons(
			[{
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_help_close',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
		container.append('<div style="clear:both"/>');
	}
}

/**
 *	POPUP: support
 */
var wdaSupport;
wdaSupport = new BX.CDialog({
	ID: 'wdaSupport',
	title: '',
	content: '',
	resizable: true,
	draggable: true,
	height: 320,
	width: 500
});
wdaSupport.Open = function(){
	this.SetTitle(BX.message('WDA_POPUP_SUPPORT_TITLE'));
	this.SetNavButtons(true);
	this.Show();
	this.LoadContent();
}
wdaSupport.SetTitle = function(title){
	$('.bx-core-adm-dialog-head-inner', this.PARTS.TITLEBAR).html(title);
}
wdaSupport.LoadContent = function(){
	var thisPopup = this,
		post = [
			{name: 'popup_id', value: 'support'}
		];
	this.SetContent(BX.message('WDA_POPUP_LOADING'));
	//
	wdaAjax('load_popup', post, function(jqXHR, textStatus, arJson){
		wdaSetPopupContent(thisPopup, arJson.Html);
		$('select', thisPopup.PARTS.CONTENT_DATA).each(function(){
			wdaSelect2($(this), {
				dropdownParent: $(this).parent()
			});
		});
		thisPopup.SetNavButtons();
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
}
wdaSupport.SetNavButtons = function(empty){
	var container = $(this.PARTS.BUTTONS_CONTAINER);
	container.html('');
	if(empty) {
		container.html('<input type="button" value="0" style="visibility:hidden;" />');
	}
	else{
		this.SetButtons(
			[{
				'name': BX.message('WDA_POPUP_SEND'),
				'id': 'wda_support_send',
				'className': 'adm-btn-green',
				'action': function(){
					var thisPopup = this.parentWindow,
						content = thisPopup.PARTS.CONTENT_DATA,
						post = $(':input', thisPopup.PARTS.CONTENT_DATA).not('.adm-list-table-row-hidden :input').serializeArray();
					wdaAjax('support_send', post, function(jqXHR, textStatus, arJson){
						if(arJson.Success){
							alert(arJson.SuccessMessage);
							thisPopup.Close();
						}
						else if(arJson.ErrorMessage){
							wdaPopupError.Open(arJson.ErrorMessage);
						}
						else{
							wdaPopupError.Open(jqXHR);
						}
					}, function(jqXHR, textStatus, errorThrown){
						wdaPopupError.Open(jqXHR);
					});
				}
			}, {
				'name': BX.message('WDA_POPUP_CLOSE'),
				'id': 'wda_support_close',
				'className': 'wda-button-right',
				'action': function(){
					this.parentWindow.Close();
				}
			}]
		);
		container.append('<div style="clear:both"/>');
	}
}

// *********************************************************************************************************************
// Form Controls
// *********************************************************************************************************************

/**
 *	Fieldset collapse/expand
 */
$(document).delegate('div[data-role="wda-fieldset"] a[data-role="wda-fieldset-toggle"]', 'click', function(e){
	e.preventDefault();
	var fieldset = $(this).closest('div[data-role="wda-fieldset"]'),
		className = 'wda-fieldset-collapsed';
	fieldset.toggleClass('wda-fieldset-collapsed');
	$('input[data-role="fieldset-collapsed"]', fieldset).val(fieldset.hasClass(className) ? 'Y' : 'N');
	wdaScrollFix();
});

/**
 *	On change entity type
 */
$(document).delegate('div[data-role="entity-type"] input[type="radio"]', 'change', function(e, params){
	var div = $(this).closest('div[data-role="entity-type"]'),
		checkedInput = $('input[type="radio"]:checked', div),
		value = checkedInput.val(),
		post = [],
		isInitial = params && params.initial,
		optionRootLevel = $('select[data-role="iblock-sections-id"] option[value="0"]');
	if(!isInitial){
		wdaAjax('change_entity_type', post, function(jqXHR, textStatus, arJson){
			$('div[data-role="iblock-filter"]').html(arJson.FilterHtml);
			$('div[data-role="plugin-list-pinned"] div[data-role="plugin-settings"]').remove();
			$('div[data-role="wda-wrapper" ]').removeClass('wda-type-element wda-type-section')
				.addClass('wda-type-'+value);
			$('select[data-role="plugin"]').val('').trigger('change').trigger('pinchange')
				.html(arJson.PluginsHtml);
			$('select[data-role="plugin"]').trigger('change');
		}, function(jqXHR, textStatus, errorThrown){
			wdaPopupError.Open(jqXHR);
		});
		wdaChangeUrl('entity_type', value);
		wdaChangeUrl('plugin', null);
	}
	$('div[data-role="wda_filter_section_restrictions"]').toggle($(this).val() == 'section');
	$('div[data-role="wda_section_subsections_notice"]').toggle($(this).val() == 'section');
	if($(this).val() == 'section'){
		optionRootLevel.prop('selected', false);
		if(!optionRootLevel.parent().is('span')){
			optionRootLevel.wrap('<span style="display"none"/>'); // cross-browser hide (also for Safari)
		}
	}
	else{
		if(optionRootLevel.parent().is('span')){
			optionRootLevel.unwrap();
		}
	}
});

/**
 *	On change 'IBlock ID'
 */
$(document).delegate('select[data-role="iblock-id"]', 'change', function(e, data){
	var
		iblockSelect = $(this),
		iblockId = iblockSelect.val(),
		post = [
			{name: 'select_sections', value: $('input[data-role="select-sections"]').is(':checked') ? 'Y' : 'N'},
			{name: 'selected_sections_id', value: $('select[data-role="iblock-sections-id"]').val()},
			{name: 'previous_iblock_id', value: iblockSelect.attr('data-iblock-id')},
			{name: 'previous_filter_data', value: $('div[data-role="iblock-filter"] > input[name="filter"]').val()},
		],
		pluginList = $('div[data-role="plugin-list-pinned"]'),
		linkTaskSort = $('a[data-role="task-sort"]'),
		justChangeDepth = data && data.just_change_depth;
	if(linkTaskSort.hasClass('wda-task-sorting')){
		linkTaskSort.trigger('click');
	}
	if(!justChangeDepth) {
		pluginList.children('div[data-role="plugin-settings"]').remove();
	}
	if(!justChangeDepth){
		$('select[data-role="plugin"]').val('').trigger('change').trigger('pinchange');
	}
	wdaAjax('change_iblock', post, function(jqXHR, textStatus, arJson){
		$('select[data-role="iblock-sections-id"]').html(arJson.IBlockSectionsHtml).trigger('change');
		if(!justChangeDepth) {
			$('div[data-role="iblock-filter"]').html(arJson.FilterHtml);
			iblockSelect.attr('data-iblock-id', iblockSelect.val());
			$('option[value=""]', iblockSelect).remove();
			$('.wda-hide-all').removeClass('wda-hide-all');
			if(typeof arJson.MaxSectionDepth != 'undefined'){
				$('select[data-role="sections-max-depth"]').each(function(){
					var value = $(this).val();
					$('option', this).remove();
					for(var i = 1; i <= arJson.MaxSectionDepth; i++){
						$('<option>').attr('value', i).text(i).appendTo(this);
					}
					$(this).val(value);
					if(!$(this).val()){
						$(this).val($('option', this).last().val());
					}
				});
			}
			wdaChangeUrl('iblock_id', iblockId);
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	});
});

/**
 *	On change checkbox 'i want select sections'
 */
$(document).delegate('input[data-role="select-sections"]', 'change', function(e){
	e.preventDefault();
	if($(this).is(':checked')){
		$('tr[data-role="row-sections"]').show();
		$('span[data-role="sections-max-depth-wrapper"]').show();
	}
	else{
		$('tr[data-role="row-sections"]').hide();
		$('span[data-role="sections-max-depth-wrapper"]').hide();
	}
	$('select[data-role="iblock-sections-id"]').trigger('change');
	wdaScrollFix();
});

/**
 *	On change select max depth
 */
$(document).delegate('select[data-role="sections-max-depth"]', 'change', function(e){
	$('select[data-role="iblock-id"]').trigger('change', {just_change_depth: true});
});

/**
 *	Transform selected sections id to one line [with comma separated values]
 */
$(document).delegate('select[data-role="iblock-sections-id"]', 'change', function(e){
	var value = $(this).val(),
		entityType = $('div[data-role="entity-type"] input[type="radio"]:checked').val();
	if(!Array.isArray(value)){
		value = [];
	}
	$(this).parent().find('input[data-role="iblock-sections-id"]')
		.val($.map(value, function(value){return value === '' ? null : value;}).join(','));
	if(Array.isArray(value) && value.length == 1 && value[0] == '' || !$('input[data-role="select-sections"]').is(':checked')){
		$('span[data-role="include-subsections-wrapper"]').hide();
	}
	else{
		$('span[data-role="include-subsections-wrapper"]').show();
	}
	$('div[data-role="iblock-filter"] input[name="filter"]').trigger('change');
	wdaHideNotSelectedSections($('span[data-role="wda_sections_select_checkbox"] input[type=checkbox]').prop('checked'));
	if(entityType == 'element' && value.length == 1 && value[0] == ''){
		$('input[data-role="include-subsections"]').prop('checked', false);
	}
});

/**
 *	On change plugin
 */
$(document).delegate('select[data-role="plugin"]', 'change', function(e){
	var selectedPlugin = $(this).val(),
		ajaxContainer = $('div[data-role="plugin-settings-ajax"]'),
		post = [{name: 'full', value: 'Y'}];
	if(selectedPlugin.length){
		wdaAjax('change_plugin', post, function(jqXHR, textStatus, arJson){
			ajaxContainer.html(arJson.PluginSettingsHtml);
			wdaReplaceCheckboxes(ajaxContainer);
			wdaSortPlugins();
			wdaScrollFix();
		}, function(jqXHR, textStatus, errorThrown){
			wdaPopupError.Open(jqXHR);
		});
		wdaChangeUrl('plugin', selectedPlugin.toLowerCase());
	}
	else{
		ajaxContainer.html(ajaxContainer.attr('data-empty'));
		wdaChangeUrl('plugin', null);
	}
});

/**
 *	Reload plugin
 */
$(document).delegate('input[data-role="plugin-reload"]', 'click', function(e){
	e.preventDefault();
	$('select[data-role="plugin"]').trigger('change');
});

/**
 *	Change 'INCLUDE_SUBSECTIONS'
 */
$(document).delegate('input[data-role="include-subsections"]', 'change', function(e){
	e.preventDefault();
	$('div[data-role="iblock-filter"] input[name="filter"]').trigger('change');
	wdaShowSectionIncludeSubsectionsNotice();
});

/**
 *	Helper event
 */
$(document).delegate('select[data-role="plugin"]', 'pinchange', function(e){
	e.preventDefault();
	var rowTask = $('tr[data-role="row-task"]'),
		rowTaskHeader = rowTask.prev(),
		divPinned = $('div[data-role="plugin-list-pinned"]'),
		linkSort = $('a[data-role="task-sort"]'),
		children = divPinned.children('div[data-role="plugin-settings"]'),
		empty = !children.length;
	if(!empty){
		rowTaskHeader.show();
		rowTask.show();
	}
	else{
		rowTaskHeader.hide();
		rowTask.hide();
		
	}
	if(children.length > 1){
		linkSort.show();
	}
	else{
		linkSort.hide();
	}
	if(linkSort.hasClass('wda-task-sorting')){
		linkSort.trigger('click');
	}
	wdaSortPlugins();
	wdaScrollFix();
});

/**
 *	On plugin help
 */
$(document).delegate('a[data-role="plugin-settings-help"]', 'click', function(e){
	e.preventDefault();
	var divSettings = $(this).closest('div[data-role="plugin-settings"]'),
		entity = $('div[data-role="entity-type"] input[type="radio"]:checked').val(),
		pluginCode = divSettings.attr('data-plugin'),
		maxLength = 50;
	wdaHelp.Open(entity, pluginCode);
});

/**
 *	On plugin rename
 */
$(document).delegate('a[data-role="plugin-settings-rename"]', 'click', function(e){
	e.preventDefault();
	var divSettings = $(this).closest('div[data-role="plugin-settings"]'),
		linkPluginTitle = $('a[data-role="wda-fieldset-toggle"]', divSettings),
		inputPluginTitle = $('input[data-role="fieldset-title"]', divSettings),
		title = prompt($(this).attr('data-prompt'), inputPluginTitle.val()),
		maxLength = 50;
	if(title !== null){
		title = title.substr(0, maxLength);
		if(!title.length){
			var plugin = $('input[data-role="fieldset-plugin"]', divSettings).val();
			if(plugin.length){
				plugin = $('select[data-role="plugin"] option[value="'+plugin+'"]').text();
				if(plugin.length){
					title = plugin;
				}
			}
		}
		linkPluginTitle.text(title);
		inputPluginTitle.val(title);
	}
});

/**
 *	On plugin pin
 */
$(document).delegate('a[data-role="plugin-settings-pin"]', 'click', function(e){
	e.preventDefault();
	var divSettings = $(this).closest('div[data-role="plugin-settings"]'),
		divUnpinned = $('div[data-role="plugin-settings-ajax"]'),
		divPinned = $('div[data-role="plugin-list-pinned"]'),
		selectPlugin = $('select[data-role="plugin"]'),
		pinPluginsToEnd = $(this).attr('data-pin-plugins-to-end') == 'Y';
	if(pinPluginsToEnd){
		divPinned.append(divSettings);
		var tabHeader = $(this).closest('.adm-detail-block').children('.adm-detail-tabs-block'),
			fixedHeader = !tabHeader.is('.adm-detail-tabs-block-pin');
		if(divPinned.children('[data-role="plugin-settings"]').length > 1){
			$('html,body').animate({
				scrollTop: divSettings.offset().top - (fixedHeader ? tabHeader.height() - 4 : 0)
			}, 300);
		}
	}
	else{
		divPinned.prepend(divSettings);
	}
	selectPlugin.val('').trigger('change');
	$('select[data-role="plugin"]').trigger('pinchange');
});

/**
 *	On plugin unpin
 */
$(document).delegate('a[data-role="plugin-settings-unpin"]', 'click', function(e){
	e.preventDefault();
	var divSettings = $(this).closest('div[data-role="plugin-settings"]');
	if(confirm($(this).data('confirm'))){
		divSettings.remove();
	}
	$('select[data-role="plugin"]').trigger('pinchange');
});

/**
 *	Button 'task settings'
 */
$(document).delegate('a[data-role="task-settings"]', 'click', function(e){
	e.preventDefault();
	var divSettings = $('div[data-role="task-settings-container"]');
	if(!divSettings.is(':animated')){
		divSettings.slideToggle();
	}
});

/**
 *	Button 'sort actions'
 */
$(document).delegate('a[data-role="task-sort"]', 'click', function(e){
	e.preventDefault();
	var pluginList = $('div[data-role="plugin-list-pinned"]'),
		className = 'wda-task-sorting';
	$(this).toggleClass(className);
	if($(this).hasClass(className)){
		$('div[data-role="plugin-list-pinned"] div[data-role="wda-fieldset"]').addClass('wda-fieldset-collapsed');
		pluginList.sortable({
			handle:'.wda-fieldset-legend',
			helper: function(Event, TR) {
				var $originals = TR.children();
				var $helper = TR.clone();
				$helper.children().each(function(index) {
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			stop: function(Event, UI) {
				$('td.index', UI.item.parent()).each(function (i) {
					$(this).html(i + 1);
				});
			},
			update: function(Event, UI) {
				wdaSortPlugins();
			}
		});
	}
	else{
		pluginList.sortable('destroy');
		$('div[data-role="wda-fieldset"]').removeClass('wda-fieldset-collapsed');
	}
});

// *********************************************************************************************************************
// Filter by sections
// *********************************************************************************************************************

/**
 *	Show filter
 */
$(document).delegate('a[data-role="wda_sections_show_filter"]', 'click', function(e){
	e.preventDefault();
	$('input[data-role="wda_sections_filter"]').val('').trigger('input').parent().toggle();
});

/**
 *	Filtering
 */
$(document).delegate('input[data-role="wda_sections_filter"]', 'input', function(e){
	e.preventDefault();
	let
		text = $.trim($(this).val()).toLowerCase(),
		textArray = text.split(/[\s]+/g),
		select = $('select[data-role="iblock-sections-id"]'),
		options = $('option', select).not('[value=""]'),
		optionEmpty = $('option', select).filter('[value=""]').toggle(!text.length),
		optionText,
		found;
	options.each(function(){
		found = [];
		optionText = $.trim($(this).text()).toLowerCase();
		$.each(textArray, function(){
			if(optionText.indexOf(this) != -1){
				found.push(true);
			}
		});
		$(this).toggle(found.length == textArray.length);
	});
});

/**
 *	Select all
 */
$(document).delegate('a[data-role="wda_sections_select"]', 'click', function(e){
	e.preventDefault();
	let
		select = $('select[data-role="iblock-sections-id"]'),
		options = $('option', select).not('[value=""]'),
		optionEmpty = $('option', select).filter('[value=""]'),
		type = $(this).attr("data-select"),
		checkbox = $('span[data-role="wda_sections_select_checkbox"] input[type=checkbox]');
	switch(type){
		case 'all':
			checkbox.prop('checked', false);
			wdaHideNotSelectedSections(false);
			optionEmpty.prop('selected', false);
			options.filter(':visible').prop('selected', true);
			break;
		case 'none':
			checkbox.prop('checked', false);
			wdaHideNotSelectedSections(false);
			optionEmpty.prop('selected', true);
			options.filter(':visible').prop('selected', false);
			break;
		case 'invert':
			checkbox.prop('checked', false);
			wdaHideNotSelectedSections(false);
			let selectedCount = 0;
			options.filter(':visible').each(function(){
				$(this).prop('selected', $(this).prop('selected') == true ? false : true);
				if($(this).prop('selected') == true){
					selectedCount++;
				}
			});
			optionEmpty.prop('selected', selectedCount == 0);
			break;
		case 'show':
		case 'shown':
			checkbox.prop('checked', !checkbox.prop('checked'));
			wdaHideNotSelectedSections(checkbox.prop('checked'));
			break;
	}
});

// *********************************************************************************************************************
// Filter
// *********************************************************************************************************************

/**
 *	On change iblock filter
 */
$(document).delegate('div[data-role="iblock-filter"] input[name="filter"]', 'change', function(e, data){
	if(document.readyState == 'complete' || typeof data == 'object' && data.initial) {
			var container = $('span[data-role="filter-results"]'),
				post = [{name: 'full', value: 'Y'}];
		if($('select[data-role="iblock-id"]').val() > 0){
			container.html('?').addClass('filter-loading');
			wdaAjax('check_filter', post, function(jqXHR, textStatus, arJson){
				container.html(arJson.Count).removeClass('filter-loading');
				$('div[data-role="bitrix-filter"]').html(arJson.BitrixFilter);
			}, function(jqXHR, textStatus, errorThrown){
				wdaPopupError.Open(jqXHR);
			}, true);
		}
		else{
			container.html('0');
		}
	}
});

/**
 *	Preview filter results
 */
$(document).delegate('a[data-role="filter-results-link"]', 'click', function(e){
	e.preventDefault();
	wdaPopupPreview.Open();
});

/**
 *	Button 'show filter'
 */
$(document).delegate('a[data-role="filter-show"]', 'click', function(e){
	e.preventDefault();
	var divFilterPrint = $('div[data-role="bitrix-filter"]'),
		className = 'wda-filter-shown';
	$(this).toggleClass(className);
	if($(this).hasClass(className)){
		divFilterPrint.show();
	}
	else{
		divFilterPrint.hide();
	}
});

// *********************************************************************************************************************
// Profile save and load
// *********************************************************************************************************************

/**
 *	Open popup for profile load
 */
$(document).delegate('input[data-role="wda-button-profile-load"]', 'click', function(e){
	e.preventDefault();
	wdaPopupLoadProfile.Open();
});

/**
 *	Open popup for profile save
 */
$(document).delegate('input[data-role="wda-button-profile-save"]', 'click', function(e){
	e.preventDefault();
	wdaPopupSaveProfile.Open();
});

/**
 *	Profile list: Filter profiles
 */
$(document).delegate('input[data-role="profile-list-search"]', 'input', function(e){
	var search = $(this).val().trim().toLowerCase(),
		profileList = $(this).closest('div[data-role="profile-list"]'),
		tbody = profileList.find('table[data-role="profile-list-table"]').children('tbody'),
		divEmpty = profileList.find('div[data-role="profile-list-empty"]'),
		rows = tbody.children('tr');
	rows.each(function(){
		if($(this).attr('data-search').indexOf(search) != -1){
			$(this).show();
		}
		else{
			$(this).hide();
		}
	});
	if(rows.filter(':visible').length){
		divEmpty.hide();
	}
	else{
		divEmpty.show();
	}
});

/**
 *	Profile list: Filter by entity type
 */
$(document).delegate('select[data-role="profile-list-entity"]', 'change', function(e){
	var profileList = $(this).closest('div[data-role="profile-list"]');
	profileList.attr('data-filter-entity-type', $(this).val());
	$('input[data-role="profile-list-search"]').trigger('input');
});

/**
 *	Profile list: Sort
 */
$(document).delegate('thead td.adm-list-table-cell-sort', 'click', function(e){
	var thisHeader = this,
		tbody = $(thisHeader).closest('table').children('tbody'),
		colIndex = $(thisHeader).index(),
		classAsc = 'adm-list-table-cell-sort-down',
		classDesc = 'adm-list-table-cell-sort-up';

	$(thisHeader).siblings().removeClass(classAsc).removeClass(classDesc).each(function(){this.inverse=null;});
	if(this.inverse === true || this.inverse === false){
		this.inverse = !this.inverse;
	}
	else {
		this.inverse = false;
	}
	tbody.find('td').filter(function(){
		return $(this).index() === colIndex;
	}).sortElements(function(a, b){
		a = $.text([a]).trim();
		b = $.text([b]).trim();
		if(!a.length && b.length) {
			return thisHeader.inverse ? -1 : 1;
		}
		if(a.length && !b.length) {
			return thisHeader.inverse ? 1 : -1;
		}
		if(a == b) {
			return 0;
		}
		if(!isNaN(parseInt(a)) && !isNaN(parseInt(b))){
			a = parseInt(a);
			b = parseInt(b);
		}
		return a > b ? (thisHeader.inverse ? -1 : 1) : (thisHeader.inverse ? 1 : -1);
	}, function(){
		return this.parentNode; //tr
	});
	if(this.inverse){
		$(thisHeader).addClass(classDesc);
		$(thisHeader).removeClass(classAsc);
	}
	else{
		$(thisHeader).addClass(classAsc);
		$(thisHeader).removeClass(classDesc);
	}
});

/**
 *	Load profile by click on row
 */
$(document).delegate('div[data-role="profile-list"] tr.adm-list-table-row td', 'click', function(e){
	if(!$(this).hasClass('wda-profile-list__button')){
		wdaLoadProfile($(this).closest('tr').attr('data-profile-id'), function(profileId){
			wdaPopupLoadProfile.Close();
		});
	}
});

/**
 *	Profile list: Delete profile
 */
$(document).delegate('input[data-role="profile-list-delete"]', 'click', function(e){
	e.preventDefault();
	var row = $(this).closest('tr[data-profile-id]'),
		profileId = row.attr('data-profile-id');
		tbody = row.parent(),
		profileList = row.closest('div[data-role="profile-list"]'),
		divEmpty = profileList.find('div[data-role="profile-list-empty"]'),
		confirmText = $(this).attr('data-confirm'),
		post = [{name:'profile_id', value: profileId}];
	if(confirm(confirmText)){
		wdaAjax('profile_delete', post, function(jqXHR, textStatus, arJson){
			if(arJson.Success){
				if(profileId == $('input[data-role="profile-id"]').val()){
					wdaChangeUrl('profile_id', null);
				}
				row.fadeOut(200, function(){
					$(this).remove();
					if(tbody.children('tr').length == 0){
						divEmpty.show();
					}
				});
			}
			else{
				wdaPopupError.Open(jqXHR);
			}
		}, function(jqXHR, textStatus, errorThrown){
			wdaPopupError.Open(jqXHR);
		});
	}
});

/**
 *	Profile list: Config cron
 */
$(document).delegate('input[data-role="profile-list-cron"]', 'click', function(e){
	e.preventDefault();
	var profileId = $(this).closest('tr[data-profile-id]').attr('data-profile-id');
	wdaPopupProfileCron.Open(profileId);
});

/**
 *	Cron list: Delete line
 */
$(document).delegate('input[data-role="cron-task-delete"]', 'click', function(e){
	e.preventDefault();
	var row = $(this).closest('tr'),
		tbody = row.parent(),
		classRowHidden = 'adm-list-table-row-hidden';
	if(!row.hasClass(classRowHidden) && confirm($(this).attr('data-confirm'))) {
		row.remove();
	}
});

// *********************************************************************************************************************
// Start / stop / resume
// *********************************************************************************************************************

/**
 *	Start!
 */
$(document).delegate('input[data-role="wda-button-start"]', 'click', function(e){
	e.preventDefault();
	wdaExecute(true, false);
});

/**
 *	Resume after stop/error
 */
$(document).delegate('input[data-role="wda-button-resume"]', 'click', function(e){
	e.preventDefault();
	wdaExecute(false, true);
});

/**
 *	Stop
 */
$(document).delegate('input[data-role="wda-button-stop"]', 'click', function(e){
	e.preventDefault();
	wdaStop();
	wdaProgressBar($('[data-role="execute-progress-bar"]'), false);
});

// *********************************************************************************************************************
// On DOMReady
// *********************************************************************************************************************
$(document).ready(function(){
	
	var selectIBlockId = $('select[data-role="iblock-id"]'),
		selectPlugin = $('select[data-role="plugin"]');
	
	// <select> for IBLOCK_ID
	wdaSelect2(selectIBlockId, {
		dropdownParent: $('div[data-role="iblock-id-parent"]')
	});
	
	// <select> for PLUGIN
	wdaSelect2(selectPlugin, {
		dropdownParent: $('div[data-role="plugin-wrapper"]'),
		templateResult: function(item){
			if(item.loading || $(item.element).is('optgroup') || !item.id.length) {
				return item.text;
			}
			var option = $(item.element);
			return $(
				'<div class="plugin-list__item" title="' + option.data('hint') + '">' +
					'<div class=\"plugin-list__item-icon">' +
						'<img src="' + option.data('icon') + '" alt="" />' +
					'</div>' +
					'<div class="plugin-list__item-title">' +
						item.text +
					'</div>' +
				'</div>'
			);
		},
		templateSelection: function(item){
			if(!item.id) {
				return item.text;
			}
			var option = $(item.element);
			return $(
				'<span class="select2-menuicon" title="' + option.data('hint') + '">' +
					'<img src="' + $(item.element).attr('data-icon') + '" /> ' + 
					'<span>' + item.text + '</span>' +
				'</span>'
			);
		}
	});
	
	// Trigger change
	$('input[data-role="select-sections"]').trigger('change');
	
	// Trigger `pinchange` on page load if using &profile=123
	selectPlugin.trigger('pinchange');

	// Trigger sections change (Transform selected sections id to one line [with comma separated values])
	$('select[data-role="iblock-sections-id"]').trigger('change');
	
	// Start filtering
	$('div[data-role="iblock-filter"] input[name="filter"]').trigger('change', {initial:true});
	
	// Remove option value=""
	if(selectIBlockId.val() > 0){
		$('option[value=""]', selectIBlockId).remove();
	}
	
	// Filter section restrictions
	$('div[data-role="entity-type"] input[type="radio"]:checked').trigger('change', {initial: true});
	
	// Notice for non-D7
	wdaShowSectionIncludeSubsectionsNotice();
	
});
