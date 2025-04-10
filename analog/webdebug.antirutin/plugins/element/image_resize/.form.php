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
				'BX_RESIZE_IMAGE_PROPORTIONAL' => static::getMessage('METHOD_BX_RESIZE_IMAGE_PROPORTIONAL'),
				'BX_RESIZE_IMAGE_EXACT' => static::getMessage('METHOD_BX_RESIZE_IMAGE_EXACT'),
				'BX_RESIZE_IMAGE_PROPORTIONAL_ALT' => static::getMessage('METHOD_BX_RESIZE_IMAGE_PROPORTIONAL_ALT'),
			], $this->get('method'), null, 'data-role="method"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('WIDTH', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('width');?>">
			<input type="text" name="<?=$this->getInputName('width');?>" 
				value="<?=htmlspecialcharsbx($this->get('width'));?>" size="15" data-role="width"
				placeholder="<?=static::getMessage('WIDTH_PLACEHOLDER');?>" />
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('HEIGHT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('height');?>">
			<input type="text" name="<?=$this->getInputName('height');?>"
				value="<?=htmlspecialcharsbx($this->get('height'));?>" size="15" data-role="height"
				placeholder="<?=static::getMessage('HEIGHT_PLACEHOLDER');?>" />
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SHARPEN', true);?>
	</div>
	<div class="plugin-form__field-value">
		<input type="hidden" name="<?=$this->getInputName('sharpen');?>" value="N" />
		<label>
			<input type="checkbox" name="<?=$this->getInputName('sharpen');?>" value="Y" 
				<?if($this->get('sharpen') == 'Y'):?>checked="checked"<?endif?> 
				id="<?=$this->getId('sharpen');?>" data-role="sharpen" />
			<span><?=Helper::getMessage('WDA_Y');?></span>
		</label>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="error_no_width" value="<?=static::getMessage('ERROR_NO_WIDTH');?>" />
<input type="hidden" data-role="error_no_height" value="<?=static::getMessage('ERROR_NO_HEIGHT');?>" />
