<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\Filter,
	\WD\Antirutin\Json,
	\WD\Antirutin\Plugin,
	\WD\Antirutin\ProfileTable as Profile,
	\WD\Antirutin\Support,
	\WD\Antirutin\Uploader,
	\WD\Antirutin\Worker;

$strModuleId = 'webdebug.antirutin';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin_tools.php");
$bModule = \Bitrix\Main\Loader::includeModule($strModuleId);
#$APPLICATION->setTitle(Helper::getMessage('WDA_PAGE_TITLE'));

if(!$bModule){
	$APPLICATION->setTitle(Helper::getMessage('WDA_PAGE_TITLE'));
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	return;
}

# Prepare
Helper::loadMessages(__FILE__);
if($APPLICATION->getGroupRight($strModuleId) < 'R') {
	$APPLICATION->authForm(getMessage('ACCESS_DENIED'));
}
if(!\Bitrix\Main\Loader::includeModule('iblock')) {
	$APPLICATION->setTitle(Helper::getMessage('WDA_PAGE_TITLE'));
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	print Helper::showError(Helper::getMessage('WDA_NO_IBLOCK_MODULE'));
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	return;
}
$bCanWrite = $APPLICATION->getGroupRight($strModuleId) >= 'W';

# Default params
$intMaxDepthDefault = 3;
$intMaxDepthShow = 5;

# Input data
list($arGet, $arPost) = Helper::getRequestQuery();

# Prepare
\Bitrix\Main\Loader::includeModule('fileman');
\CJSCore::Init(['jquery', 'jquery2', 'jquery3', 'file_input', 'fileinput', 'html_editor']);

# Get profile
$intProfileId = Helper::getFirstValue([$arPost['profile_id'], $arGet['profile_id']]);
$arProfile = Helper::getProfileArray($intProfileId);
if(empty($arProfile)){
	$intProfileId = false;
}

# Remove parameters [entity_type, iblock_id, plugin] if using profile_id
if($intProfileId && !isset($arGet['ajax_action'])){
	$arGetRemove = ['entity_type', 'iblock_id', 'plugin'];
	$arGetRemove = array_intersect_key($arGet, array_flip($arGetRemove));
	if(!empty($arGetRemove)){
		localRedirect($APPLICATION->getCurPageParam('', array_keys($arGetRemove)));
	}
}

# Get params
$strEntityType = Helper::getFirstValue([$arPost['entity_type'], $arGet['entity_type'], $arProfile['ENTITY_TYPE']]);
$strEntityType = toUpper($strEntityType);
if(!in_array($strEntityType, [Helper::TYPE_ELEMENT, Helper::TYPE_SECTION])){
	$strEntityType = Helper::TYPE_ELEMENT;
}
$intIBlockId = Helper::getFirstValue([$arPost['iblock_id'], $arGet['iblock_id'], $arProfile['IBLOCK_ID']]);
if($intIBlockId){
	$intMaxDepthShow = max(1, IBlock::getMaxSectionDepth($intIBlockId));
}
$bSelectSections = Helper::getFirstValue([$arGet['select_sections'], $arProfile['SELECT_SECTIONS']]) == 'Y';
$intMaxDepth = Helper::getFirstValue([$arPost['max_depth'], $arGet['max_depth'], $arProfile['MAX_DEPTH']]);
if($intMaxDepth <= 0 || $intMaxDepth > $intMaxDepthShow) {
	$intMaxDepth = $intMaxDepthShow;
}
$bIncludeSubsections = Helper::getFirstValue([$arGet['include_subsections'], $arProfile['INCLUDE_SUBSECTIONS']]) == 'Y';
$strSectionsId = Helper::getFirstValue([$arPost['sections_id'], $arProfile['SECTIONS_ID']]);
if($strSectionsId == '-' || !$bSelectSections){
	$strSectionsId = '';
}
$arSectionsId = strlen($strSectionsId) ? explode(',', $strSectionsId) : [];
$strFilter = Helper::getFirstValue([$arPost['filter'], $arProfile['FILTER']]);
if($strFilter == '-'){
	$strFilter = '';
}
$intIBlockOffersId = null;
if($intIBlockId){
	$arCatalog = Helper::getCatalogArray($intIBlockId);
	if($arCatalog['OFFERS_IBLOCK_ID']){
		$intIBlockOffersId = $arCatalog['OFFERS_IBLOCK_ID'];
	}
}

