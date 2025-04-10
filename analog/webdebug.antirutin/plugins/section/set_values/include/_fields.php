<?
namespace WD\Antirutin;

$strId = $this->getId();
$strJsId = $this->getCode().'_initialized_'.$strId;

?>

<div data-role="field_list" id="<?=$strId;?>_field_list">
	<table data-role="field_items" style="min-width:200px;">
		<tbody>
			<?=$strFieldsHtml;?>
		</tbody>
	</table>
	<?if($bAllowMultiply):?>
		<div data-role="field_add">
			<input type="button" value="<?=static::getMessage('FIELD_ADD');?>" class="adm-btn-green" />
		</div>
		<br/>
	<?endif?>
	<?if($bCanSaveCurrentValues):?>
		<span class="plugin-form__field-item">
			<input type="hidden" name="<?=$this->getInputName(static::SAVE_CURRENT_VALUES);?>" value="N" />
			<label>
				<input type="checkbox" name="<?=$this->getInputName(static::SAVE_CURRENT_VALUES);?>" value="Y" 
					<?if($this->isSaveValues()):?> checked="checked"<?endif?> data-role="save_current_values" />
				<span><?=static::getMessage('SAVE_CURRENT_VALUES');?></span>
			</label>
			<?=$this->hint('SAVE_CURRENT_VALUES');?>
		</span>
	<?endif?>
	<?if($bCanRemoveDuplicates):?>
		<span class="plugin-form__field-item">
			<input type="hidden" name="<?=$this->getInputName(static::REMOVE_DUPLICATES);?>" value="N" />
			<label>
				<input type="checkbox" name="<?=$this->getInputName(static::REMOVE_DUPLICATES);?>" value="Y" 
					<?if($this->isRemoveDuplicates()):?> checked="checked"<?endif?> data-role="remove_duplicates" />
				<span><?=static::getMessage('REMOVE_DUPLICATES');?></span>
			</label>
			<?=$this->hint('REMOVE_DUPLICATES');?>
		</span>
	<?endif?>
	<?if($arField['TYPE'] == 'F'):?>
		<span class="plugin-form__field-item">
			<input type="hidden" name="<?=$this->getInputName(static::REMOVE_FILES_IF_EMPTY_VALUE);?>" value="N" />
			<label>
				<input type="checkbox" name="<?=$this->getInputName(static::REMOVE_FILES_IF_EMPTY_VALUE);?>" value="Y" 
					<?if($this->isRemoveFilesIfEmptyValue()):?> checked="checked"<?endif?> data-role="remove_files_if_empty_value" />
				<span><?=static::getMessage('REMOVE_FILES_IF_EMPTY_VALUE');?></span>
			</label>
			<?=$this->hint('REMOVE_FILES_IF_EMPTY_VALUE');?>
		</span>
	<?endif?>
</div>
