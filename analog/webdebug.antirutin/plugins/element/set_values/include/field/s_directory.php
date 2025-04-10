<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$arEnums = [];
if($intPropertyId = $this->isProperty($strField)){
	$arEnums = IBlock::getPropertyHighloadItems($intPropertyId);
}

?>
<div data-role="field_select_wrapper">
	<select name="<?=$this->getInputName('value', $bMultiple);?>">
		<option value="">---</option>
		<?foreach($arEnums as $arEnum):?>
			<option value="<?=$arEnum['UF_XML_ID'];?>"<?if($arEnum['UF_XML_ID'] == $mValue):?> selected="selected"<?endif?>
				><?=$arEnum['UF_NAME'];?> [<?=$arEnum['UF_XML_ID'];?>]</option>
		<?endforeach?>
	</select>
</div>