<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

?>
<?=$this->selectBox($this->getInputName('value', $bMultiple), Helper::getCurrencyList(true), $mValue, null, 
	'class="wda-no-min-width"');?>