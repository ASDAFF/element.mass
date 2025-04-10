<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('select');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'data-role="field"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('include_subsections');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('include_subsections');?>" value="Y" 
			<?if($this->get('include_subsections') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('include_subsections');?>" data-role="include_subsections" />
	</span>
	<label for="<?=$this->getId('include_subsections');?>">
		<?=$this->fieldName('INCLUDE_SUBSECTIONS', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('filter_active');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('filter_active');?>" value="Y" 
			<?if($this->get('filter_active') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('filter_active');?>" data-role="filter_active" />
	</span>
	<label for="<?=$this->getId('filter_active');?>">
		<?=$this->fieldName('FILTER_ACTIVE', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('filter_active_date');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('filter_active_date');?>" value="Y" 
			<?if($this->get('filter_active_date') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('filter_active_date');?>" data-role="filter_active_date" />
	</span>
	<label for="<?=$this->getId('filter_active_date');?>">
		<?=$this->fieldName('FILTER_ACTIVE_DATE', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('additional_filter');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('additional_filter');?>" value="Y" 
			<?if($this->get('additional_filter') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('additional_filter');?>" data-role="additional_filter" />
	</span>
	<label for="<?=$this->getId('additional_filter');?>">
		<?=$this->fieldName('ADDITIONAL_FILTER', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ADDITIONAL_FILTER_PHP', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('additional_filter_php');?>">
			<textarea name="<?=$this->getInputName('additional_filter_php');?>" 
				data-role="additional_filter_php" cols="50" rows="6"><?
					print htmlspecialcharsbx($this->get('additional_filter_php'));
				?></textarea>
		</div>
	</div>
</div>


<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
