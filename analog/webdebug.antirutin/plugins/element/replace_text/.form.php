<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arCodeConfig = Helper::getIBlockFields($this->intIBlockId, 'CODE', true);

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'data-role="field"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('mode');?>">
			<?=Helper::selectBox($this->getInputName('mode'), [
				'simple' => static::getMessage('MODE_SIMPLE'),
				'multiline' => static::getMessage('MODE_MULTILINE'),
				'reg_exp' => static::getMessage('MODE_REG_EXP'),
				'append' => static::getMessage('MODE_APPEND'),
				'prepend' => static::getMessage('MODE_PREPEND'),
			], $this->get('mode'), false, 'data-role="mode"');?>
		</div>
	</div>
</div>

<?// Simple ?>
<div class="plugin-form__fields-group" data-role="mode_settings" data-code="simple" style="display:none;">
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('SIMPLE_SEARCH', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('simple_search');?>" cols="80" data-role="simple_search"
				><?=htmlspecialcharsbx($this->get('simple_search'));?></textarea>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('SIMPLE_REPLACE', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('simple_replace');?>" rows="8" cols="80"
				data-role="simple_replace">
<?=htmlspecialcharsbx($this->get('simple_replace'));?></textarea>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<span>
			<input type="hidden" name="<?=$this->getInputName('simple_case_sensitive');?>" value="N" />
			<input type="checkbox" name="<?=$this->getInputName('simple_case_sensitive');?>" value="Y" 
				<?if($this->get('simple_case_sensitive') == 'Y'):?>checked="checked"<?endif?> 
				id="<?=$this->getId('simple_case_sensitive');?>" data-role="simple_case_sensitive" />
		</span>
		<label for="<?=$this->getId('simple_case_sensitive');?>">
			<?=$this->fieldName('SIMPLE_CASE_SENSITIVE', true, false);?>
		</label>
	</div>
	<?//?>
</div>

<?// Multiline ?>
<div class="plugin-form__fields-group" data-role="mode_settings" data-code="multiline" style="display:none;">
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('MULTILINE_SEARCH', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('multiline_search');?>" cols="80" data-role="multiline_search"
				style="height:200px;max-height:2000px;"><?=htmlspecialcharsbx($this->get('multiline_search'));?></textarea>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('MULTILINE_REPLACE', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('multiline_replace');?>" rows="8" cols="80" data-role="multiline_replace"
				style="height:60px;">
<?=htmlspecialcharsbx($this->get('multiline_replace'));?></textarea>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<span>
			<input type="hidden" name="<?=$this->getInputName('multiline_case_sensitive');?>" value="N" />
			<input type="checkbox" name="<?=$this->getInputName('multiline_case_sensitive');?>" value="Y" 
				<?if($this->get('multiline_case_sensitive') == 'Y'):?>checked="checked"<?endif?> 
				id="<?=$this->getId('multiline_case_sensitive');?>" data-role="multiline_case_sensitive" />
		</span>
		<label for="<?=$this->getId('multiline_case_sensitive');?>">
			<?=$this->fieldName('MULTILINE_CASE_SENSITIVE', true, false);?>
		</label>
	</div>
	<?//?>
</div>

<?// RegExp ?>
<div class="plugin-form__fields-group" data-role="mode_settings" data-code="reg_exp" style="display:none;">
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('REG_EXP_SEARCH', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('reg_exp_search');?>" cols="80" data-role="reg_exp_search"
				><?=htmlspecialcharsbx($this->get('reg_exp_search'));?></textarea>
		</div>
		<div style="margin:0 0 12px;">
			<?=static::getMessage('REG_EXP_SEARCH_HELP');?>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('REG_EXP_REPLACE', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('reg_exp_replace');?>" rows="8" cols="80"
				data-role="reg_exp_replace">
<?=htmlspecialcharsbx($this->get('reg_exp_replace'));?></textarea>
		</div>
	</div>
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title" style="font-weight:normal;">
			<?=$this->fieldName('REG_EXP_LIMIT', true);?>
		</div>
		<div class="plugin-form__field-value">
			<input type="text" name="<?=$this->getInputName('reg_exp_limit');?>"
				value="<?=htmlspecialcharsbx($this->get('reg_exp_limit'));?>" size="10" />
		</div>
	</div>
	<?//?>
</div>

<?// Append ?>
<div class="plugin-form__fields-group" data-role="mode_settings" data-code="append" style="display:none;">
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('APPEND_TEXT', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('append_text');?>" rows="5" cols="80"
				data-role="append_text">
<?=htmlspecialcharsbx($this->get('append_text'));?></textarea>
		</div>
	</div>
	<?//?>
</div>

<?// Prepend ?>
<div class="plugin-form__fields-group" data-role="mode_settings" data-code="prepend" style="display:none;">
	<?//?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('PREPEND_TEXT', true);?>
		</div>
		<div class="plugin-form__field-value">
			<textarea name="<?=$this->getInputName('prepend_text');?>" rows="5" cols="80"
				data-role="prepend_text">
<?=htmlspecialcharsbx($this->get('prepend_text'));?></textarea>
		</div>
	</div>
	<?//?>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="error_no_simple_search" value="<?=static::getMessage('ERROR_NO_SIMPLE_SEARCH');?>" />
<input type="hidden" data-role="error_no_reg_exp_search" value="<?=static::getMessage('ERROR_NO_REG_EXP_SEARCH');?>" />
<input type="hidden" data-role="error_no_append_text" value="<?=static::getMessage('ERROR_NO_APPEND_TEXT');?>" />
<input type="hidden" data-role="error_no_prepend_text" value="<?=static::getMessage('ERROR_NO_PREPEND_TEXT');?>" />
