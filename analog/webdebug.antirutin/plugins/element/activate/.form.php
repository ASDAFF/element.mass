<?
use
	WD\Antirutin\Helper;

?>

<div class="wda-radio-selector" style="display:inline-block;">
	<label>
		<input type="radio" name="<?=$this->getInputName('mode', $bMultiple);?>" value="activate"
			<?if($this->get('mode') != 'deactivate'):?> checked="checked"<?endif?> />
		<span><?=static::getMessage('MODE_ACTIVATE');?></span>
	</label>
	<label>
		<input type="radio" name="<?=$this->getInputName('mode', $bMultiple);?>" value="deactivate"
			<?if($this->get('mode') == 'deactivate'):?> checked="checked"<?endif?> />
		<span><?=static::getMessage('MODE_DEACTIVATE');?></span>
	</label>
</div>
