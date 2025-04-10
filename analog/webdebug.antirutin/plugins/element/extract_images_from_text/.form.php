<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('HTML_FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('html_field');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter), $this->strEntityType, $this->getInputName('html_field'),
				$this->get('html_field'), 'data-role="html_field"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD_PICTURE_MAIN');?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field_picture_main');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsPictureMain), $this->strEntityType, $this->getInputName('field_picture_main'),
				$this->get('field_picture_main'), 'data-role="field_picture_main"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD_PICTURE_MORE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field_picture_more');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsPictureMore), $this->strEntityType, $this->getInputName('field_picture_more'),
				$this->get('field_picture_more'), 'data-role="field_picture_more"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_TEXT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('mode_text');?>">
			<?=Helper::selectBox($this->getInputName('mode_text'), [
				static::MODE_TEXT_CUT => static::getMessage('MODE_TEXT_CUT'),
				static::MODE_TEXT_COPY => static::getMessage('MODE_TEXT_COPY'),
			], $this->get('mode_text'), false, 'data-role="mode_text"');?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_html_field" value="<?=static::getMessage('ERROR_NO_HTML_FIELD');?>" />
