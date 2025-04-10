<?
use
	WD\Antirutin\Helper;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PICTURE_TYPE_ELEMENT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('picture_type_element');?>">
			<?=Helper::selectBox($this->getInputName('picture_type_element'), [
				'detail' => static::getMessage('PICTURE_TYPE_DETAIL'),
				'preview' => static::getMessage('PICTURE_TYPE_PREVIEW'),
			], $this->get('picture_type_element'), null, 'data-role="picture_type_element"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PICTURE_TYPE_SECTION', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('picture_type_section');?>">
			<?=Helper::selectBox($this->getInputName('picture_type_section'), [
				'detail' => static::getMessage('PICTURE_TYPE_DETAIL'),
				'preview' => static::getMessage('PICTURE_TYPE_PREVIEW'),
			], $this->get('picture_type_section'), null, 'data-role="picture_type_section"');?>
		</div>
	</div>
</div>

