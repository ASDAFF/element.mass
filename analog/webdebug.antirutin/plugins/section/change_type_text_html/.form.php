<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;
?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SOURCE_FIELD_TITLE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter), $this->strEntityType, $this->getInputName('source_field', true),
				$this->get('source_field'), 'data-role="source_field" multiple="multiple" size="4"', true, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TYPE_FIELD_TITLE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('type_field');?>">
			<?=Helper::selectBox($this->getInputName('type_field'), [
				'TEXT' => static::getMessage('CHANGE_TO_TEXT'),
				'HTML' => static::getMessage('CHANGE_TO_HTML'),
			], $this->get('type_field'), false, 'data-role="type_field"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('HTML_TO_TEXT_TITLE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('html_to_text');?>">
			<?=Helper::selectBox($this->getInputName('html_to_text'), [
				'N' => static::getMessage('HTML_TO_TEXT_N'),
				'Y' => static::getMessage('HTML_TO_TEXT_Y'),
			], $this->get('html_to_text'), false, 'data-role="html_to_text"');?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_source_field" value="<?=static::getMessage('ERROR_NO_SOURCE_FIELD');?>" />