# Auto fill iblock_id
if(!$intIBlockId && !$intProfileId && !isset($arGet['ajax_action'])){
	if($intDefaultIBlockId = Helper::getOption('default_iblock_id')){
		localRedirect($APPLICATION->getCurPageParam('iblock_id='.$intDefaultIBlockId, ['iblock_id']));
	}
}

# Prepare data
$arPluginsPlain = Helper::findPlugins($strEntityType, $bGroup=false);
$arIBlocks = Helper::getIBlocks();

# Plugin
$strCurrentPlugin = Helper::getFirstValue([$arPost['plugin'], $arGet['plugin_code'], $arGet['plugin']]);
$arCurrentPlugin = [];
$obCurrentPlugin = null;
if($strCurrentPlugin){
	$strCurrentPlugin = toUpper($strCurrentPlugin);
	$arCurrentPlugin = $arPluginsPlain[$strCurrentPlugin];
	if(is_array($arCurrentPlugin)){
		$obCurrentPlugin = new $arCurrentPlugin['CLASS'];
		$obCurrentPlugin->setEntityType($strEntityType);
		$strPluginId = htmlspecialcharsbx($arPost['plugin_id']);
		if(strlen($strPluginId)){
			$obCurrentPlugin->setId($strPluginId);
		}
		$obCurrentPlugin->setIBlockId($intIBlockId);
		$obCurrentPlugin->setRequestData($arGet, $arPost);
	}
}

# Additional
$strNoPlugin = Helper::getMailtoLink([
	'TEXT' => Helper::getMessage('WDA_NO_PLUGIN_MAILTO_TEXT'),
	'SUBJECT' => Helper::getMessage('WDA_NO_PLUGIN_MAILTO_SUBJECT'),
	'ATTR' => 'class="wda-inline-link"',
]);

