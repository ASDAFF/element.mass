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
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter1), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'size="4" data-role="field"', static::getMessage('FIELD_PLACEHOLDER'), false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PROPERTY', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('property');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('property'),
				$this->get('property'), 'size="8" data-role="property"', false, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('METHOD', true);?>
	</div>
	<div class="plugin-form__field-value" data-role="duplicate_methods">
		<?foreach(static::getDuplicateMethods() as $strParamCode => $strParmaName):?>
			<?$strInputName = sprintf('method_%s', $strParamCode);?>
			<div style="margin-bottom:4px;">
				<input type="hidden" name="<?=$this->getInputName($strInputName);?>" value="N" />
				<label>
					<input type="checkbox" name="<?=$this->getInputName($strInputName);?>" value="Y" 
						<?if($this->get($strInputName) == 'Y'):?>checked="checked"<?endif?> 
						id="<?=$this->getId($strInputName);?>" />
					<span><?=$strParmaName;?></span>
				</label>
			</div>
		<?endforeach?>
	</div>
</div>

<input type="hidden" data-role="error_no_property" value="<?=static::getMessage('ERROR_NO_PROPERTY');?>" />
<input type="hidden" data-role="error_no_methods" value="<?=static::getMessage('ERROR_NO_METHODS');?>" />
