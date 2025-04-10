<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$bCatalog = \Bitrix\Main\Loader::includeModule('catalog');

?>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('catalog_product_update');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('catalog_product_update');?>" value="Y" 
			<?if($bCatalog && $this->get('catalog_product_update') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('catalog_product_update');?>" data-role="catalog_product_update"
			<?if(!$bCatalog):?>disabled<?endif?> />
	</span>
	<label for="<?=$this->getId('catalog_product_update');?>" <?if(!$bCatalog):?>style="color:gray;"<?endif?>>
		<?=$this->fieldName('CATALOG_PRODUCT_UPDATE', true, false);?>
		<?if(!$bCatalog):?>
			<span style="color:red;font-style:italic;"><?=static::getMessage('ERROR_NO_CATALOG_MODULE');?></span>
		<?endif?>
	</label>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('iblock_element_update');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('iblock_element_update');?>" value="Y" 
			<?if($this->get('iblock_element_update') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('iblock_element_update');?>" data-role="iblock_element_update" />
	</span>
	<label for="<?=$this->getId('iblock_element_update');?>">
		<?=$this->fieldName('IBLOCK_ELEMENT_UPDATE', true, false);?>
	</label>
	<div class="plugin-form__group">
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_element_update_workflow');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_element_update_workflow');?>" value="Y" 
					<?if($this->get('iblock_element_update_workflow') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_element_update_workflow');?>" data-role="iblock_element_update_workflow" />
			</span>
			<label for="<?=$this->getId('iblock_element_update_workflow');?>">
				<?=$this->fieldName('IBLOCK_ELEMENT_UPDATE_WORKFLOW', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_element_update_updatesearch');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_element_update_updatesearch');?>" value="Y" 
					<?if($this->get('iblock_element_update_updatesearch') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_element_update_updatesearch');?>" data-role="iblock_element_update_updatesearch" />
			</span>
			<label for="<?=$this->getId('iblock_element_update_updatesearch');?>">
				<?=$this->fieldName('IBLOCK_ELEMENT_UPDATE_UPDATESEARCH', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_element_update_resizepictures');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_element_update_resizepictures');?>" value="Y" 
					<?if($this->get('iblock_element_update_resizepictures') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_element_update_resizepictures');?>" data-role="iblock_element_update_resizepictures" />
			</span>
			<label for="<?=$this->getId('iblock_element_update_resizepictures');?>">
				<?=$this->fieldName('IBLOCK_ELEMENT_UPDATE_RESIZEPICTURES', true, false);?>
			</label>
		</div>
		<div class="plugin-form__field">
			<span>
				<input type="hidden" name="<?=$this->getInputName('iblock_element_update_with_fields');?>" value="N" />
				<input type="checkbox" name="<?=$this->getInputName('iblock_element_update_with_fields');?>" value="Y" 
					<?if($this->get('iblock_element_update_with_fields') == 'Y'):?>checked="checked"<?endif?> 
					id="<?=$this->getId('iblock_element_update_with_fields');?>" data-role="iblock_element_update_with_fields" />
			</span>
			<label for="<?=$this->getId('iblock_element_update_with_fields');?>">
				<?=$this->fieldName('IBLOCK_ELEMENT_UPDATE_WITH_FIELDS', true, false);?>
			</label>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_nothing_selected" value="<?=static::getMessage('ERROR_NOTHING_SELECTED');?>" />
