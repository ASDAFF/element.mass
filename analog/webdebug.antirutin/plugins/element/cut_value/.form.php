<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;
?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SOURCE_FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('source_field');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter), $this->strEntityType, $this->getInputName('source_field'),
				$this->get('source_field'), 'data-role="source_field"', true);?>
		</div>
	</div>
</div>
<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('CUT_FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('cut_field');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter2), $this->strEntityType, $this->getInputName('cut_field'),
				$this->get('cut_field'), 'data-role="cut_field"', true);?>
		</div>
	</div>
</div>
<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('case_sensitive');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('case_sensitive');?>" value='Y'
			   <?if($this->get('case_sensitive') == 'Y'):?>checked="checked"<?endif?>
			id="<?=$this->getId('case_sensitive');?>" data-role="settings_case_sensitive"
			   data-default="N" />
	</span>
	<label for="<?=$this->getId('case_sensitive');?>">
		<?=$this->fieldName('CASE_SENSITIVE', true, false);?>
	</label>
</div>
<div class="plugin-form__field">
	<span>
		<input type='hidden' name="<?=$this->getInputName('leave_duplicate_space');?>" value='N' />
		<input type='checkbox' name="<?=$this->getInputName('leave_duplicate_space');?>" value='Y'
			   <?if($this->get('leave_duplicate_space') == 'Y'):?>checked='checked'<?endif?>
			id="<?=$this->getId('leave_duplicate_space');?>" data-role='settings_leave_duplicate_space'
			   data-default='N' />
	</span>
	<label for="<?=$this->getId('leave_duplicate_space');?>">
		<?=$this->fieldName('LEAVE_DUPLICATE_SPACE', true, false);?>
	</label>
</div>


<input type="hidden" data-role="error_no_source_field" value="<?=static::getMessage('ERROR_NO_SOURCE_FIELD');?>" />
<input type="hidden" data-role="error_no_cut_field" value="<?=static::getMessage('ERROR_NO_CUT_FIELD');?>" />
