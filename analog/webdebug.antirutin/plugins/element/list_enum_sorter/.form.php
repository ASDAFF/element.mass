<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$intSort = intVal($this->get('sort'));
if($intSort <= 0){
	$intSort = 500;
}

?>

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
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SORT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<input type="text" name="<?=$this->getInputName('sort');?>" data-role="sort"
				value="<?=$intSort;?>" size="10" />
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="error_no_sort" value="<?=static::getMessage('ERROR_NO_SORT');?>" />
