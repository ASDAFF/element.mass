<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strId = Helper::randString();

$strLang = 'WDA_POPUP_PROFILE_SAVE_';
$strHint = $strLang.'HINT_';

$intProfileId = IntVal($arParams['PROFILE_ID']);
$arProfile = $arParams['PROFILE_ARRAY'];

if(!strlen($arProfile['NAME'])){
	$arProfile['NAME'] = Helper::getMessage($strLang.'FIELD_NAME_DEFAULT');
}
if(!is_numeric($arProfile['SORT']) || $arProfile['SORT'] < 0){
	$arProfile['SORT'] = 100;
}

?>
<div class="profile-save__form wda-form">
	<form action="<?=POST_FORM_ACTION_URI;?>" method="post" data-role="wda_profile_save_form">
		<?if($intProfileId > 0):?>
			<div class="profile-save__form-field">
				<div class="profile-save__form-field-value">
					<div class="wda-radio-selector wda-not-select" data-role="profile-save-type">
						<label>
							<input type="radio" name="profile-save-type" value="existing" checked="checked" />
							<span><?=Helper::getMessage($strLang.'TYPE_EXISTING', ['#ID#' => $intProfileId]);?></span>
						</label>
						<label>
							<input type="radio" name="profile-save-type" value="new" />
							<span><?=Helper::getMessage($strLang.'TYPE_NEW');?></span>
						</label>
					</div>
				</div>
			</div>
		<?endif?>
		<div class="profile-save__form-field">
			<div class="profile-save__form-field-title">
				<b><?=Helper::getMessage($strLang.'FIELD_NAME');?></b>
				<?=Helper::showHint(Helper::getMessage($strHint.'FIELD_NAME'));?>
			</div>
			<div class="profile-save__form-field-value">
				<input type="text" name="name" value="<?=htmlspecialcharsbx($arProfile['NAME']);?>" size="40" 
					data-role="profile-save-name" />
			</div>
		</div>
		<div class="profile-save__form-field profile-save__form-field-50">
			<div class="profile-save__form-field-title">
				<?=Helper::getMessage($strLang.'FIELD_CODE');?>
				<?=Helper::showHint(Helper::getMessage($strHint.'FIELD_CODE'));?>
			</div>
			<div class="profile-save__form-field-value">
				<input type="text" name="code" value="<?=htmlspecialcharsbx($arProfile['CODE']);?>" size="40"
					data-role="profile-save-code" />
			</div>
		</div>
		<div class="profile-save__form-field profile-save__form-field-50">
			<div class="profile-save__form-field-title">
				<?=Helper::getMessage($strLang.'FIELD_SORT');?>
				<?=Helper::showHint(Helper::getMessage($strHint.'FIELD_SORT'));?>
			</div>
			<div class="profile-save__form-field-value">
				<input type="text" name="sort" value="<?=htmlspecialcharsbx($arProfile['SORT']);?>" size="40"
					data-role="profile-save-sort" />
			</div>
		</div>
		<div class="profile-save__form-clear"></div>
		<div class="profile-save__form-field">
			<div class="profile-save__form-field-title">
				<?=Helper::getMessage($strLang.'FIELD_DESCRIPTION');?>
				<?=Helper::showHint(Helper::getMessage($strHint.'FIELD_DESCRIPTION'));?>
			</div>
			<div class="profile-save__form-field-value">
				<textarea name="description" rows="3" cols="40" data-role="profile-save-description"
					placeholder="<?=Helper::getMessage($strLang.'PLACEHOLDER_DESCRIPTION');?>"
						><?=htmlspecialcharsbx($arProfile['DESCRIPTION']);?></textarea>
			</div>
		</div>
		<div data-role="wda_form_hidden_submit">
			<input type="submit" value="" />
		</div>
	</form>
</div>
