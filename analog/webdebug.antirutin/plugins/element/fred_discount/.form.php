<?
use
	WD\Antirutin\Helper;
?>

<div class="plugin-form__field">
	<?=Helper::showNote(static::getMessage('NO_SETTINGS_REQUIRED'), true);?>
</div>

<?if(!$this->isFredDiscountModuleInstalled()):?>
	<?=Helper::showError(static::getMessage('ERROR_NO_MODULE'));?>
	<input type="hidden" data-role="wda_fred_discount_error" value="Y" />
<?elseif($this->getFredDiscountOption('IBLOCK_ID') != $this->intIBlockId):?>
	<?=Helper::showError(static::getMessage('ERROR_WRONG_IBLOCK_ID', ['#IBLOCK_ID#' => $this->getFredDiscountOption('IBLOCK_ID')]));?>
	<input type="hidden" data-role="wda_fred_discount_error" value="Y" />
<?elseif(!strlen($this->getFredDiscountOption('DISCOUNT_PROPERTY'))):?>
	<?=Helper::showError(static::getMessage('ERROR_EMPTY_PROPERTY'));?>
	<input type="hidden" data-role="wda_fred_discount_error" value="Y" />
<?endif?>
