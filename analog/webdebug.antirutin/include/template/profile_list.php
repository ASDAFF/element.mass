<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Cli,
	\WD\Antirutin\Helper,
	\WD\Antirutin\ProfileTable as Profile;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strId = Helper::randString();

$strLang = 'WDA_POPUP_PROFILE_LIST_';

$arProfileTasksCount = [];

$arProfiles = [];
$resProfiles = Profile::getList([
	'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
]);
while($arProfile = $resProfiles->fetch()){
	$arProfiles[] = $arProfile;
	$arProfileTasksCount[$arProfile['ID']] = 0;
}

$arSearch = ['ID', 'NAME', 'CODE', 'DESCRIPTION', 'SORT'];

$arCronTasks = Cli::getCronTasks();

foreach($arCronTasks as $arTask){
	$arProfileTasksCount[$arTask['PROFILE_ID']]++;
}
?>

<div class="wda-profile-list adm-list-table-wrap adm-list-table-without-header adm-list-table-without-footer wda-form"
	data-role="profile-list" id="<?=$strId;?>">
	<div class="wda-profile-list__top adm-list-table-top">
		<table>
			<tbody>
				<tr>
					<td>
						<?=Helper::selectBox('', [
							'' => Helper::getMessage($strLang.'FILTER_ENTITY_ALL'),
							Helper::TYPE_ELEMENT => Helper::getMessage($strLang.'FILTER_ENTITY_ELEMENT'),
							Helper::TYPE_SECTION => Helper::getMessage($strLang.'FILTER_ENTITY_SECTION'),
						], null, null, 
							'title="'.Helper::getMessage($strLang.'FILTER_ENTITY_TITLE').'" data-role="profile-list-entity"');?>
					</td>
					<td>
						<input type="text" class="wda-profile-list__search" data-role="profile-list-search"
							placeholder="<?=Helper::getMessage($strLang.'SEARCH_PLACEHOLDER');?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="wda-profile-list__main">
		<div class="wda-profile-list__inner">
			<table class="adm-list-table"data-role="profile-list-table">
				<thead>
					<tr class="adm-list-table-header">
						<td class="adm-list-table-cell" style="width:10px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_ICON');?></div>
						</td>
						<?/*
						<td class="adm-list-table-cell adm-list-table-cell-sort" style="width:10px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_LOCK');?></div>
						</td>
						*/?>
						<td class="adm-list-table-cell adm-list-table-cell-sort" style="width:10px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_ID');?></div>
						</td>
						<td class="adm-list-table-cell adm-list-table-cell-sort">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_NAME');?></div>
						</td>
						<td class="adm-list-table-cell adm-list-table-cell-sort">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_DESCRIPTION');?></div>
						</td>
						<td class="adm-list-table-cell adm-list-table-cell-sort">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_CODE');?></div>
						</td>
						<td class="adm-list-table-cell adm-list-table-cell-sort adm-list-table-cell-sort-initial">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_SORT');?></div>
						</td>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_CONFIGURED');?></div>
						</td>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_CRON');?></div>
						</td>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner"><?=Helper::getMessage($strLang.'COL_DELETE');?></div>
						</td>
					</tr>
				</thead>
				<tbody id="<?=$strId;?>_rows">
					<?foreach($arProfiles as $arProfile):?>
						<?
						$strSearchText = toLower(implode(' ', array_intersect_key($arProfile, array_flip($arSearch))));
						$strSearchText = str_replace(["\r", "\n", "\t"], ' ', htmlspecialcharsbx($strSearchText));
						while(strpos($strSearchText, '  ') !== false){
							$strSearchText = str_replace('  ', ' ', $strSearchText);
						}
						$strConfirmDelete = htmlspecialcharsbx(Helper::getMessage($strLang.'BUTTON_DELETE_CONFIRM', [
							'#PROFILE_ID#' => $arProfile['ID'],
							'#PROFILE_NAME#' => $arProfile['NAME'],
						]));
						$bCurrent = $arProfile['ID'] == $arParams['PROFILE_ID'];
						$intTasks = $arProfileTasksCount[$arProfile['ID']];
						#
						$strEntityTitle = '';
						$strEntityIcon = '';
						if($arProfile['ENTITY_TYPE'] == Helper::TYPE_ELEMENT){
							$strEntityTitle = Helper::getMessage($strLang.'TYPE_ELEMENT');
							$strEntityIcon = 'wda-icon-element';
						}
						elseif($arProfile['ENTITY_TYPE'] == Helper::TYPE_SECTION){
							$strEntityTitle = Helper::getMessage($strLang.'TYPE_SECTION');
							$strEntityIcon = 'wda-icon-section';
						}
						$strTitle = $arProfile['DESCRIPTION'];
						$strDate = is_object($arProfile['DATE_START']) ? $arProfile['DATE_START']->toString() : '---';
						$strTitle .= Helper::getMessage($strLang.'DATE_START', ['#DATETIME#' => $strDate]);
						$strDate = is_object($arProfile['DATE_FINISH']) ? $arProfile['DATE_FINISH']->toString() : '---';
						$strTitle .= Helper::getMessage($strLang.'DATE_FINISH', ['#DATETIME#' => $strDate]);
						?>
						<tr class="adm-list-table-row<?if($bCurrent):?> wda-profile-list__row-current<?endif?>"
							data-profile-id="<?=$arProfile['ID'];?>" data-search="<?=$strSearchText;?>"
							data-entity-type="<?=$arProfile['ENTITY_TYPE'];?>"
							title="<?=htmlspecialcharsbx($strTitle);?>">
							<td class="adm-list-table-cell wda-profile-list__entity" title="<?=$strEntityTitle;?>">
								<span class="<?=$strEntityIcon;?>"></span>
							</td>
							<?/*
							<td class="adm-list-table-cell">
								
							</td>
							*/?>
							<td class="adm-list-table-cell">
								<?=$arProfile['ID'];?>
							</td>
							<td class="adm-list-table-cell wda-profile-list__cell-profile-name">
								<?=$arProfile['NAME'];?>
							</td>
							<td class="adm-list-table-cell">
								<?=$arProfile['DESCRIPTION'];?>
							</td>
							<td class="adm-list-table-cell">
								<?=$arProfile['CODE'];?>
							</td>
							<td class="adm-list-table-cell">
								<?=$arProfile['SORT'];?>
							</td>
							<td class="adm-list-table-cell wda-profile-list__button wda-profile-list__configured"
								<?if($intTasks > 0):?>
									title="<?=Helper::getMessage($strLang.'CRON_TITLE', ['#COUNT#' => $intTasks]);?>"
								<?endif?>
								>
								<?if($intTasks > 0):?>
									<span class="wda-profile-list__configured-y wda-icon-check"><?=$intTasks;?></span>
								<?else:?>
									<span class="wda-profile-list__configured-n"></span>
								<?endif?>
							</td>
							<td class="adm-list-table-cell wda-profile-list__button wda-profile-list__cron">
								<input type="button" value="" data-role="profile-list-cron" class="wda-icon-cron"
									title="<?=Helper::getMessage($strLang.'BUTTON_CRON_TITLE');?>" />
							</td>
							<td class="adm-list-table-cell wda-profile-list__button wda-profile-list__delete">
								<input type="button" value="" data-role="profile-list-delete" class="wda-icon-delete"
									data-confirm="<?=$strConfirmDelete;?>"
									title="<?=Helper::getMessage($strLang.'BUTTON_DELETE_TITLE');?>" />
							</td>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
			<div class="wda-profile-list__empty" data-role="profile-list-empty" 
				<?if(empty($arProfiles)):?> style="display:block;"<?endif?>><?
				print Helper::getMessage($strLang.'NO_PROFILES');?></div>
		</div>
	</div>
</div>
