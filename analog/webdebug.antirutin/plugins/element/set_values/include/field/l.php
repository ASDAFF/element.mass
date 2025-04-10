<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$arEnums = [];
if($intPropertyId = $this->isProperty($strField)){
	$arEnums = IBlock::getPropertyEnums($intPropertyId);
}

?>
<div data-role="field_select_wrapper">
	<select name="<?=$this->getInputName('value', $bMultiple);?>">
		<option value="">---</option>
		<?foreach($arEnums as $arEnum):?>
			<option value="<?=$arEnum['ID'];?>"<?if($arEnum['ID'] == $mValue):?> selected="selected"<?endif?>
				><?=$arEnum['VALUE'];?> [<?=$arEnum['ID'];?>]</option>
		<?endforeach?>
	</select>
</div>