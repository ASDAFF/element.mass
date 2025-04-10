<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SOURCE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('source');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('source'),
				$this->get('source'), 'data-role="source"', true);?>
		</div>
	</div>

	<?// S:directory ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:directory">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_s_directory');?>">
					<?=Helper::selectBox($this->getInputName('source_s_directory'), [
						'title' => static::getMessage('SOURCE_TYPE_TITLE'),
						'value' => static::getMessage('SOURCE_TYPE_CODE'),
						'id' => static::getMessage('SOURCE_TYPE_HIGHLOAD_ID'),
					], $this->get('source_s_directory'), null, 'data-role="source_s_directory"');?>
				</div>
			</div>
		</div>
	</div>

	<?// L ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="L">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_l');?>">
					<?=Helper::selectBox($this->getInputName('source_l'), [
						'value' => static::getMessage('SOURCE_TYPE_TITLE'),
						'xml_id' => static::getMessage('SOURCE_TYPE_XML_ID'),
						'enum_id' => static::getMessage('SOURCE_TYPE_ENUM_ID'),
					], $this->get('source_l'), null, 'data-role="source_l"');?>
				</div>
			</div>
		</div>
	</div>

	<?// E ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="E">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_e');?>">
					<?=Helper::selectBox($this->getInputName('source_e'), [
						'title' => static::getMessage('SOURCE_TYPE_TITLE'),
						'code' => static::getMessage('SOURCE_TYPE_CODE'),
						'xml_id' => static::getMessage('SOURCE_TYPE_XML_ID'),
						'id' => static::getMessage('SOURCE_TYPE_ID'),
					], $this->get('source_e'), null, 'data-role="source_e"');?>
				</div>
			</div>
		</div>
	</div>

	<?// G ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="G">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_g');?>">
					<?=Helper::selectBox($this->getInputName('source_g'), [
						'title' => static::getMessage('SOURCE_TYPE_TITLE'),
						'code' => static::getMessage('SOURCE_TYPE_CODE'),
						'xml_id' => static::getMessage('SOURCE_TYPE_XML_ID'),
						'id' => static::getMessage('SOURCE_TYPE_ID'),
					], $this->get('source_g'), null, 'data-role="source_g"');?>
				</div>
			</div>
		</div>
	</div>

	<?// F ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="F">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_f');?>">
					<?=Helper::selectBox($this->getInputName('source_f'), [
						'src_rel' => static::getMessage('SOURCE_TYPE_SRC_REL'),
						'src_abs' => static::getMessage('SOURCE_TYPE_SRC_ABS'),
						'src_url' => static::getMessage('SOURCE_TYPE_SRC_URL'),
						'description' => static::getMessage('SOURCE_TYPE_DESCRIPTION'),
						'id' => static::getMessage('SOURCE_TYPE_FILE_ID'),
					], $this->get('source_f'), null, 'data-role="source_f"');?>
				</div>
			</div>
		</div>
	</div>

	<?// S:_Checkbox ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:_Checkbox">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('format_s_checkbox');?>">
					<?=Helper::selectBox($this->getInputName('format_s_checkbox'), [
						'yes_no' => static::getMessage('SOURCE_TYPE_BOOLEAN_YES_NO'),
						'y_n' => static::getMessage('SOURCE_TYPE_BOOLEAN_Y_N'),
						'1_0' => static::getMessage('SOURCE_TYPE_BOOLEAN_1_0'),
					], $this->get('format_s_checkbox'), null, 'data-role="format_s_checkbox"');?>
				</div>
			</div>
		</div>
	</div>

	<?// S:_CheckboxD ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:_CheckboxD">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('format_s_checkboxd');?>">
					<?=Helper::selectBox($this->getInputName('format_s_checkboxd'), [
						'yes_no' => static::getMessage('SOURCE_TYPE_BOOLEAN_YES_NO_DEFAULT'),
						'y_n' => static::getMessage('SOURCE_TYPE_BOOLEAN_Y_N_D'),
						'1_0' => static::getMessage('SOURCE_TYPE_BOOLEAN_1_0__'),
					], $this->get('format_s_checkboxd'), null, 'data-role="format_s_checkboxd"');?>
				</div>
			</div>
		</div>
	</div>

	<?// N:_UserId ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="N:_UserId">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_n_userid');?>">
					<?=Helper::selectBox($this->getInputName('source_n_userid'), [
						'default' => static::getMessage('SOURCE_TYPE_USER_DEFAULT'),
						'name_lastname' => static::getMessage('SOURCE_TYPE_USER_NAME_LASTNAME'),
						'name' => static::getMessage('SOURCE_TYPE_USER_NAME'),
						'login' => static::getMessage('SOURCE_TYPE_USER_LOGIN'),
						'email' => static::getMessage('SOURCE_TYPE_USER_EMAIL'),
						'id' => static::getMessage('SOURCE_TYPE_USER_ID'),
					], $this->get('source_n_userid'), null, 'data-role="source_n_userid"');?>
				</div>
			</div>
		</div>
	</div>

	<?// S:UserID ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:UserID">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_s_userid');?>">
					<?=Helper::selectBox($this->getInputName('source_s_userid'), [
						'default' => static::getMessage('SOURCE_TYPE_USER_DEFAULT'),
						'name_lastname' => static::getMessage('SOURCE_TYPE_USER_NAME_LASTNAME'),
						'name' => static::getMessage('SOURCE_TYPE_USER_NAME'),
						'login' => static::getMessage('SOURCE_TYPE_USER_LOGIN'),
						'email' => static::getMessage('SOURCE_TYPE_USER_EMAIL'),
						'id' => static::getMessage('SOURCE_TYPE_USER_ID'),
					], $this->get('source_s_userid'), null, 'data-role="source_s_userid"');?>
				</div>
			</div>
		</div>
	</div>

	<?// S:Money ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:Money">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_s_money');?>">
					<?=Helper::selectBox($this->getInputName('source_s_money'), [
						'default' => static::getMessage('SOURCE_TYPE_MONEY_DEFAULT'),
						'formatted' => static::getMessage('SOURCE_TYPE_MONEY_FORMATTED'),
						'price_currency' => static::getMessage('SOURCE_TYPE_MONEY_PRICE_CURRENCY'),
						'number' => static::getMessage('SOURCE_TYPE_MONEY_NUMBER'),
					], $this->get('source_s_money'), null, 'data-role="source_s_money"');?>
				</div>
			</div>
		</div>
	</div>

	<?// N:_Vat ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="N:_Vat">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_n_vat');?>">
					<?=Helper::selectBox($this->getInputName('source_n_vat'), [
						'text' => static::getMessage('SOURCE_TYPE_VAT_TEXT'),
						'number' => static::getMessage('SOURCE_TYPE_VAT_NUMBER'),
						'id' => static::getMessage('SOURCE_TYPE_VAT_ID'),
					], $this->get('source_n_vat'), null, 'data-role="source_n_vat"');?>
				</div>
			</div>
		</div>
	</div>

	<?// N:_Measure ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="N:_Measure">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_n_measure');?>">
					<?=Helper::selectBox($this->getInputName('source_n_measure'), [
						'title' => static::getMessage('SOURCE_TYPE_MEASURE_TITLE'),
						'symbol_rus' => static::getMessage('SOURCE_TYPE_MEASURE_SYMBOL_RUS'),
						'symbol_int' => static::getMessage('SOURCE_TYPE_MEASURE_SYMBOL_INT'),
						'code_letter' => static::getMessage('SOURCE_TYPE_MEASURE_CODE_LETTER'),
						'code' => static::getMessage('SOURCE_TYPE_MEASURE_CODE'),
						'id' => static::getMessage('SOURCE_TYPE_MEASURE_ID'),
					], $this->get('source_n_measure'), null, 'data-role="source_n_measure"');?>
				</div>
			</div>
		</div>
	</div>

	<?// S:HTML ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-type="S:HTML">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SOURCE_FORMAT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_s_html');?>">
					<?=Helper::selectBox($this->getInputName('source_s_html'), [
						'default' => static::getMessage('SOURCE_TYPE_HTML_DEFAULT'),
						'html_to_text' => static::getMessage('SOURCE_TYPE_HTML_TO_TEXT'),
						'strip_tags' => static::getMessage('SOURCE_TYPE_HTML_STRIP_TAGS'),
					], $this->get('source_s_html'), null, 'data-role="source_s_html"');?>
				</div>
			</div>
		</div>
	</div>

	<?// Price settings ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-code="price_settings">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('OPTIMAL_PRICE_SITE_ID', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('optimal_price_site_id');?>">
					<?=Helper::selectBox($this->getInputName('optimal_price_site_id'), Helper::getSitesList(false, true), 
						$this->get('optimal_price_site_id'), null, 'data-role="optimal_price_site_id"');?>
				</div>
			</div>
		</div>
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('OPTIMAL_PRICE_DELTA', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('optimal_price_delta');?>">
					<input type="text" name="<?=$this->getInputName('optimal_price_delta');?>"
						value="<?=htmlspecialcharsbx($this->get('optimal_price_delta'));?>" size="15" maxlength="15" 
						data-role="optimal_price_delta"
						placeholder="<?=static::getMessage('OPTIMAL_PRICE_DELTA_PLACEHOLDER');?>" />
				</div>
			</div>
		</div>
	</div>

	<?// Tags ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-code="tags_multiple">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<label>
					<input type="hidden" name="<?=$this->getInputName('tags_multiple');?>" value="N" /> 
					<input type="checkbox" name="<?=$this->getInputName('tags_multiple');?>" value="Y" 
						<?if($this->get('tags_multiple') == 'Y'):?> checked="checked"<?endif?> data-role="tags_multiple" />
					<?=$this->fieldName('TAGS_MULTIPLE', true, false);?>
				</label>
			</div>
		</div>
	</div>
	
	<?// Site domain ?>
	<div class="plugin-form__group" data-role="group_settings_source" data-code="site_domain">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('SITE_DOMAIN', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('site_domain');?>">
					<input type="text" name="<?=$this->getInputName('site_domain');?>"
						value="<?=htmlspecialcharsbx($this->get('site_domain'));?>" size="30" maxlength="255"
						data-role="site_domain"
						placeholder="<?=static::getMessage('SITE_DOMAIN_PLACEHOLDER');?>" />
				</div>
			</div>
		</div>
	</div>
	
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TARGET', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('target');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter2), $this->strEntityType, 
				$this->getInputName('target'), $this->get('target'), 'data-role="target"', true);?>
		</div>
	</div>

	<?// Option: html type ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="html_type">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('HTML_TYPE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('html_type');?>">
					<?=Helper::selectBox($this->getInputName('html_type'), [
						'auto' => static::getMessage('HTML_TYPE_AUTO'),
						'html' => static::getMessage('HTML_TYPE_HTML'),
						'text' => static::getMessage('HTML_TYPE_TEXT'),
					], $this->get('html_type'), null, 'data-role="html_type"');?>
				</div>
			</div>
		</div>
	</div>
	
	<?// Option: Multiple ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="multiple_mode">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('MULTIPLE_MODE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('source_s_directory');?>">
					<?=Helper::selectBox($this->getInputName('multiple_mode'), [
						'comma' => static::getMessage('MULTIPLE_MODE_COMMA'),
						'first' => static::getMessage('MULTIPLE_MODE_FIRST'),
						'last' => static::getMessage('MULTIPLE_MODE_LAST'),
						'max' => static::getMessage('MULTIPLE_MODE_MAX'),
						'min' => static::getMessage('MULTIPLE_MODE_MIN'),
					], $this->get('multiple_mode'), null, 'data-role="multiple_mode"');?>
				</div>
			</div>
		</div>
	</div>
	
	<?// Option: Save current values ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="save_current_values">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<label>
					<input type="hidden" name="<?=$this->getInputName(static::SAVE_CURRENT_VALUES);?>" value="N" /> 
					<input type="checkbox" name="<?=$this->getInputName(static::SAVE_CURRENT_VALUES);?>" value="Y" 
						<?if($this->isSaveValues()):?> checked="checked"<?endif?> data-role="save_current_values" />
					<?=$this->fieldName('MULTIPLE_MODE_SAVE_CURRENT_VALUES', true, false);?>
				</label>
			</div>
		</div>
	</div>
	
	<?// Option: Remove duplicates ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="remove_duplicates">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<label>
					<input type="hidden" name="<?=$this->getInputName(static::REMOVE_DUPLICATES);?>" value="N" /> 
					<input type="checkbox" name="<?=$this->getInputName(static::REMOVE_DUPLICATES);?>" value="Y" 
						<?if($this->isRemoveDuplicates()):?> checked="checked"<?endif?> data-role="remove_duplicates" />
					<?=$this->fieldName('MULTIPLE_MODE_REMOVE_DUPLICATES', true, false);?>
				</label>
			</div>
		</div>
	</div>
	
	<?// Option: File convert name ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="file_convert_name">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<label>
					<input type="hidden" name="<?=$this->getInputName('file_convert_name');?>" value="N" /> 
					<input type="checkbox" name="<?=$this->getInputName('file_convert_name');?>" value="Y" 
						<?if($this->get('file_convert_name') == 'Y'):?> checked="checked"<?endif?> data-role="file_convert_name" />
					<?=$this->fieldName('FILE_CONVERT_NAME', true, false);?>
				</label>
				<?if(Helper::getOption('save_original_file_name', false, 'main') != 'Y'):?>
					<?=Helper::showNote(static::getMessage('FILE_CONVERT_NAME_WARNING'));?>
				<?endif?>
			</div>
		</div>
	</div>
	
	<?// Option: Copy ID to element as raw value ?>
	<div class="plugin-form__group" data-role="group_target_settings" data-code="copy_raw_element_id">
		<?//?>
		<div class="plugin-form__field">
			<div class="plugin-form__field-value">
				<label>
					<input type="hidden" name="<?=$this->getInputName('copy_raw_element_id');?>" value="N" /> 
					<input type="checkbox" name="<?=$this->getInputName('copy_raw_element_id');?>" value="Y" 
						<?if($this->get('copy_raw_element_id') == 'Y'):?> checked="checked"<?endif?> data-role="copy_raw_element_id" />
					<?=$this->fieldName('COPY_RAW_ELEMENT_ID', true, false);?>
				</label>
			</div>
		</div>
	</div>
	
</div>
	
<?// Option: Skip null values ?>
<div class="plugin-form__field">
	<div class="plugin-form__field-value">
		<label>
			<input type="hidden" name="<?=$this->getInputName(static::SKIP_NULL_VALUES);?>" value="N" /> 
			<input type="checkbox" name="<?=$this->getInputName(static::SKIP_NULL_VALUES);?>" value="Y" 
				<?if($this->isSkipNullValues()):?> checked="checked"<?endif?> data-role="skip_null_values" />
			<?=$this->fieldName('SKIP_NULL_VALUES', true, false);?>
		</label>
	</div>
</div>

<input type="hidden" data-role="error_no_source_field" value="<?=static::getMessage('ERROR_NO_SOURCE_FIELD');?>" />
<input type="hidden" data-role="error_no_target_field" value="<?=static::getMessage('ERROR_NO_TARGET_FIELD');?>" />