# Ajax actions
$strAjaxAction = $arGet['ajax_action'];
if(strlen($strAjaxAction)){
	Json::setHttpHeader();
	$arJson = Json::prepare();
	switch($strAjaxAction){
		# file_upload
		case 'file_upload':
			$arJson['Success'] = false;
			$arUploadedFile = Uploader::uploadFile($arPost['data'], $arPost['name'], $arPost['type']);
			if(is_array($arUploadedFile)){
				$arJson['Success'] = true;
				$arJson['File'] = $arUploadedFile;
			}
			break;
		# custom_ajax
		case 'custom_ajax':
			$arJson['Success'] = true;
			$arJson['Html'] = $obCurrentPlugin->customAjax($arGet['ajax_action_sub'], $arJson);
			break;
		# change_entity_type
		case 'change_entity_type':
			$arJson['Success'] = true;
			$arJson['FilterHtml'] = Helper::includeFile('iblock_filter', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'FILTER_DATA' => null,
				'SECTIONS_ID' => $arSectionsId,
				'INCLUDE_SUBSECTIONS' => $bIncludeSubsections,
			]);
			$arJson['PluginsHtml'] = Helper::includeFile('plugin_select_options', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'PLUGIN_CODE' => $strCurrentPlugin,
				'PLUGIN_ARRAY' => $arCurrentPlugin,
				'PLUGIN_OBJECT' => $obCurrentPlugin,
			]);
			break;
		# change_iblock
		case 'change_iblock':
			$arJson['Success'] = true;
			$arJson['MaxSectionDepth'] = max(1, IBlock::getMaxSectionDepth($intIBlockId));
			$arJson['IBlockSectionsHtml'] = Helper::includeFile('iblock_sections_select', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'SECTIONS_ID' => IBlock::getIBlockSections($intIBlockId, $intMaxDepth),
				'SECTIONS_ID_SELECTED' => array_filter(explode(',', $arPost['selected_sections_id']),
					function($a){return $a !== '' && $a !== false && $a !== null;}),
				'PLACEHOLDER' => Helper::getMessage('WDA_SECTIONS_PLACEHOLDER'),
				'ROOT_LEVEL' => Helper::getMessage('WDA_SECTIONS_ROOT_LEVEL'),
			]);
			$arJson['FilterHtml'] = Helper::includeFile('iblock_filter', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'FILTER_DATA' => null,
				'SECTIONS_ID' => $arSectionsId,
				'INCLUDE_SUBSECTIONS' => $bIncludeSubsections,
			]);
			break;
		# change_plugin
		case 'change_plugin':
			$arJson['Success'] = true;
			$arJson['PluginSettingsHtml'] = Helper::includeFile('plugin_settings', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'PLUGIN_CODE' => $strCurrentPlugin,
				'PLUGIN_ARRAY' => $arCurrentPlugin,
				'PLUGIN_OBJECT' => $obCurrentPlugin,
			]);
			if(!strlen($arJson['PluginSettingsHtml'])){
				$arJson['PluginSettingsHtml'] = Helper::getMessage('WDA_NO_PLUGIN_SELECTED', ['#LINK#' => $strNoPlugin]);
			}
			break;
		# load_popup
		case 'load_popup':
			$arJson['Success'] = true;
			$strPopupId = Helper::clearFilename($arPost['popup_id']);
			$arJson['Html'] = Helper::getPopupContent($strPopupId, [
				'GET' => $arGet,
				'POST' => $arPost,
				'JSON' => &$arJson,
				#
				'ENTITY_TYPE' => $strEntityType,
				'PROFILE_ID' => $intProfileId,
				'PROFILE_ARRAY' => $arProfile,
				'IBLOCK_ID' => $intIBlockId,
				'IBLOCK_OFFERS_ID' => $intIBlockOffersId,
				'PLUGIN_CODE' => $strCurrentPlugin,
				'PLUGIN_ARRAY' => $arCurrentPlugin,
				'PLUGIN_OBJECT' => $obCurrentPlugin,
				#
				'FILTER' => $strFilter,
				'SECTIONS_ID' => $arSectionsId,
				'INCLUDE_SUBSECTIONS' => $bIncludeSubsections,
			]);
			break;
		# check_filter
		case 'check_filter':
			$arJson['Success'] = true;
			$obFilter = new Filter($intIBlockId, $strEntityType);
			if(is_array($arSectionsId)){
				$obFilter->setSectionsId($arSectionsId);
			}
			$obFilter->setIncludeSubsections($bIncludeSubsections);
			if(strlen($strFilter)){
				$obFilter->setJson($strFilter);
			}
			$arFilter = $obFilter->buildFilter(true);
			ob_start();
			Helper::P(htmlspecialcharsbx(print_r($arFilter, true)));
			$arJson['BitrixFilter'] = ob_get_clean();
			$arJson['Count'] = $obFilter->checkCount();
			break;
		# execute
		case 'execute':
			$arJson['Success'] = true;
			$obWorker = new Worker();
			$obWorker->setExecuteType($arPost['start'] == 'Y' ? Worker::TYPE_START : Worker::TYPE_CONTINUE);
			$obWorker->setProfileId($intProfileId);
			$obWorker->setEntityType($strEntityType);
			$obWorker->setIBlockId($intIBlockId);
			$obWorker->setIBlockSectionsId($arSectionsId, $bIncludeSubsections);
			if(strlen($strFilter)){
				$obWorker->setFilter($strFilter);
			}
			$arActions = $arPost[Plugin::FIELD_NAME];
			if(is_array($arActions)){
				foreach($arActions as $key => $arAction){
					$arActions[$key] = Helper::replaceActionRandInputValues($arAction);
				}
			}
			$obWorker->setActions($arActions);
			$obWorker->setSettings($arPost['settings']);
			$arExecuteResult = $obWorker->execute();
			$arJson = array_merge($arJson, $arExecuteResult);
			break;
		# profile_save
		case 'profile_save':
			$arJson['Success'] = false;
			$arProfileFields = [
				'ACTIVE' => 'Y',
				'NAME' => $arPost['profile_name'],
				'CODE' => $arPost['profile_code'],
				'SORT' => $arPost['profile_sort'],
				'DESCRIPTION' => $arPost['profile_description'],
				#
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'SELECT_SECTIONS' => $bSelectSections == 'Y' ? 'Y' : 'N',
				'MAX_DEPTH' => $intMaxDepth,
				'SECTIONS_ID' => $arPost['sections_id'],
				'INCLUDE_SUBSECTIONS' => $bIncludeSubsections ? 'Y' : 'N',
				'FILTER' => $arPost['filter'],
				'SETTINGS' => serialize($arPost['settings']),
				'ACTIONS' => $arPost[Plugin::FIELD_NAME],
			];
			if(!empty($arProfileFields['ACTIONS'])){
				ob_start();
				if(is_numeric($intProfileId) && $intProfileId > 0 && $arPost['profile_type'] != 'new'){
					$obResult = Profile::update($intProfileId, $arProfileFields);
				}
				else{
					$obResult = Profile::add($arProfileFields);
				}
				$bSuccess = $obResult->isSuccess();
				$arJson['Success'] = $bSuccess;
				if($bSuccess){
					$arJson['ProfileId'] = $obResult->getId();
					$arQuery = ['filter' => ['ID' => $obResult->getId()], 'select' => ['NAME']];
					$arJson['ProfileTitle'] = Profile::getList($arQuery)->fetch()['NAME'];
				}
				else{
					$arErrors = $obResult->getErrorMessages();
					$arJson['ErrorText'] = is_array($arErrors) ? implode("\n", $arErrors) : 'Error';
				}
				$strHtml = ob_get_clean();
			}
			else{
				$arJson['MessageText'] = Helper::getMessage('WDA_CANNOT_SAVE_NO_ACTION');
			}
			$arJson['Html'] = Helper::getPopupContent('profile_save', [
				'ENTITY_TYPE' => $strEntityType,
				'IBLOCK_ID' => $intIBlockId,
				'PLUGIN_CODE' => $strCurrentPlugin,
				'PLUGIN_ARRAY' => $arCurrentPlugin,
				'PLUGIN_OBJECT' => $obCurrentPlugin,
			]).$strHtml;
			break;
		# profile_delete
		case 'profile_delete':
			$arJson['Success'] = false;
			if($intProfileId > 0) {
				$obResult = Profile::delete($intProfileId);
				if($obResult->isSuccess()){
					$arJson['Success'] = true;
				}
			}
			break;
		# cron_tasks_save
		case 'cron_tasks_save':
			$arJson['Success'] = false;
			$arTasks = [];
			$arKeys = ['external', 'minute', 'hour', 'day', 'month', 'weekday'];
			$arData = array_intersect_key($arPost, array_flip($arKeys));
			$strKeyFirst = reset($arKeys);
			foreach($arData[$strKeyFirst] as $strExternalId => $strValue){
				$arTask = [];
				foreach($arKeys as $strKey){
					if($strKey != $strKeyFirst){
						$arTask[$strKey] = $arData[$strKey][$strExternalId];
					}
				}
				if(!empty($arTask)){
					$arTasks[$strExternalId] = $arTask;
				}
			}
			if(isset($arPost['php_path'])){
				Helper::setOption('php_path', $arPost['php_path']);
			}
			if(in_array($arPost['php_mbstring'], ['Y', 'N'])){
				Helper::setOption('php_mbstring', $arPost['php_path']);
			}
			if(isset($arPost['php_config'])){
				Helper::setOption('php_config', $arPost['php_config']);
			}
			$arJson['Success'] = Cli::setProfileCronTasks($intProfileId, $arTasks);
			if(!$arJson['Success']){
				$arJson['ErrorText'] = Helper::getMessage('WDA_ERROR_SET_CRON_TASKS');
			}
			break;
		# support_send
		case 'support_send':
			$arJson['Success'] = false;
			$mSupportResult = Support::sendRequest($arPost['name'], $arPost['email'], $arPost['subject'], $arPost['message']);
			if(is_array($mSupportResult) && $mSupportResult['SUCCESS']){
				$arJson['Success'] = true;
				$arJson['SuccessMessage'] = Helper::getMessage('WDA_MESSAGE_SUPPORT_SUCCESS');
			}
			else{
				$arJson['ErrorMessage'] = is_array($mSupportResult) ? $mSupportResult['ERROR_MESSAGE'] : $mSupportResult;
			}
			break;
		# get_user
		case 'get_user':
			$arJson['Success'] = true;
			$arJson['UserTitle'] = Helper::getUserTitle($arPost['user_id'], $arPost['user_title_mode']);
			break;
	}
	Helper::obRestart();
	Json::output($arJson);
	die();
}

