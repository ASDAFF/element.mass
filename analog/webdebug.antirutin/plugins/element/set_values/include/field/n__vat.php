<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$arVatList = ['' => '---'];
foreach(Helper::getVatList(true) as $intVatId => $strVatName){
	$arVatList[$intVatId] = $strVatName;
}

?>
<?=$this->selectBox($this->getInputName('value', $bMultiple), $arVatList, $mValue, null, 'class="wda-no-min-width"');?>
