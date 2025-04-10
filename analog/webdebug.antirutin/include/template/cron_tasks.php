<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Cli,
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_PROFILE_CRON_';

$bCanAutoSet = Cli::canAutoSet();
	
$intProfileId = $arParams['PROFILE_ID'];

$arProfileTasks = Cli::getProfileCronTasks($intProfileId);
$arProfileTasksDisplay = array_merge([true], $arProfileTasks);
$arCommand = Cli::getFullCommand(null, $intProfileId);

?>
<div class="wda-profile-cron" data-role="cron-tasks">
	<?if($bCanAutoSet):?>
		<input type="hidden" data-role="cron-tasks-profile-id" value="<?=$intProfileId;?>" />
		<div class="adm-list-table-wrap adm-list-table-without-header adm-list-table-without-footer">
			<table class="adm-list-table">
				<thead>
					<tr class="adm-list-table-header">
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'EXTERNAL_ID');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'MINUTE');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'HOUR');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'DAY');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'MONTH');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'WEEKDAY');?>
						</div></td>
						<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
							<?=Helper::getMessage($strLang.'DELETE');?>
						</div></td>
					</tr>
				</thead>
				<tbody>
					<?foreach($arProfileTasksDisplay as $arTask):?>
						<?
						$bHidden = $arTask === true;
						if($bHidden){
							$arTask = [
								'EXTERNAL_ID' => '__EXTERNAL_ID__',
								'MINUTE' => '*',
								'HOUR' => '*',
								'DAY' => '*',
								'MONTH' => '*',
								'WEEKDAY' => '*',
							];
						}
						$strId = $arTask['EXTERNAL_ID'];
						?>
						<tr class="adm-list-table-row<?if($bHidden):?> adm-list-table-row-hidden<?endif?>">
							<td class="adm-list-table-cell">
								<span data-role="cron-task-external-id"><?=$strId;?></span>
								<input type="hidden" name="external[<?=$strId;?>]" 
									value="<?=htmlspecialcharsbx($arTask['EXTERNAL_ID']);?>"
										data-role="cron-task-external-id"/>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" size="6" name="minute[<?=$strId;?>]" maxlength="50"
									value="<?=htmlspecialcharsbx($arTask['MINUTE']);?>" />
							</td>
							<td class="adm-list-table-cell">
								<input type="text" size="6" name="hour[<?=$strId;?>]" maxlength="50"
									value="<?=htmlspecialcharsbx($arTask['HOUR']);?>" />
							</td>
							<td class="adm-list-table-cell">
								<input type="text" size="6" name="day[<?=$strId;?>]" maxlength="50"
									value="<?=htmlspecialcharsbx($arTask['DAY']);?>" />
							</td>
							<td class="adm-list-table-cell">
								<input type="text" size="6" name="month[<?=$strId;?>]" maxlength="50" 
									value="<?=htmlspecialcharsbx($arTask['MONTH']);?>" />
							</td>
							<td class="adm-list-table-cell">
								<input type="text" size="6" name="weekday[<?=$strId;?>]" maxlength="50"
									value="<?=htmlspecialcharsbx($arTask['WEEKDAY']);?>" />
							</td>
							<td class="adm-list-table-cell adm-list-table-cell-last">
								<input type="button" value="" data-role="cron-task-delete" class="wda-icon-delete"
									title="<?=Helper::getMessage($strLang.'BUTTON_DELETE');?>"
									data-confirm="<?=Helper::getMessage($strLang.'BUTTON_DELETE_CONFIRM');?>" />
							</td>
						</tr>
					<?endforeach?>
					<tr class="wda-profile-cron__line-empty" data-role="cron-tasks-line-empty">
						<td colspan="7"><?=Helper::getMessage($strLang.'NO_TASKS');?></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?else:?>
		<div>
			<?=Helper::showNote(Helper::getMessage($strLang.'CANNOT_AUTOSET'), true);?>
			<script>$('#wda_profile_cron_save, #wda_profile_cron_add_more').remove();</script>
			<input type="hidden" data-role="cron-tasks-cannot-autoset" value="Y" />
		</div>
	<?endif?>
	<br/>
	<?=Helper::getMessage($strLang.'COMMAND');?><br/>
	<?Helper::P($arCommand['COMMAND']);?><br/>
	<?=Helper::getMessage($strLang.'LOGS_NOTICE');?><br/>
</div>