#
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
$APPLICATION->setTitle(Helper::getMessage('WDA_PAGE_TITLE'));

# Add JS phrases
Helper::addJsLangPhrases([
	'LOADING',
	'SAVE',
	'CLOSE',
	'CANCEL',
	'REFRESH',
	'ADD',
	'ADD_MORE',
	'SEND',
	'PROFILE_SAVE_TITLE',
	'PROFILE_LOAD_TITLE',
	'PROFILE_LOAD_NOTICE',
	'PROFILE_CRON_TITLE',
	'PREVIEW_TITLE',
	'ERROR_TITLE',
	'DEBUG_TITLE',
	'RESULTS_TITLE',
	'HELP_TITLE',
	'HELP_TITLE_2',
	'SUPPORT_TITLE',
	['WDA_FILTER_VALUE_SEPARATOR', Filter::VALUE_SEPARATOR],
	['WDA_CANNON_START', Helper::getMessage('WDA_CANNOT_START')],
	['WDA_CANNOT_START_NO_ACTIONS', Helper::getMessage('WDA_CANNOT_START_NO_ACTIONS')],
	['WDA_UNKNOWN_ERROR', Helper::getMessage('WDA_UNKNOWN_ERROR')],
	['JSADM_FILES', 'JSADM_FILES'], // Fix for Bitrix
	['WDA_PAGE_PROFILE_TITLE', Helper::getMessage('WDA_PAGE_PROFILE_TITLE')],
], 'WDA_POPUP_');

