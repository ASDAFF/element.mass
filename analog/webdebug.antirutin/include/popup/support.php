<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_SUPPORT_';

$arSupport = [
	'NAME' => $GLOBALS['USER']->getFullName(),
	'EMAIL' => $GLOBALS['USER']->getEmail(),
	'MESSAGE' => '',
];

?>
<div class="wda-form">
	<div class="support__form">
		<div class="support__form-field support__form-field-50">
			<div class="support__form-field-title"><?=Helper::getMessage($strLang.'FIELD_NAME');?>:</div>
			<div class="support__form-field-value">
				<input type="text" name="name" value="<?=htmlspecialcharsbx($arSupport['NAME']);?>" size="40" 
					data-role="support-name" />
			</div>
		</div>
		<div class="support__form-field support__form-field-50">
			<div class="support__form-field-title"><?=Helper::getMessage($strLang.'FIELD_EMAIL');?>:</div>
			<div class="support__form-field-value">
				<input type="email" name="email" value="<?=htmlspecialcharsbx($arSupport['EMAIL']);?>" size="40" 
					data-role="support-email" />
			</div>
		</div>
		<div class="support__form-field">
			<div class="support__form-field-title"><?=Helper::getMessage($strLang.'FIELD_SUBJECT');?>:</div>
			<div class="support__form-field-value">
				<select name="subject" data-role="support-subject">
					<option><?=Helper::getMessage($strLang.'FIELD_SUBJECT_GENERAL');?></option>
					<option><?=Helper::getMessage($strLang.'FIELD_SUBJECT_ERROR');?></option>
					<option><?=Helper::getMessage($strLang.'FIELD_SUBJECT_NEW_PLUGIN');?></option>
				</select>
			</div>
		</div>
		<div class="support__form-clear"></div>
		<div class="support__form-field">
			<div class="support__form-field-title"><?=Helper::getMessage($strLang.'FIELD_MESSAGE');?>:</div>
			<div class="support__form-field-value">
				<textarea name="message" rows="7" cols="40" data-role="support-message"
					placeholder="<?=Helper::getMessage($strLang.'FIELD_MESSAGE_PLACEHOLDER');?>"
						><?=htmlspecialcharsbx($arSupport['MESSAGE']);?></textarea>
			</div>
		</div>
		<div>
			<?=Helper::getMessage($strLang.'NOTICE');?>
		</div>
	</div>
</div>
