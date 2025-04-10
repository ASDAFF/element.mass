<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$strCurrency = $this->arSavedData['currency'];

$intPrice = IBlock::isPrice($strField);

static $arBasePrice = null;
if(is_null($arBasePrice)){
	$arBasePrice = \CCatalogGroup::getBaseGroup();
	$arBasePrice = is_array($arBasePrice) ? $arBasePrice : [];
}

$bBasePrice = $arBasePrice['ID'] == $intPrice;

$bMarkup = $this->get('price_mode') == 'markup';
$bInterval = $this->get('price_mode') == 'interval';
$bPrice = !$bMarkup && !$bInterval;

$arCatalog = Helper::getCatalogArray($this->intIBlockId);

$strRandId = Helper::randString(true);

?>
<?if($intPrice):?>
	<br/>
	<div id="<?=$strRandId;?>_sel">
		<div class="wda-radio-selector" style="display:inline-block;">
			<label>
				<input type="radio" name="<?=$this->getInputName('price_mode', $bMultiple);?>" value="value"
					<?if($bPrice):?> checked="checked"<?endif?> />
				<span><?=static::getMessage('PRICE_PRICE_MODE_VALUE');?></span>
			</label>
			<?if(!$bBasePrice):?>
				<label>
					<input type="radio" name="<?=$this->getInputName('price_mode', $bMultiple);?>" value="markup"
						<?if($bMarkup):?> checked="checked"<?endif?> />
					<span><?=static::getMessage('PRICE_PRICE_MODE_MARKUP');?></span>
				</label>
			<?endif?>
			<label>
				<input type="radio" name="<?=$this->getInputName('price_mode', $bMultiple);?>" value="interval"
					<?if($bInterval):?> checked="checked"<?endif?> />
				<span><?=static::getMessage('PRICE_PRICE_MODE_INTERVAL');?></span>
			</label>
		</div>
	</div>
	<br/>
<?endif?>

<div>
	<div id="<?=$strRandId;?>_price_value">
		<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="15" />
		<div style="display:inline-block;">
			<?=$this->selectBox($this->getInputName('currency', $bMultiple), Helper::getCurrencyList(true), $strCurrency, null, 
				'class="wda-no-min-width"');?>
		</div>
		<?if($intPrice):?>
			<div style="margin-top:6px;" id="<?=$strRandId;?>_del">
				<?=Helper::showNote(static::getMessage('PRICE_VALUE_DELETE_NOTICE'), true);?>
			</div>
		<?endif?>
	</div>
	<?if($intPrice && !$bBasePrice):?>
		<div id="<?=$strRandId;?>_price_markup" style="display:none;">
			<?=\CExtra::selectBox($this->getInputName('markup', $bMultiple), $this->get('markup'), 
				static::getMessage('PRICE_PRICE_MODE_DELETE'), '', '');?>
		</div>
	<?endif?>
	<div id="<?=$strRandId;?>_price_interval" style="display:none;">
		<?
		$arIntervals = $this->getIntervalPrices();
		do {
			$arIntervals = array_merge([null], $arIntervals);
		} while(count($arIntervals) < 2);
		?>
		<style>
			#<?=$strRandId;?>_price_interval_table thead td {
				font-weight:bold;
			}
			#<?=$strRandId;?>_price_interval_table tbody > tr:first-child td {
				display:none!important;
			}
			#<?=$strRandId;?>_price_interval_table tbody > tr:nth-child(1) input[data-role="wda_interval_delete"],
			#<?=$strRandId;?>_price_interval_table tbody > tr:nth-child(2) input[data-role="wda_interval_delete"] {
				display:none!important;
			}
			#<?=$strRandId;?>_price_interval_table .select2-container--default {
				width:100%!important;
			}
		</style>
		<table id="<?=$strRandId;?>_price_interval_table">
			<thead>
				<tr>
					<td><?=static::getMessage('PRICE_PRICE_MODE_INTERVAL_FROM');?></td>
					<td><?=static::getMessage('PRICE_PRICE_MODE_INTERVAL_TO');?></td>
					<td><?=static::getMessage('PRICE_PRICE_MODE_INTERVAL_PRICE');?></td>
					<td><?=static::getMessage('PRICE_PRICE_MODE_INTERVAL_CURRENCY');?></td>
					<td></td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<input type="button" value="<?=static::getMessage('PRICE_PRICE_MODE_INTERVAL_ADD');?>"
							data-role="wda_interval_add" />
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?foreach($arIntervals as $key => $arInterval):?>
					<?
					$bFirst = $key == 0;
					?>
					<tr>
						<td>
							<input type="text" name="<?=$this->getInputName('interval_from', true);?>" style="width:60px;"
								value="<?=htmlspecialcharsbx($arInterval['from']);?>" />
						</td>
						<td>
							<input type="text" name="<?=$this->getInputName('interval_to', true);?>" style="width:60px;"
								value="<?=htmlspecialcharsbx($arInterval['to']);?>" />
						</td>
						<td>
							<input type="text" name="<?=$this->getInputName('interval_price', true);?>" style="width:120px;"
								value="<?=htmlspecialcharsbx($arInterval['price']);?>" />
						</td>
						<td>
							<?=$this->selectBox($this->getInputName('interval_currency', true), Helper::getCurrencyList(true),
								$arInterval['currency'], null, 'data-no-style="Y"', !$bFirst);?>
						</td>
						<td>
							<input type="button" value="&times;" data-role="wda_interval_delete" />
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	</div>
</div>

<?if($intPrice):?>
	<script>
		// Substitute "delete"
		$('a[data-role="wda_set_values_price_value_delete"]', '#<?=$strRandId;?>_del').bind('click', function(e){
			e.preventDefault();
			$(this).closest('[data-role="field_input"]').find('input[type="text"][name^="actions"][name*="[value]"]').val($(this).text());
		});
		// Select price value type
		$('input[type="radio"]', '#<?=$strRandId;?>_sel').bind('change', function(e){
			e.preventDefault();
			let
				current = $('input[type="radio"]:checked', '#<?=$strRandId;?>_sel').val();
				$('#<?=$strRandId;?>_price_' + current).show().siblings().hide();
		});
		$('input[type="radio"]:checked', '#<?=$strRandId;?>_sel').trigger('change');
		// Add price interval
		$('input[data-role="wda_interval_add"]', '#<?=$strRandId;?>_price_interval').bind('click', function(e){
			e.preventDefault();
			let
				tbody = $('#<?=$strRandId;?>_price_interval_table > tbody'),
				row = tbody.children().first(),
				newRow = row.clone();
			tbody.append(newRow);
			wdaSelect2($('select', newRow));
		});
		// Delete price interval
		$(document).on('click', '#<?=$strRandId;?>_price_interval input[data-role="wda_interval_delete"]', function(e){
			e.preventDefault();
			let
				row = $(this).closest('tr');
			row.fadeOut(200, function(){
				$(this).remove();
			});
		});
	</script>
<?endif?>