# Static
$strLangFile = Helper::isUtf() ? 'ru_utf8.js' : 'ru_cp1251.js';
\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.$strModuleId.'/helper.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strModuleId.'/jquery.sortelements/jquery.sortElements.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strModuleId.'/jquery.ui/jquery.ui.sortable.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strModuleId.'/jquery.select2/dist/js/select2.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strModuleId.'/jquery.select2/'.$strLangFile);
$APPLICATION->SetAdditionalCss('/bitrix/js/'.$strModuleId.'/jquery.select2/dist/css/select2.css');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.$strModuleId.'/jquery.filedrop/jQuery.FileDrop.js');

# Tabs
$arTabs = [[
	'DIV' => 'wd_antirutin_settings',
	'TAB' => Helper::getMessage('WDA_TAB_SETTINGS_NAME'),
	'TITLE' => Helper::getMessage('WDA_TAB_SETTINGS_DESC'),
]];
$obTabControl = new \CAdminTabControl('WD_Antirutin_Tabs', $arTabs, $bCanExpand=true, $bDenyAutoSave=true);

# Message: access denied
if(!$bCanWrite){
	\CAdminMessage::showMessage([
		'MESSAGE' => GetMessage('WDA_ERROR_ACCESS_DENIED'), 
		'DETAILS' => GetMessage('WDA_ERROR_ACCESS_DENIED_DETAILS'),
		'TYPE' => 'ERROR',
	]);
}

# Notice for carefully
if(Helper::getOption('show_carefully_notice') == 'Y') {
	print Helper::showNote(Helper::getMessage('WDA_CAREFULLY_NOTICE'), true).'<br/>';
}

# Context menu
$arMenu[] = array(
	'TEXT' => GetMessage('WDA_BUTTON_SUPPORT'),
	'LINK' => 'javascript:wdaSupport.Open();',
	'ICON' => 'wda_context_button_support',
);
$arMenu[] = array(
	'TEXT' => GetMessage('WDA_BUTTON_PROFILE_LOAD'),
	'LINK' => 'javascript:wdaPopupLoadProfile.Open();',
	'ICON' => 'wda_context_button_profiles',
);
$context = new \CAdminContextMenu($arMenu);
$context->show();

# Show profile title
if($arProfile){
	?><script>wdaSetPageProfileTitle('<?=htmlspecialcharsbx($arProfile['NAME']);?>');</script><?
}
?>

<div class="wda-form wda-type-<?=toLower($strEntityType);?><?if(!$intIBlockId):?> wda-hide-all<?endif?>"
	data-role="wda-wrapper">
