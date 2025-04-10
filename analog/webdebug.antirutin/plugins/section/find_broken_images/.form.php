<?
namespace WD\Antirutin\Plugins\Section;

use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$this->showParentSettings();
?>

<br/>

<div class="plugin-form__field">
	<div class="plugin-form__field-value">
		<label>
			<input type="hidden" name="<?=$this->getInputName('check_content');?>" value="N" /> 
			<input type="checkbox" name="<?=$this->getInputName('check_content');?>" value="Y" 
				<?if($this->get('check_content') == 'Y'):?> checked="checked"<?endif?> data-role="check_content" />
			<?=$this->fieldName('CHECK_CONTENT', true, false);?>
		</label>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-value">
		<label>
			<input type="hidden" name="<?=$this->getInputName('delete_broken_images');?>" value="N" /> 
			<input type="checkbox" name="<?=$this->getInputName('delete_broken_images');?>" value="Y" 
				<?if($this->get('delete_broken_images') == 'Y'):?> checked="checked"<?endif?> data-role="delete_broken_images" />
			<?=$this->fieldName('DELETE_BROKEN_IMAGES', true, false);?>
		</label>
	</div>
</div>
