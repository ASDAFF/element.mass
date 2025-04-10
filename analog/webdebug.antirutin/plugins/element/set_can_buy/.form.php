<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_SOURCE');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arModes = [
				static::MODE_ALL_PRICES_AND_QUANTITY => static::getMessage('MODE_ALL_PRICES_AND_QUANTITY'),
				static::MODE_ONE_PRICE_AND_QUANTITY => static::getMessage('MODE_ONE_PRICE_AND_QUANTITY'),
				static::MODE_ALL_PRICES => static::getMessage('MODE_ALL_PRICES'),
				static::MODE_ONE_PRICE => static::getMessage('MODE_ONE_PRICE'),
				static::MODE_QUANTITY => static::getMessage('MODE_QUANTITY'),
			];
			print Helper::selectBox($this->getInputName('mode_source'), $arModes, $this->get('mode_source'), null, 
				'data-role="mode_source"');
			?>
		</div>
	</div>
</div>

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

<?/*
<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('with_offers');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('with_offers');?>" value="Y" 
			<?if($this->get('with_offers') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('with_offers');?>" data-role="with_offers" />
	</span>
	<label for="<?=$this->getId('with_offers');?>">
		<?=$this->fieldName('WITH_OFFERS', true, false);?>
	</label>
</div>
*/?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_OFFERS');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arModes = [
				static::MODE_OFFERS_Y => static::getMessage('MODE_OFFERS_Y'),
				static::MODE_OFFERS_N => static::getMessage('MODE_OFFERS_N'),
				static::MODE_OFFERS_F => static::getMessage('MODE_OFFERS_F'),
			];
			print Helper::selectBox($this->getInputName('mode_offers'), $arModes, $this->get('mode_offers'), null, 
				'data-role="mode_offers"');
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
				$this->getInputName('target'), $this->get('target'), 'data-role="target"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_TEXT');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arModes = [
				static::MODE_VALUE__1_0 => static::getMessage('MODE_VALUE__1_0'),
				static::MODE_VALUE__1_EMPTY => static::getMessage('MODE_VALUE__1_EMPTY'),
				static::MODE_VALUE__QUANTITY => static::getMessage('MODE_VALUE__QUANTITY'),
			];
			print Helper::selectBox($this->getInputName('mode_text'), $arModes, $this->get('mode_text'), null, 
				'data-role="mode_text"');
			?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_NUMERIC');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arModes = [
				static::MODE_VALUE__1_0 => static::getMessage('MODE_VALUE__1_0'),
				static::MODE_VALUE__1_EMPTY => static::getMessage('MODE_VALUE__1_EMPTY'),
				static::MODE_VALUE__QUANTITY => static::getMessage('MODE_VALUE__QUANTITY'),
			];
			print Helper::selectBox($this->getInputName('mode_numeric'), $arModes, $this->get('mode_numeric'), null, 
				'data-role="mode_numeric"');
			?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE_LIST');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arModes = [
				static::MODE_VALUE__ENUM_Y => static::getMessage('MODE_VALUE__ENUM_Y'),
				static::MODE_VALUE__ENUM_YES => static::getMessage('MODE_VALUE__ENUM_YES'),
				static::MODE_VALUE__ENUM_FIRST => static::getMessage('MODE_VALUE__ENUM_FIRST'),
				static::MODE_VALUE__ENUM_LAST => static::getMessage('MODE_VALUE__ENUM_LAST'),
			];
			print Helper::selectBox($this->getInputName('mode_list'), $arModes, $this->get('mode_list'), null, 
				'data-role="mode_list"');
			?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_source_price" value="<?=static::getMessage('ERROR_NO_SOURCE_PRICE');?>" />
<input type="hidden" data-role="error_no_target_field" value="<?=static::getMessage('ERROR_NO_TARGET_FIELD');?>" />
