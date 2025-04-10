<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$intSort = intVal($this->get('sort'));
if($intSort <= 0){
	$intSort = 500;
}

print Helper::showError(static::getMessage('WARNING'));

$arUnusedProps = array_map(function($arProp){
	$arProp['DATA'] = $arProp;
	return IBlock::displayAvailableItemName($arProp);
}, $this->getUnusedProperties());

?>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('select_delete_props');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('select_delete_props');?>" value="Y" 
			<?if($this->get('select_delete_props') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('select_delete_props');?>" data-role="select_delete_props" />
	</span>
	<label for="<?=$this->getId('select_delete_props');?>">
		<?=$this->fieldName('SELECT_DELETE_PROPS', true, false);?>
	</label>
</div>

<div class="plugin-form__field" data-role="delete_props_wrapper" style="display:none">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('DELETE_PROPS', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('delete_props');?>">
			<?
			$mValue = $this->get('delete_props');
			if(!is_array($mValue) || empty($mValue)){
				$mValue = array_keys($arUnusedProps);
			}
			?>
			<?=Helper::selectBox($this->getInputName('delete_props', true), $arUnusedProps, $mValue,
				null, 'data-role="delete_props" multiple size="12" style="min-width:400px;"', null, false);?>
		</div>
	</div>
</div>
