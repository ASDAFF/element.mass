<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('PRICES', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('prices');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('prices', true),
				$this->get('prices'), 'multiple="multiple" size="8" data-role="prices"', true, false, false, false);?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_prices" value="<?=static::getMessage('ERROR_NO_PRICES');?>" />
