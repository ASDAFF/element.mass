<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\ProfileTable as Profile;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_RESULTS_';

$arSession = &$arParams['SESSION'];

$strTitle = Helper::getMessage($strLang.'HEADER', ['#TYPE#' => Helper::getMessage($strLang.'TYPE_'.$arParams['TYPE'])]);

# Percent
if($arSession['COUNT']){
	$strPercentSuccess = number_format($arSession['COUNT_SUCCESS'] * 100 / $arSession['COUNT'], 2, '.', '');
	$strPercentError = number_format($arSession['COUNT_ERROR'] * 100 / $arSession['COUNT'], 2, '.', '');
}
else{
	$strPercentSuccess = $strPercentError = number_format(0, 2, '.', '');
}

# Time
$fSeconds = $arSession['TIME_END'] - $arSession['TIME_START'];
$init = 685;
$intHours = floor($fSeconds / 3600);
$intMinutes = floor(($fSeconds / 60) % 60);
$intSeconds = round($fSeconds % 60);
$intFraction = round(($fSeconds - floor($fSeconds)) * 100);
$strTimeElapsed = sprintf("%02d:%02d:%02d.%01d", $intHours, $intMinutes, $intSeconds, $intFraction);

# Table
$arTable = [
	[
		Helper::getMessage($strLang.'TIME_START'),
		\Bitrix\Main\Type\DateTime::createFromTimestamp($arSession['TIME_START'])->toString(),
	],
	[
		Helper::getMessage($strLang.'TIME_END'),
		sprintf('%s (%s)', \Bitrix\Main\Type\DateTime::createFromTimestamp($arSession['TIME_END'])->toString(),
			$strTimeElapsed),
	],
	[
		Helper::getMessage($strLang.'COUNT_ALL', ['#TYPE#' => Helper::getMessage($strLang.'TYPE_'.$arParams['TYPE'])]),
		sprintf('<div class="wda-results__count">%s</div>', $arSession['COUNT']),
	],
	[
		Helper::getMessage($strLang.'COUNT_SUCCESS'),
		sprintf('<div class="wda-results__count_success">%s (%s%%)</div>', $arSession['COUNT_SUCCESS'], $strPercentSuccess),
	],
];
if($arSession['FIRST_ERROR']){
	$arTable[] = [
		Helper::getMessage($strLang.'COUNT_ERROR'),
		sprintf('<div class="wda-results__count_error">%s (%s%%)</div>', $arSession['COUNT_ERROR'], $strPercentError),
	];
}

?>
<div class="wda-results">
	<h2 class="wda-results__title"><?=$strTitle;?></h2>
	<table class="wda-results__table">
		<tbody>
			<?foreach($arTable as $arRow):?>
				<tr>
					<td><?=$arRow[0];?></td>
					<td><?=$arRow[1];?></td>
				</tr>
			<?endforeach?>
			<tr><td colspan="2"></td><tr>
			<tr>
				<td colspan="2">
					<?=Helper::getMessage($strLang.'ACTIONS');?><br/>
					<ul class="wda-results__actions">
						<?foreach($arSession['ACTIONS'] as $strHash => $arAction):?>
							<li data-hash="<?=$strHash;?>"><?=$arAction['_title'];?></li>
						<?endforeach?>
					</ul>
				</td>
			</tr>
			<?if(strlen($arSession['FIRST_ERROR'])):?>
				<tr>
					<td colspan="2">
						<hr/>
						<?=Helper::getMessage($strLang.'FIRST_ERROR');?><br/>
						<?=sprintf('<div class="wda-results__error_text">%s</div>', $arSession['FIRST_ERROR']);?>
					</td>
				</tr>
			<?endif?>
		</tbody>
	</table>
</div>
