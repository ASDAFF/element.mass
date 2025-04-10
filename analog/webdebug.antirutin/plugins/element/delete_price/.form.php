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
				$this->get('price_type'), 'data-role="price_type" multiple="multiple" size="8"', true, false, false, false);?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_source_price" value="<?=static::getMessage('ERROR_NO_SOURCE_PRICE');?>" />
