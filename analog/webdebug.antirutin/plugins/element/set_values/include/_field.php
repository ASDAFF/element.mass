<?
namespace WD\Antirutin;
?>
<tr data-role="field_item">
	<td data-role="field_input">
		<div style="position:relative;" data-role="field_input_inner">
			<?=$strFieldHtml;?>
			<?if($arField['DATA']['PROPERTY_TYPE'] != 'F' && $arField['DATA']['WITH_DESCRIPTION'] == 'Y'):?>
				<input type="text" name="<?=$this->getInputName('value_description', $bMultiple);?>" size="30"
					value="<?=$mDescription;?>" placeholder="<?=static::getMessage('FIELD_DESCRIPTION_PLACEHOLDER');?>"
					 data-role="field_input_description" />
			<?endif?>
		</div>
	</td>
	<td data-role="field_delete">
		<input type="button" value="&times;" title="<?=static::getMessage('FIELD_DELETE');?>" class="wda-icon-delete" />
	</td>
</tr>
