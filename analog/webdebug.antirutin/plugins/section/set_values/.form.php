<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$strCode = strlen($this->get('php_code')) ? $this->get('php_code') : $this->getDemoCode();

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
	<div class="plugin-form__field-title">
		<?=$this->fieldName('VALUE', true);?>
	</div>
	<div class="plugin-form__field-value" data-role="field_value">
		<?=$this->getInputHtml($this->get('field'), $this->get(static::INPUT_VALUE), $this->get(static::INPUT_DESCRIPTION));?>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-value">
		<label>
			<input type="hidden" name="<?=$this->getInputName('use_custom_php');?>" value="N" />
			<input type="checkbox" name="<?=$this->getInputName('use_custom_php');?>" value="Y" data-role="use_custom_php"
				<?if($this->get('use_custom_php') == 'Y'):?>checked="checked"<?endif?> />
			<?=$this->fieldName('USE_CUSTOM_PHP', true, false);?>
		</label>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PHP_CODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('php_code');?>">
			<textarea name="<?=$this->getInputName('php_code');?>" data-role="php_code" rows="8" cols="60"
				placeholder="<?=$this->getDemoCode();?>">
<?=htmlspecialcharsbx($strCode);?></textarea>
		</div>
		<div>
			<label>
				<input type="hidden" name="<?=$this->getInputName('php_debug_mode');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('php_debug_mode');?>" value="Y" data-role="php_debug_mode"
					<?if($this->get('php_debug_mode') != 'N'):?>checked="checked"<?endif?> />
				<?=$this->fieldName('PHP_DEBUG_MODE', true, false);?>
			</label>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
