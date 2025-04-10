<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$strCurrency = $this->arSavedData['currency'];

?>
<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="15" />
<?=$this->selectBox($this->getInputName('currency', $bMultiple), Helper::getCurrencyList(true), $strCurrency, null, 
	'class="wda-no-min-width"');?>