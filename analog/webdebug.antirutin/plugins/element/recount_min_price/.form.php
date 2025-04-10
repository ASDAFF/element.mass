<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PRICE_TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('price_type', true),
				$this->get('price_type'), 'data-role="source_price" multiple="multiple" size="8"', true, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('including_main_price');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('including_main_price');?>" value="Y" 
			<?if($this->get('including_main_price') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('including_main_price');?>" data-role="including_main_price" />
	</span>
	<label for="<?=$this->getId('including_main_price');?>">
		<?=$this->fieldName('INCLUDING_MAIN_PRICE', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('with_discounts');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('with_discounts');?>" value="Y" 
			<?if($this->get('with_discounts') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('with_discounts');?>" data-role="with_discounts" />
	</span>
	<label for="<?=$this->getId('with_discounts');?>">
		<?=$this->fieldName('WITH_DISCOUNTS', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('use_active');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('use_active');?>" value="Y" 
			<?if($this->get('use_active') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('use_active');?>" data-role="use_active" />
	</span>
	<label for="<?=$this->getId('use_active');?>">
		<?=$this->fieldName('USE_ACTIVE', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('use_available');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('use_available');?>" value="Y" 
			<?if($this->get('use_available') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('use_available');?>" data-role="use_available" />
	</span>
	<label for="<?=$this->getId('use_available');?>">
		<?=$this->fieldName('USE_AVAILABLE', true, false);?>
	</label>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SITE_ID');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arSitesId = array_map(function($arSite){
				return Helper::formatSite($arSite);
			}, Helper::getSitesList());
			print Helper::selectBox($this->getInputName('site_id'), $arSitesId, $this->get('site_id'), null, 
				'data-role="site_id"');
			?>
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
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TARGET', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter2), $this->strEntityType, 
				$this->getInputName('target'), $this->get('target'), 'data-role="target_field"', true);?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_source_price" value="<?=static::getMessage('ERROR_NO_SOURCE_PRICE');?>" />
<input type="hidden" data-role="error_no_target_field" value="<?=static::getMessage('ERROR_NO_TARGET_FIELD');?>" />
