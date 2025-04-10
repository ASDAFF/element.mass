<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$strCode = strlen($this->get('code')) ? $this->get('code') : $this->getDemoCode();

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('CODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('code');?>">
			<textarea name="<?=$this->getInputName('code');?>" data-role="code" rows="12" cols="60"
				placeholder="<?=htmlspecialcharsbx($this->getDemoCode());?>" spellcheck="false">
<?=htmlspecialcharsbx($strCode);?></textarea>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_code" value="<?=static::getMessage('ERROR_NO_CODE');?>" />
