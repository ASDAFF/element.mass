<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arProducts = $this->getSavedProducts();
$arProducts = array_merge([null], $arProducts);

?>

<div class="wda_product_set" id="<?=$this->getId();?>">
	<span><?=static::getMessage('TYPE');?>:</span>
	<input type="radio" name="<?=$this->getInputName('type');?>" value="2" id="<?=$this->getId('type');?>_2"
		<?if($this->get('type') != '1'):?>checked<?endif?> />
	<label for="<?=$this->getId('type');?>_2"><?=static::getMessage('TYPE_2');?>:</label>
	<input type="radio" name="<?=$this->getInputName('type');?>" value="1" id="<?=$this->getId('type');?>_1"
		<?if($this->get('type') == '1'):?>checked<?endif?> />
	<label for="<?=$this->getId('type');?>_1"><?=static::getMessage('TYPE_1');?>:</label>
	<div class="wda_product_set-table_wrapper adm-list-table-wrap">
		<table class="adm-list-table">
			<thead>
				<tr class="adm-list-table-header">
					<td class="wda_product_set-col_product adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_PRODUCT');?></div>
					</td>
					<td class="wda_product_set-col_amount adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_AMOUNT');?></div>
					</td>
					<td class="wda_product_set-col_sort adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_SORT');?></div>
					</td>
					<td class="wda_product_set-col_percent adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_PERCENT');?></div>
					</td>
					<td class="wda_product_set-col_delete adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_DELETE');?></div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProducts as $arProduct):?>
					<?
					$arProduct = is_array($arProduct) ? $arProduct : [];
					$intProductId = intVal($arProduct['id']);
					$strProductName = '';
					if($intProductId > 0 && \Bitrix\Main\Loader::includeModule('iblock')){
						if($arElement = \CIBlockElement::getList([], ['ID' => $intProductId], false, false, ['NAME'])->fetch()){
							$strProductName = $arElement['NAME'];
						}
					}
					?>
					<tr class="adm-list-table-row">
						<td class="wda_product_set-col_product adm-list-table-cell align-right">
							<div class="wda_product_set-product_select">
								<input type="text" name="<?=$this->getInputName('products');?>[id][]" value="<?=htmlspecialcharsbx($arProduct['id']);?>" size="10" maxlength="10" />
								<input type="button" value="..." data-role="product_select" />
							</div>
							<div class="wda_product_set-product_name"><?=$strProductName;?></div>
						</td>
						<td class="wda_product_set-col_amount adm-list-table-cell align-right">
							<input type="text" name="<?=$this->getInputName('products');?>[amount][]" value="<?=htmlspecialcharsbx($arProduct['amount']);?>" size="10" maxlength="10" data-default="1" />
						</td>
						<td class="wda_product_set-col_sort adm-list-table-cell align-right">
							<input type="text" name="<?=$this->getInputName('products');?>[sort][]" value="<?=htmlspecialcharsbx($arProduct['sort']);?>" size="10" maxlength="10" data-default="100" />
						</td>
						<td class="wda_product_set-col_percent adm-list-table-cell align-right">
							<input type="text" name="<?=$this->getInputName('products');?>[percent][]" value="<?=htmlspecialcharsbx($arProduct['percent']);?>" size="10" maxlength="3" />
						</td>
						<td class="wda_product_set-col_delete adm-list-table-cell align-right">
							<input type="button" value="&times;" title="<?=static::getMessage('ROW_DELETE');?>" data-role="row_delete" />
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	</div>
	<div class="wda_product_set-button_add">
		<input type="button" value="<?=static::getMessage('ROW_ADD');?>" data-role="row_add" />
	</div>
</div>
