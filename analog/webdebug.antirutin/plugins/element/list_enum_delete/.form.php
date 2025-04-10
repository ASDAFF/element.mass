<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arUnusedEnums = [];
$arSavedValue = [];
$strField = $this->get('field');
$bFieldSelected = strlen($strField) && array_key_exists($strField, $this->getFieldsPlain());
if($bFieldSelected && $intPropertyId = $this->isProperty($strField)){
	// Get unused enums list
	$arUnusedEnums = array_map(function($arProp){
		$arProp['DATA'] = $arProp;
		return IBlock::displayAvailableItemName($arProp);
	}, $this->getUnusedEnums($intPropertyId));
	// Get saved values
	$arSavedValue = $this->get('value');
	$arSavedValue = is_array($arSavedValue) ? $arSavedValue : (strlen($arSavedValue) ? [$arSavedValue] : []);
}



?>

<?Helper::showError(static::getMessage('WARNING'));?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'data-role="field"', true, false, false, true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('select_delete_enums');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('select_delete_enums');?>" value="Y" 
			<?if($this->get('select_delete_enums') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('select_delete_enums');?>" data-role="select_delete_enums" />
	</span>
	<label for="<?=$this->getId('select_delete_enums');?>">
		<?=$this->fieldName('SELECT_DELETE_ENUMS', true, false);?>
	</label>
</div>

<div class="plugin-form__field" data-role="delete_enums_wrapper" style="display:none">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('DELETE_ENUMS', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('delete_enums');?>">
			<select name="<?=$this->getInputName('value', true);?>" data-role="value" multiple="multiple" size="10">
				<?if($bFieldSelected):?>
					<?=$this->buildSelectOptions($this->get('field'), $arSavedValue);?>
				<?endif?>
			</select>
		</div>
	</div>
</div>
