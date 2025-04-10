<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('iblock_section_update');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('iblock_section_update');?>" value="Y" 
			<?if($this->get('iblock_section_update') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('iblock_section_update');?>" data-role="iblock_section_update" />
	</span>
	<label for="<?=$this->getId('iblock_section_update');?>">
		<?=$this->fieldName('IBLOCK_SECTION_UPDATE', true, false);?>
	</label>
	<div class="plugin-form__group">
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_section_update_resort');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_section_update_resort');?>" value="Y" 
					<?if($this->get('iblock_section_update_resort') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_section_update_resort');?>" data-role="iblock_section_update_resort" />
			</span>
			<label for="<?=$this->getId('iblock_section_update_resort');?>">
				<?=$this->fieldName('IBLOCK_SECTION_UPDATE_RESORT', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_section_update_updatesearch');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_section_update_updatesearch');?>" value="Y" 
					<?if($this->get('iblock_section_update_updatesearch') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_section_update_updatesearch');?>" data-role="iblock_section_update_updatesearch" />
			</span>
			<label for="<?=$this->getId('iblock_section_update_updatesearch');?>">
				<?=$this->fieldName('IBLOCK_SECTION_UPDATE_UPDATESEARCH', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_section_update_resizepictures');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_section_update_resizepictures');?>" value="Y" 
					<?if($this->get('iblock_section_update_resizepictures') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_section_update_resizepictures');?>" data-role="iblock_section_update_resizepictures" />
			</span>
			<label for="<?=$this->getId('iblock_section_update_resizepictures');?>">
				<?=$this->fieldName('IBLOCK_SECTION_UPDATE_RESIZEPICTURES', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_section_update_with_fields');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_section_update_with_fields');?>" value="Y" 
					<?if($this->get('iblock_section_update_with_fields') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_section_update_with_fields');?>" data-role="iblock_section_update_with_fields" />
			</span>
			<label for="<?=$this->getId('iblock_section_update_with_fields');?>">
				<?=$this->fieldName('IBLOCK_SECTION_UPDATE_WITH_FIELDS', true, false);?>
			</label>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_nothing_selected" value="<?=static::getMessage('ERROR_NOTHING_SELECTED');?>" />
