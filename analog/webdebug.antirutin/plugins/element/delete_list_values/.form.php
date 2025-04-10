<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$strField = $this->get('field');
$bFieldSelected = strlen($strField) && array_key_exists($strField, $this->getFieldsPlain());

$arValue = $this->get('value');
$arValue = is_array($arValue) ? $arValue : (strlen($arValue) ? [$arValue] : []);

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

<div class="plugin-form__field"<?if(!$bFieldSelected):?> style="display:none;"<?endif?>>
	<div class="plugin-form__field-title">
		<?=$this->fieldName('VALUE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('value');?>">
			<div data-role="select_value">
				<select name="<?=$this->getInputName('value', true);?>" data-role="value" multiple="multiple" size="10">
					<?if($bFieldSelected):?>
						<?=$this->buildSelectOptions($this->get('field'), $arValue);?>
					<?endif?>
				</select>
			</div>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="error_no_value" value="<?=static::getMessage('ERROR_NO_VALUE');?>" />
