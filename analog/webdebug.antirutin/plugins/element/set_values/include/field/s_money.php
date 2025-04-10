<?
namespace WD\Antirutin;

use \WD\Antirutin\Helper;

if(!\Bitrix\Main\Loader::includeModule('currency')){
	print Helper::showError(static::getMessage('NO_MODULE_CURRENCY'));
	return;
}

$arCurrencies = [];
foreach(Helper::getCurrencyList() as $strCurrency => $arCurrency){
	$arCurrencies[$strCurrency] = sprintf('[%s] %s', $strCurrency, $arCurrency['FULL_NAME']);
}

$arSavedCurrency = $this->get('currency');
$mCurrency = is_array($arSavedCurrency) ? $arSavedCurrency[$intValueKey] : null;

$strId = Helper::randString(true);

?>
<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="<?=$intColCount;?>" />
<div style="display:inline-block;">
	<?=Helper::selectBox($this->getInputName('currency', $bMultiple), $arCurrencies, $mCurrency, null, 'id="'.$strId.'"');?>
</div>
