<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('action');?>">
			<?=Helper::selectBox($this->getInputName('type'), [
				'auto' => static::getMessage('TYPE_AUTO'),
				'set' => static::getMessage('TYPE_SET'),
				'group' => static::getMessage('TYPE_GROUP'),
			], $this->get('type'), null, 'data-role="type"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PRICE_TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('price_type'),
				$this->get('price_type'), 'data-role="price_type"', false, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('CURRENCY_ID', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arCurrencies = array_map(function($arCurrency){
				return Helper::formatCurrency($arCurrency);
			}, Helper::getCurrencyList());
			print Helper::selectBox($this->getInputName('currency_id'), $arCurrencies, $this->get('currency_id'), null, 
				'data-role="currency_id"');
			?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('zero_price');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('zero_price');?>" value='Y'
			   <?if($this->get('zero_price') == 'Y'):?>checked="checked"<?endif?>
			id="<?=$this->getId('zero_price');?>" data-role="settings_zero_price"
			   data-default="N" />
	</span>
	<label for="<?=$this->getId('zero_price');?>">
		<?=$this->fieldName('ZERO_PRICE', true, false);?>
	</label>
</div>

<input type="hidden" data-role="error_no_action" value="<?=static::getMessage('ERROR_NO_ACTION');?>" />
<input type="hidden" data-role="error_no_section" value="<?=static::getMessage('ERROR_NO_SECTION');?>" />
