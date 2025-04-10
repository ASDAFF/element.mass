<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$arMeasure = Helper::getMeasuresList(true);

?>
<?=$this->selectBox($this->getInputName('value', $bMultiple), $arMeasure, $mValue, null, 'class="wda-no-min-width"');?>
