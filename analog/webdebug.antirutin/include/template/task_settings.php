<?
namespace WD\Antirutin;

use
	WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_TASK_SETTINGS_';
$strHint = $strLang.'HINT_';
$arSettings = unserialize($arParams['PROFILE']['SETTINGS'], ['allowed_classes' => false]);
$arSettings = is_array($arSettings) ? $arSettings : [];
?>
<div class="wda-task-settings-block">
	<table>
		<tbody>
			<tr>
				<td>
					<?=Helper::getMessage($strLang.'STEP_TIME');?>:
					<?=Helper::showHint(Helper::getMessage($strHint.'STEP_TIME'));?>
				</td>
				<td>
					<input type="text" name="settings[step_time]" value="<?=$arSettings['step_time'];?>"
						data-role="wda_settings_step_time" size="5" maxlength="5" />
				</td>
				<td>
					<?
					$strValue = Helper::getOption('step_time');
					?>
					<?=Helper::getMessage($strLang.'MODULE_SETTINGS');?>: <b><?=$strValue;?></b>
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::getMessage($strLang.'SHOW_RESULTS_POPUP');?>:
					<?=Helper::showHint(Helper::getMessage($strHint.'SHOW_RESULTS_POPUP'));?>
				</td>
				<td>
					<?
					$arShowResultsOptions = \WD\Antirutin\Worker::getOptionsShowResults();
					$arShowResultsOptions = array_merge(['D' => static::getMessage($strLang.'MODULE_SETTINGS_VALUE')], 
						$arShowResultsOptions);
					?>
					<?=Helper::selectBox('settings[show_results_popup]', $arShowResultsOptions, 
						$arSettings['show_results_popup'], null, 'data-role="wda_settings_show_results_popup"');?>
				</td>
				<td>
					<?
					$strValue = Helper::getOption('show_results');
					?>
					<?=Helper::getMessage($strLang.'MODULE_SETTINGS');?>: <b><?=$arShowResultsOptions[$strValue];?></b>
				</td>
			</tr>
			<tr>
				<td>
					<?=Helper::getMessage($strLang.'EMAIL_ENABLED');?>:
					<?=Helper::showHint(Helper::getMessage($strHint.'EMAIL_ENABLED'));?>
				</td>
				<td>
					<?
					$arEmailEnabledOptions = \WD\Antirutin\Worker::getOptionsEmailEnabled();
					$arEmailEnabledOptions = array_merge(['D' => static::getMessage($strLang.'MODULE_SETTINGS_VALUE')], 
						$arEmailEnabledOptions);
					?>
					<?=Helper::selectBox('settings[email_enabled]', $arEmailEnabledOptions, 
						$arSettings['email_enabled'], null, 'data-role="wda_settings_email_enabled"');?>
				</td>
				<td>
					<?
					$strValue = Helper::getOption('email_enabled');
					?>
					<?=Helper::getMessage($strLang.'MODULE_SETTINGS');?>: <b><?=$arEmailEnabledOptions[$strValue];?></b>
					<?if($strValue == 'Y'):?>
						- <a href="mailto:<?=Helper::getOption('email_address');?>"><?=Helper::getOption('email_address');?></a>
					<?endif?>
				</td>
			</tr>
			<tr style="display:none;">
				<td>
					<?=Helper::getMessage($strLang.'EMAIL_ADDRESS');?>:
					<?=Helper::showHint(Helper::getMessage($strHint.'EMAIL_ADDRESS'));?>
				</td>
				<td>
					<input type="text" name="settings[email_address]" value="<?=$arSettings['email_address'];?>"
						data-role="wda_settings_email_address" size="32" />
				</td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>
<script>
	$('select[data-role="wda_settings_email_enabled"]').bind('change', function(e){
		$('input[data-role="wda_settings_email_address"]').closest('tr').toggle($(this).val() == 'Y');
	});
	$(document).ready(function(){
		$('select[data-role="wda_settings_email_enabled"]').trigger('change');
	});
</script>
