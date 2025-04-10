<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('mode');?>">
			<?=Helper::selectBox($this->getInputName('mode'), [
				'all' => static::getMessage('MODE_ALL'),
				'inactive' => static::getMessage('MODE_INACTIVE'),
			], $this->get('mode'), false, 'data-role="mode"');?>
		</div>
	</div>
</div>

<input type="hidden" data-role="prompt_delete" value="<?=static::getMessage('PROMPT_DELETE');?>" />
<input type="hidden" data-role="prompt_cancelled" value="<?=static::getMessage('PROMPT_CANCELLED');?>" />
