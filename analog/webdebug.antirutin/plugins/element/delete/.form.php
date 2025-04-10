<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<?=Helper::showNote(static::getMessage('NO_SETTINGS_REQUIRED'), true);?>
	<?=Helper::showError(static::getMessage('WARNING'));?>
</div>

<input type="hidden" data-role="prompt_delete" value="<?=static::getMessage('PROMPT_DELETE');?>" />
<input type="hidden" data-role="prompt_cancelled" value="<?=static::getMessage('PROMPT_CANCELLED');?>" />