<?$obTabControl->begin();?>
<?$obTabControl->beginNextTab();?>
	<tr class="heading">
		<td colspan="2">
			<?=Helper::getMessage('WDA_HEADER_ENTITY');?>
		</td>
	</tr>
	<tr data-role="row-type">
		<td class="adm-detail-content-cell-r" colspan="2">
			<div class="wda-radio-selector wda-not-select wda-selector-entity-type" data-role="entity-type">
				<label>
					<input type="radio" name="entity-type" value="<?=toLower(Helper::TYPE_ELEMENT);?>"
						<?if($strEntityType == Helper::TYPE_ELEMENT):?>checked="checked"<?endif?> />
					<span><?=Helper::getMessage('WDA_TYPE_ELEMENT');?></span>
				</label>
				<label>
					<input type="radio" name="entity-type" value="<?=toLower(Helper::TYPE_SECTION);?>"
						<?if($strEntityType != Helper::TYPE_ELEMENT):?>checked="checked"<?endif?> />
					<span><?=Helper::getMessage('WDA_TYPE_SECTION');?></span>
				</label>
			</div>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2">
			<?=Helper::getMessage('WDA_HEADER_SOURCE');?>
		</td>
	</tr>
	<tr data-role="row-iblocks">
		<td colspan="2">
			<table width="100%">
				<tbody>
					<tr>
						<td class="adm-detail-content-cell-l wda-col-left-general">
							<?=Helper::getMessage('WDA_IBLOCK_ID');?>
						</td>
						<td class="adm-detail-content-cell-r">
							<div data-role="iblock-id-parent" style="position:relative;">
								<select name="iblock_id" id="wda_iblock_id" data-role="iblock-id"
									data-iblock-id="<?=$intIBlockId;?>">
									<option value=""><?=Helper::getMessage('WDA_IBLOCK_ID_PLACEHOLDER');?></option>
									<?foreach($arIBlocks as $IBlockTypeCode => $arIBlockType):?>
										<?if(is_array($arIBlockType['ITEMS'])&&!empty($arIBlockType['ITEMS'])):?>
											<optgroup label="<?=$arIBlockType['NAME'];?>">
												<?foreach($arIBlockType['ITEMS'] as $arItem):?>
													<option value="<?=$arItem['ID'];?>"
														<?if($intIBlockId == $arItem['ID']):?> selected="selected"<?endif?>
														><?=$arItem['NAME'];?> 
														[<?=$arItem['ID'];?><?=(strlen($arItem['CODE']) ? ', '.$arItem['CODE'] : '');?>]</option>
												<?endforeach?>
											</optgroup>
										<?endif?>
									<?endforeach?>
								</select>
								<span class="wda-hidden">
									&nbsp;
									<span class="wda-no-break">
										<input type="checkbox" name="select_sections" value="Y" id="wda-select-sections"
											data-role="select-sections"<?if($bSelectSections):?> checked="checked"<?endif?> />
										<label for="wda-select-sections">
											<?=Helper::getMessage('WDA_SELECT_SECTIONS');?>
										</label>
									</span>
								</span>
								<span data-role="include-subsections-wrapper" class="wda-include-subsections">
									&nbsp;
									&nbsp;
									<span class="wda-no-break">
										<input type="checkbox" name="subsections" value="Y" id="wda-include-subsections"
											data-role="include-subsections"<?if($bIncludeSubsections):?> checked="checked"<?endif?> />
										<label for="wda-include-subsections">
											<?=Helper::getMessage('WDA_INCLUDE_SUBSECTIONS');?>
										</label>
									</span>
								</span>
								<span data-role="sections-max-depth-wrapper" style="display:none;">
									&nbsp;
									&nbsp;
									<span class="wda-no-break">
										<?=Helper::getMessage('WDA_SECTIONS_MAX_DEPTH');?>
										<select data-role="sections-max-depth">
											<?for($intDepth = 1; $intDepth <= $intMaxDepthShow; $intDepth++):?>
												<option value="<?=$intDepth;?>"
													<?if($intDepth == $intMaxDepth):?> selected="selected"<?endif?>><?
													print $intDepth;
												?></option>
											<?endfor?>
										</select>
									</span>
								</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr data-role="row-sections" style="display:none;">
		<td class="adm-detail-content-cell-l wda-col-left-general" valign="top">
			<?=Helper::getMessage('WDA_SECTIONS');?>
		</td>
		<td class="adm-detail-content-cell-r">
			<div class="wda-sections-id-wrapper">
				<div class="wda-sections-id-wrapper-left">
					<div class="wda-sections-id-wrapper-filter">
						<input type="text" value="" data-role="wda_sections_filter"
							placeholder="<?=Helper::getMessage('WDA_SECTIONS_FILTER');?>" />
					</div>
					<div class="wda-sections-id-wrapper-select">
						<select class="wda-sections-id" multiple="multiple" size="<?=Helper::getOption('sections_select_size');?>"
							data-role="iblock-sections-id">
							<?=Helper::includeFile('iblock_sections_select', [
								'ENTITY_TYPE' => $strEntityType,
								'IBLOCK_ID' => $intIBlockId,
								'SECTIONS_ID' => IBlock::getIBlockSections($intIBlockId, $intMaxDepth),
								'SECTIONS_ID_SELECTED' => $arSectionsId,
								'PLACEHOLDER' => Helper::getMessage('WDA_SECTIONS_PLACEHOLDER'),
								'ROOT_LEVEL' => Helper::getMessage('WDA_SECTIONS_ROOT_LEVEL'),
							]);?>
						</select>
						<input type="hidden" name="sections_id" value="<?=$strSectionsId;?>" data-role="iblock-sections-id" size="50" />
					</div>
				</div>
				<div class="wda-sections-id-wrapper-right">
					<div class="wda-sections-id-controls">
						<a href="#sections_show_filter" class="adm-btn" data-role="wda_sections_show_filter">
							<span><?=Helper::getMessage('WDA_SECTIONS_SHOW_FILTER');?></span>
						</a>
						<a href="#sections_select_all" class="adm-btn" data-role="wda_sections_select" data-select="all">
							<span><?=Helper::getMessage('WDA_SECTIONS_SELECT_ALL');?></span>
						</a>
						<a href="#sections_select_invert" class="adm-btn" data-role="wda_sections_select" data-select="invert">
							<span><?=Helper::getMessage('WDA_SECTIONS_SELECT_INVERT');?></span>
						</a>
						<a href="#sections_unselect_all" class="adm-btn" data-role="wda_sections_select" data-select="none">
							<span><?=Helper::getMessage('WDA_SECTIONS_UNSELECT_ALL');?></span>
						</a>
						<a href="#sections_show_selected" class="adm-btn" data-role="wda_sections_select" data-select="show">
							<span><span data-role="wda_sections_select_checkbox"><input type="checkbox" disabled /></span>
								<?=Helper::getMessage('WDA_SECTIONS_SELECT_SHOW');?></span>
						</a>
					</div>
				</div>
			</div>
			<?if(!IBlock::isSectionD7Available()):?>
				<div class="wda-section-include-subsections-notice" data-role="wda_section_include_subsections_notice">
					<?=Helper::showError(Helper::getMessage('WDA_FILTER_NOTICE_SECTION_INCLUDE_SUBSECTIONS'), false, true);?>
				</div>
			<?endif?>
			<div class="wda-section-subsections-notice" data-role="wda_section_subsections_notice">
				<?=Helper::showNote(Helper::getMessage('WDA_FILTER_NOTICE_SECTION_SUBSECTIONS'), true);?>
			</div>
		</td>
	</tr>
	<tr class="heading wda-hidden"><td colspan="2">
		<a href="#filter_results" data-role="filter-results-link" class="wda-filter-results">
			<?=Helper::getMessage('WDA_FILTER_RESULTS');?>
			<span data-role="filter-results">?</span></a>
		<?=Helper::getMessage('WDA_HEADER_FILTER');?>
		<span data-role="filter-result-count"></span>
		<a href="#show_filter" data-role="filter-show" class="wda-filter-show wda-not-select"
		><?=Helper::getMessage('WDA_FILTER_SHOW_ARRAY');?></a></td></tr>
	<tr class="wda-hidden" data-role="row-filter">
		<td colspan="2" class="adm-detail-content-cell-r">
			<div data-role="iblock-filter">
				<?=Helper::includeFile('iblock_filter', [
					'ENTITY_TYPE' => $strEntityType,
					'IBLOCK_ID' => $intIBlockId,
					'FILTER_DATA' => $strFilter,
					'SECTIONS_ID' => $arSectionsId,
					'INCLUDE_SUBSECTIONS' => $bIncludeSubsections,
				]);?>
			</div>
			<div data-role="bitrix-filter" style="display:none;"><?Helper::P($GLOBALS['WDA_FILTER_PRINT']);?></div>
			<?if(!IBlock::isSectionD7Available()):?>
				<div data-role="wda_filter_section_restrictions" style="display:none;">
					<?=Helper::showError(Helper::getMessage('WDA_FILTER_NOTICE_SECTION_RESTRICTIONS', [
							'#EMAIL_HREF#' => Helper::getMailtoLink([
								'SUBJECT' => Helper::getMessage('WDA_FILTER_NOTICE_SECTION_RESTRICTIONS_ASK_SUBJECT'),
							], true),
						]), false, true);?>
				</div>
			<?endif?>
		</td>
	</tr>
	<tr class="heading wda-hidden">
		<td colspan="2">
			<?=Helper::getMessage('WDA_HEADER_ACTIONS');?>
			<a href="#task_settings" data-role="task-settings" class="wda-task-settings wda-not-select"
			><?=Helper::getMessage('WDA_HEADER_TASK_SETTINGS');?></a>
		</td>
	</tr>
	<tr class="wda-hidden" data-role="row-action">
		<td colspan="2" class="adm-detail-content-cell-r">
			<div data-role="task-settings-container" class="wda-task-settings-container">
				<?=Helper::includeFile('task_settings', ['PROFILE' => $arProfile]);?>
			</div>
			<div data-role="plugin-wrapper">
				<select name="plugin" data-role="plugin">
					<?=Helper::includeFile('plugin_select_options', [
						'ENTITY_TYPE' => $strEntityType,
						'IBLOCK_ID' => $intIBlockId,
						'PLUGIN_CODE' => $strCurrentPlugin,
						'PLUGIN_ARRAY' => $arCurrentPlugin,
						'PLUGIN_OBJECT' => $obCurrentPlugin,
					]);?>
				</select>
			</div>
			<br/>
			<div data-role="plugin-serialize-container">
				<div class="plugin-list-unpinned" data-role="plugin-settings-ajax"
					data-empty="<?=htmlspecialcharsbx(Helper::getMessage('WDA_NO_PLUGIN_SELECTED', ['#LINK#' => $strNoPlugin]));?>">
					<?if(is_object($obCurrentPlugin)):?>
					<?=Helper::includeFile('plugin_settings', [
						'ENTITY_TYPE' => $strEntityType,
						'IBLOCK_ID' => $intIBlockId,
						'PLUGIN_CODE' => $strCurrentPlugin,
						'PLUGIN_ARRAY' => $arCurrentPlugin,
						'PLUGIN_OBJECT' => $obCurrentPlugin,
					]);?>
					<?else:?>
						<?=Helper::getMessage('WDA_NO_PLUGIN_SELECTED', ['#LINK#' => $strNoPlugin]);?>
					<?endif?>
				</div>
			</div>
		</td>
	</tr>
	<tr class="heading" style="display:none;">
		<td colspan="2"><?=Helper::getMessage('WDA_HEADER_TASK');?>
			<a href="#task_sort" data-role="task-sort" class="wda-task-sort wda-not-select"
			><?=Helper::getMessage('WDA_TASK_SORT');?></a>
		</td>
	</tr>
	<tr data-role="row-task" style="display:none;">
		<td colspan="2" class="adm-detail-content-cell-r">
			<div data-role="plugin-serialize-container">
				<div class="plugin-list-pinned" data-role="plugin-list-pinned"><?
					if(is_numeric($intProfileId) && $intProfileId > 0){
						print Helper::includeFile('profile_loaded', [
							'ENTITY_TYPE' => $strEntityType,
							'PROFILE_ID' => $intProfileId,
							'PLUGINS_PLAIN' => $arPluginsPlain,
						]);
					}
				?></div>
			</div>
		</td>
	</tr>
