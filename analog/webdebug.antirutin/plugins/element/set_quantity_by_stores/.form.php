<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('mode');?>">
			<?=Helper::selectBox($this->getInputName('mode'), [
				'product' => static::getMessage('MODE_PRODUCT'),
				'offers' => static::getMessage('MODE_OFFERS'),
				'all' => static::getMessage('MODE_ALL'),
			], $this->get('mode'), null, 'data-role="mode"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('STORES', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('stores');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('stores', true),
				$this->get('stores'), 'multiple="multiple" size="8" data-role="stores"', true, false, false, false);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<label>
		<input type="hidden" name="<?=$this->getInputName('product_save_to_property');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('product_save_to_property');?>" value="Y"
			data-role="save_to_property" <?if($this->get('product_save_to_property') == 'Y'):?>checked="checked"<?endif?> />
		<?=$this->fieldName('PRODUCT_SAVE_TO_PROPERTY', true, false);?>
	</label>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value" style="margin:8px 0 0 20px;">
			<div id="<?=$this->getId('product_save_property');?>">
				<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilterSaveProduct), $this->strEntityType, 
					$this->getInputName('product_save_property'),
					$this->get('product_save_property'), 'data-role="save_property"', true);?>
			</div>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<label>
		<input type="hidden" name="<?=$this->getInputName('offer_save_to_property');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('offer_save_to_property');?>" value="Y"
			data-role="save_to_property" <?if($this->get('offer_save_to_property') == 'Y'):?>checked="checked"<?endif?> />
		<?=$this->fieldName('OFFER_SAVE_TO_PROPERTY', true, false);?>
	</label>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value" style="margin:8px 0 0 20px;">
			<div id="<?=$this->getId('offer_save_property');?>">
				<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilterSaveOffer), $this->strEntityType, 
					$this->getInputName('offer_save_property'),
					$this->get('offer_save_property'), 'data-role="save_property"', true);?>
			</div>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<label>
		<input type="hidden" name="<?=$this->getInputName('save_offers_summ_to_property');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('save_offers_summ_to_property');?>" value="Y"
			data-role="save_to_property" <?if($this->get('save_offers_summ_to_property') == 'Y'):?>checked="checked"<?endif?> />
		<?=$this->fieldName('SAVE_OFFERS_SUMM_TO_PROPERTY', true, false);?>
	</label>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value" style="margin:8px 0 0 20px;">
			<div id="<?=$this->getId('save_offers_summ_property');?>">
				<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilterSaveProduct), $this->strEntityType, 
					$this->getInputName('save_offers_summ_property'),
					$this->get('save_offers_summ_property'), 'data-role="save_property"', true);?>
			</div>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_stores" value="<?=static::getMessage('ERROR_NO_STORES');?>" />
