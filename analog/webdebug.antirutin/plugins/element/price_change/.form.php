<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arPriceChange = $this->getPriceChange(true);

$arBaseCurrency = $this->getBaseCurrency();

$arCatalog = Helper::getCatalogArray($this->intIBlockId);

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SOURCE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('source');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('source'),
				$this->get('source'), 'data-role="source"', true, false, false, true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SOURCE_CURRENCY', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('source_currency');?>">
			<?
			$arCurrencies = array_map(function($arCurrency){
				return Helper::formatCurrency($arCurrency);
			}, Helper::getCurrencyList());
			?>
			<?=Helper::selectBox($this->getInputName('source_currency'), $arCurrencies, $this->get('source_currency'), false, 
				'data-role="source_currency"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TARGET', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('target');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('target'), 
				$this->get('target'), 'data-role="target"', true, false, false, true);?>
		</div>
	</div>
</div>

<div class="plugin-form__fields-group">
	<?// Use discounts ?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('use_discounts');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('use_discounts');?>" value="Y" 
					<?if($this->get('use_discounts') == 'Y'):?> checked="checked"<?endif?> data-role="use_discounts" />
				<?=$this->fieldName('USE_DISCOUNTS', true, false);?>
			</label>
			<div data-role="use_discounts_settings" style="display:inline-block;">
				<?
					$arSitesId = array_map(function($arSite){
						return Helper::formatSite($arSite);
					}, Helper::getSitesList());
					print Helper::selectBox($this->getInputName('discounts_site_id'), $arSitesId,
						$this->get('discounts_site_id'), null, 'data-role="discounts_site_id"');
				?>
			</div>
		</div>
	</div>
	<?// Change currency ?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('change_currency');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('change_currency');?>" value="Y" 
					<?if($this->get('change_currency') == 'Y'):?> checked="checked"<?endif?> data-role="change_currency" />
				<?=$this->fieldName('CHANGE_CURRENCY', true, false);?>
			</label>
			<div data-role="change_currency_settings" style="display:inline-block;">
				<?
				$arCurrencies = array_map(function($arCurrency){
					return Helper::formatCurrency($arCurrency);
				}, Helper::getCurrencyList());
				?>
				<?=Helper::selectBox($this->getInputName('currency_id'), $arCurrencies, $this->get('currency_id'), false, 
					'data-role="currency_id"');?>
			</div>
		</div>
	</div>
	<?// Change price ?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('change_price');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('change_price');?>" value="Y" 
					<?if($this->get('change_price') == 'Y'):?> checked="checked"<?endif?> data-role="change_price" />
				<?=$this->fieldName('CHANGE_PRICE', true, false);?>
			</label>
			<div data-role="change_price_settings" style="padding:10px 20px;">
				<?if(!empty($arBaseCurrency)):?>
					<p>
						<?=static::getMessage('CHANGE_PRICE_NOTICE', ['#CURRENCY#' => Helper::formatCurrency($arBaseCurrency)]);?>
					</p>
				<?endif?>
				<table>
					<tbody>
						<?foreach($arPriceChange as $arPriceChangeItem):?>
							<tr>
								<td data-role="change_price_value">
									<input name="<?=$this->getInputName('change_price_value', true);?>" type="text"
										placeholder="<?=static::getMessage('CHANGE_PRICE_PLACEHOLDER_PRICE');?>" 
										value="<?=htmlspecialcharsbx($arPriceChangeItem['VAL']);?>" size="20" />
								</td>
								<td data-role="change_price_text_1">
									<?=static::getMessage('CHANGE_PRICE_TEXT_1');?>
								</td>
								<td data-role="change_price_min">
									<input name="<?=$this->getInputName('change_price_min', true);?>" type="text"
										placeholder="<?=static::getMessage('CHANGE_PRICE_PLACEHOLDER_FROM');?>" 
									value="<?=htmlspecialcharsbx($arPriceChangeItem['MIN']);?>" size="10" />
								</td>
								<td data-role="change_price_text_2">
									<?=static::getMessage('CHANGE_PRICE_TEXT_2');?>
								</td>
								<td data-role="change_price_max">
									<input name="<?=$this->getInputName('change_price_max', true);?>" type="text"
										placeholder="<?=static::getMessage('CHANGE_PRICE_PLACEHOLDER_TO');?>" 
										value="<?=htmlspecialcharsbx($arPriceChangeItem['MAX']);?>" size="10" />
								</td>
								<td data-role="change_price_delete">
									<input type="button" value="&times;" title="<?=static::getMessage('CHANGE_PRICE_DELETE');?>" />
								</td>
							</tr>
						<?endforeach?>
					</tbody>
				</table>
				<input type="button" value="<?=static::getMessage('CHANGE_PRICE_ADD');?>" data-role="change_price_add_row" />
			</div>
		</div>
	</div>
	<?// Limit ?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('limit');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('limit');?>" value="Y" 
					<?if($this->get('limit') == 'Y'):?> checked="checked"<?endif?> data-role="limit" />
				<?=$this->fieldName('LIMIT', true, false);?>
			</label>
			<div data-role="limit_settings" style="display:inline-block;">
				<input type="text" name="<?=$this->getInputName('limit_value');?>" size="10"
					value="<?=htmlspecialcharsbx($this->get('limit_value'));?>" data-role="limit_value" 
					placeholder="<?=static::getMessage('LIMIT_PLACEHOLDER');?>" />
			</div>
		</div>
	</div>
	<?// Round ?>
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('round');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('round');?>" value="Y" 
					<?if($this->get('round') == 'Y'):?> checked="checked"<?endif?> data-role="round" />
				<?=$this->fieldName('ROUND', true, false);?>
			</label>
			<div data-role="round_settings" style="display:inline-block;">
				<div style="display:inline-block;">
					<?=Helper::selectBox($this->getInputName('round_precision'), [
						'-3' => static::getMessage('ROUND_PRECISION_-3'),
						'-2' => static::getMessage('ROUND_PRECISION_-2'),
						'-1' => static::getMessage('ROUND_PRECISION_-1'),
						'0' => static::getMessage('ROUND_PRECISION_0'),
						'1' => static::getMessage('ROUND_PRECISION_1'),
						'2' => static::getMessage('ROUND_PRECISION_2'),
						'3' => static::getMessage('ROUND_PRECISION_3'),
					], $this->get('round_precision'), false, 'data-role="round_precision"');?>
				</div>
				<div style="display:inline-block;">
					<?=Helper::selectBox($this->getInputName('round_type'), [
						'round' => static::getMessage('ROUND_TYPE_ROUND'),
						'ceil' => static::getMessage('ROUND_TYPE_CEIL'),
						'floor' => static::getMessage('ROUND_TYPE_FLOOR'),
					], $this->get('round_type'), false, 'data-role="round_type"');?>
				</div>
			</div>
		</div>
	</div>
	<?// Format ?>
	<?/*
	<div class="plugin-form__field">
		<div class="plugin-form__field-value">
			<input type="hidden" name="<?=$this->getInputName('format');?>" value="N" /> 
			<label>
				<input type="checkbox" name="<?=$this->getInputName('format');?>" value="Y" 
					<?if($this->get('format') == 'Y'):?> checked="checked"<?endif?> data-role="format" />
				<?=$this->fieldName('FORMAT', true, false);?>
			</label>
			<div data-role="format_settings" style="display:inline-block;">
				<?=Helper::selectBox($this->getInputName('format_type'), [
					'add_index' => static::getMessage('EXISTS_ACTION_ADD_INDEX'),
					'add_id_element' => static::getMessage('EXISTS_ACTION_ADD_ID_ELEMENT'),
				], $this->get('format_type'), false, 'data-role="format_type"');?>
			</div>
		</div>
	</div>
	*/?>
	<?// For offers ?>
	<?if($arCatalog['OFFERS_IBLOCK_ID']):?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<input type="hidden" name="<?=$this->getInputName('offers');?>" value="N" /> 
				<label>
					<input type="checkbox" name="<?=$this->getInputName('offers');?>" value="Y" 
						<?if($this->get('offers') == 'Y'):?> checked="checked"<?endif?> data-role="offers" />
					<?=$this->fieldName('OFFERS', true, false);?>
				</label>
			</div>
		</div>
	<?endif?>
	<?//?>
</div>

<input type="hidden" data-role="error_no_source" value="<?=static::getMessage('ERROR_NO_SOURCE');?>" />
<input type="hidden" data-role="error_no_target" value="<?=static::getMessage('ERROR_NO_TARGET');?>" />
