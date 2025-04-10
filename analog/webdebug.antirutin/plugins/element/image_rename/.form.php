<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<?=Helper::showNote(static::getMessage('WARNING'), true);?>
<br/>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field', true),
				$this->get('field'), 'multiple="multiple" size="8" data-role="field"', true, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('METHOD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('method');?>">
			<?=Helper::selectBox($this->getInputName('method'), [
				'TRANSLITERATE_NAME' => static::getMessage('METHOD_TRANSLITERATE_NAME'),
				'USE_ORIGINAL_NAME' => static::getMessage('METHOD_USE_ORIGINAL_NAME'),
			], $this->get('method'), null, 'data-role="method"');?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