<?$obTabControl->buttons();//todo:hack fix?>
	<input type="hidden" value="<?=$intProfileId;?>" data-role="profile-id" />
	<input type="button" id="wda_profile_save"  class="adm-btn" data-role="wda-button-profile-save"
		value="<?=Helper::getMessage('WDA_BUTTON_PROFILE_SAVE');?>" style="float:right" />
	<?if($bCanWrite):?>
		<input type="button" id="wda_execute_start" class="adm-btn-green" data-role="wda-button-start"
			value="<?=Helper::getMessage('WDA_BUTTON_START');?>" />
		<input type="button" id="wda_execute_resume" class="adm-btn-green" data-role="wda-button-resume"
			value="<?=Helper::getMessage('WDA_BUTTON_RESUME');?>" disabled="disabled" style="display:none;" />
		<input type="button" id="wda_stop" class="adm-btn-stop" data-role="wda-button-stop"
			value="<?=Helper::getMessage('WDA_BUTTON_STOP');?>" disabled="disabled"
			data-disabling-control-reverse="true" />
		<div class="wda-progress-bar" data-role="execute-progress-bar"></div>
		<img src="https://www.webdebug.ru/_res/<?=$strModuleId;?>/<?=$strModuleId;?>.img" alt="" 
			width="0" height="0" style="visibility:hidden"/>
	<?endif?>
<?$obTabControl->end();?>

</div>

<div id="wda_message"></div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>