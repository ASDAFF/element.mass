<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arCodeConfig = Helper::getIBlockFields($this->intIBlockId, 'CODE', true);

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'data-role="source_field"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SETTINGS_TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('settings_type');?>">
			<?=Helper::selectBox($this->getInputName('settings_type'), [
				static::TYPE_IBLOCK => static::getMessage('SETTINGS_TYPE_IBLOCK'),
				static::TYPE_CUSTOM => static::getMessage('SETTINGS_TYPE_CUSTOM'),
			], $this->get('settings_type'), false, 'data-role="settings_type"');?>
		</div>
	</div>
</div>

<div class="plugin-form__fields-group" data-role="custom_code_settings">

	<div class="plugin-form__field">
		<span>
			<input type="hidden" name="<?=$this->getInputName('unique');?>" value="N" />
			<input type="checkbox" name="<?=$this->getInputName('unique');?>" value="Y" 
				<?if($this->get('unique', null, $arCodeConfig['UNIQUE']) == 'Y'):?>checked="checked"<?endif?> 
				id="<?=$this->getId('unique');?>" data-role="settings_unique"
				data-default="<?=$arCodeConfig['UNIQUE'];?>" />
		</span>
		<label for="<?=$this->getId('unique');?>">
			<?=$this->fieldName('SETTINGS_UNIQUE', true, false);?>
		</label>
	</div>

	<div class="plugin-form__fields-group" data-role="settings_unique_sub">

		<div class="plugin-form__field">
			<?=$this->fieldName('EXISTS_ACTION', true);?>
			<div style="display:inline-block;">
				<?=Helper::selectBox($this->getInputName('exists_action'), [
					'add_index' => static::getMessage('EXISTS_ACTION_ADD_INDEX'),
					'add_id_element' => static::getMessage('EXISTS_ACTION_ADD_ID_ELEMENT'),
				], $this->get('exists_action'), false, 'data-role="exists_action"');?>
			</div>
		</div>
		
	</div>

	<div class="plugin-form__field">
		<span>
			<input type="hidden" name="<?=$this->getInputName('transliteration');?>" value="N" />
			<input type="checkbox" name="<?=$this->getInputName('transliteration');?>" value="Y" 
				<?if($this->get('transliteration', null, $arCodeConfig['TRANSLITERATION']) == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('translit');?>" data-role="settings_translit"
					data-default="<?=$arCodeConfig['TRANSLITERATION'];?>" />
		</span>
		<label for="<?=$this->getId('translit');?>">
			<?=$this->fieldName('SETTINGS_TRANSLIT', true, false);?>
		</label>
		
		<div class="plugin-form__fields-group" data-role="settings_translit_sub">
		
			<div class="plugin-form__field">
				<?=$this->fieldName('SETTINGS_LENGTH', true, true);?>
				<input type="text" name="<?=$this->getInputName('trans_len');?>"
					value="<?=htmlspecialcharsbx($this->get('trans_len', null, $arCodeConfig['TRANS_LEN']));?>" size="3"
					data-default="<?=$arCodeConfig['TRANS_LEN'];?>"/>
			</div>
		
			<div class="plugin-form__field">
				<?=$this->fieldName('SETTINGS_CASE', true, true);?>
				<div style="display:inline-block;">
					<?=Helper::selectBox($this->getInputName('trans_case'), [
						'_' => static::getMessage('SETTINGS_CASE_SAVE'),
						'L' => static::getMessage('SETTINGS_CASE_LOWER'),
						'U' => static::getMessage('SETTINGS_CASE_UPPER'),
					], $this->get('trans_case', false, $arCodeConfig['TRANS_CASE']), null,
					'data-role="settings_case" data-default="'.$arCodeConfig['TRANS_CASE'].'"');?>
				</div>
			</div>
		
			<div class="plugin-form__field">
				<?=$this->fieldName('SETTINGS_REPLACE_SPACE', true, true);?>
				<input type="text" name="<?=$this->getInputName('trans_space');?>"
					value="<?=htmlspecialcharsbx($this->get('trans_space', null, $arCodeConfig['TRANS_SPACE']));?>" size="2"
					data-default="<?=$arCodeConfig['TRANS_SPACE'];?>" />
			</div>
		
			<div class="plugin-form__field">
				<?=$this->fieldName('SETTINGS_REPLACE_OTHER', true, true);?>
				<input type="text" name="<?=$this->getInputName('trans_other');?>"
					value="<?=htmlspecialcharsbx($this->get('trans_other', null, $arCodeConfig['TRANS_OTHER']));?>" size="2"
					data-default="<?=$arCodeConfig['TRANS_OTHER'];?>" />
			</div>
		
			<div class="plugin-form__field">
				<span>
					<input type="hidden" name="<?=$this->getInputName('trans_eat');?>" value="N" />
					<input type="checkbox" name="<?=$this->getInputName('trans_eat');?>" value="Y" 
						<?if($this->get('trans_eat', null, $arCodeConfig['TRANS_EAT']) == 'Y'):?>checked="checked"<?endif?> 
						id="<?=$this->getId('eat');?>" data-role="settings_eat"
						data-default="<?=$arCodeConfig['TRANS_EAT'];?>"/>
				</span>
				<label for="<?=$this->getId('eat');?>">
					<?=$this->fieldName('SETTINGS_EAT', true, false);?>
				</label>
			</div>
		
			<div class="plugin-form__field">
				<span>
					<input type="hidden" name="<?=$this->getInputName('use_google');?>" value="N" />
					<input type="checkbox" name="<?=$this->getInputName('use_google');?>" value="Y" 
						<?if($this->get('use_google', null, $arCodeConfig['USE_GOOGLE']) == 'Y'):?>checked="checked"<?endif?> 
						id="<?=$this->getId('use_google');?>" data-role="settings_google"
						data-default="<?=$arCodeConfig['USE_GOOGLE'];?>"/>
				</span>
				<label for="<?=$this->getId('use_google');?>">
					<?=$this->fieldName('SETTINGS_GOOGLE', true, false);?>
				</label>
			</div>
			
		</div>
		
	</div>
	
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ADDITIONAL_TEXT', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('additional_text');?>">
			<input type="text" name="<?=$this->getInputName('text_begin');?>"
				value="<?=htmlspecialcharsbx($this->get('text_begin'));?>" size="15" />
			<input type="text" name="<?=$this->getInputName('text_end');?>"
				value="<?=htmlspecialcharsbx($this->get('text_end'));?>" size="15" />
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('prevent_cut');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('prevent_cut');?>" value="Y" 
			<?if($this->get('prevent_cut') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('prevent_cut');?>" data-role="settings_prevent_cut"
			data-default="N" />
	</span>
	<label for="<?=$this->getId('prevent_cut');?>">
		<?=$this->fieldName('PREVENT_CUT', true, false);?>
	</label>
</div>

<input type="hidden" data-role="error_no_source_field" value="<?=static::getMessage('ERROR_NO_SOURCE_FIELD');?>" />
