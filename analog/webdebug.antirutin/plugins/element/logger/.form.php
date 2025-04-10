<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arFieldsAll = $this->getFields();
$arFieldsPlain = $this->getFieldsPlain();

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FORMAT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('format');?>">
			<?=Helper::selectBox($this->getInputName('format'), [
				'CSV' => static::getMessage('FORMAT_CSV'),
				'TXT' => static::getMessage('FORMAT_TXT'),
			], $this->get('format'), null, 'data-role="format"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FILENAME', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('filename');?>">
			<?
			$strFilename = $this->get('filename');
			if(!strlen($strFilename)){
				$strFilename = '/antirutin_export_element.txt';
			}
			?>
			<input type="text" name="<?=$this->getInputName('filename');?>" value="<?=$strFilename;?>" 
				size="60" maxlength="255" data-role="filename" />
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ENCODING', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('encoding');?>">
			<?=Helper::selectBox($this->getInputName('encoding'), [
				'UTF-8' => 'UTF-8',
				'CP1251' => 'windows-1251',
			], $this->get('encoding'), null, 'data-role="encoding"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SITE_DOMAIN', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('site_domain');?>">
			<input type="text" name="<?=$this->getInputName('site_domain');?>" value="<?=$this->get('site_domain');?>" 
				size="60" maxlength="255" data-role="site_domain"
				placeholder="<?=static::getMessage('SITE_DOMAIN_PLACEHOLDER');?>" />
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<?=Helper::showHeading($this->fieldName('FIELDS', true));?>
	<table data-role="fields">
		<tbody>
			<tr>
				<td data-role="fields_column_all">
					<div data-role="fields_header">
						<?=static::getMessage('FIELDS_ALL');?>
					</div>
					<div data-role="fields_filter">
						<input type="text" placeholder="<?=static::getMessage('FILTER_PLACEHOLDER');?>" />
					</div>
					<select size="12" multiple="multiple" data-role="fields_all">
						<?foreach($arFieldsAll as $strGroup => $arGroup):?>
							<?if(is_array($arGroup['ITEMS']) && !empty($arGroup['ITEMS'])):?>
								<optgroup label="<?=$arGroup['NAME'];?>">
									<?foreach($arGroup['ITEMS'] as $strField => $arField):?>
										<?
										$strField = $arGroup['PREFIX'].$strField;
										?>
										<option value="<?=$strField;?>"><?=IBlock::displayAvailableItemName($arField);?></option>
									<?endforeach?>
								</optgroup>
							<?endif?>
						<?endforeach?>
					</select>
				</td>
				<td data-role="fields_column_middle">
					<input type="button" value="<?=static::getMessage('MOVE_RIGHT');?>" class="move_right"
						data-role="fields_move_right" />
				</td>
				<td data-role="fields_column_selected">
					<div data-role="fields_header">
						<?=static::getMessage('FIELDS_SELECTED');?>
					</div>
					<div data-role="fields_filter">
						<input type="text" placeholder="<?=static::getMessage('FILTER_PLACEHOLDER');?>" />
					</div>
					<select size="12" multiple="multiple" data-role="fields_selected">
						<?foreach($this->getValuesArray($this->get('fields')) as $strField):?>
							<?if(array_key_exists($strField, $arFieldsPlain)):?>
								<option value="<?=$strField;?>"><?=IBlock::displayAvailableItemName($arFieldsPlain[$strField]);
									?></option>
							<?endif?>
						<?endforeach?>
					</select>
					<input type="hidden" name="<?=$this->getInputName('fields');?>" value="<?=$this->get('fields');?>" 
						data-role="fields_serialized" />
				</td>
				<td data-role="fields_column_controls">
					<div>
						<input type="button" value="<?=static::getMessage('MOVE_UP');?>" 
							data-role="fields_move_up" />
					</div>
					<br/>
					<div>
						<input type="button" value="<?=static::getMessage('MOVE_DOWN');?>"
							data-role="fields_move_down" />
					</div>
					<br/>
					<div>
						<input type="button" value="<?=static::getMessage('DELETE');?>" 
							data-role="fields_delete" />
						</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FOR_EXCEL', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('for_excel');?>">
			<?=Helper::selectBox($this->getInputName('for_excel'), [
				'Y' => Helper::getMessage('WDA_Y'),
				'N' => Helper::getMessage('WDA_N'),
			], $this->get('for_excel'), null, 'data-role="for_excel"');?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_filename" value="<?=static::getMessage('ERROR_NO_FILENAME');?>" />
<input type="hidden" data-role="error_no_csv_fields" value="<?=static::getMessage('ERROR_NO_CVS_FIELDS');?>" />
