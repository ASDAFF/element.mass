<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PROPERTY_FROM', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('source');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter), $this->strEntityType, $this->getInputName('source'),
				$this->get('source'), 'data-role="source"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PROPERTY_TO', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('target');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter2), $this->strEntityType, $this->getInputName('target'),
				$this->get('target'), 'data-role="target"', true);?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_source_field" value="<?=static::getMessage('ERROR_NO_SOURCE_FIELD');?>" />
<input type="hidden" data-role="error_no_target_field" value="<?=static::getMessage('ERROR_NO_TARGET_FIELD');?>" />
