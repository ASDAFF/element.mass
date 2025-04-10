<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('action');?>">
			<?=Helper::selectBox($this->getInputName('type'), [
				'all' => static::getMessage('TYPE_ALL'),
				'set' => static::getMessage('TYPE_SET'),
				'group' => static::getMessage('TYPE_GROUP'),
			], $this->get('type'), null, 'data-role="type"');?>
		</div>
	</div>
</div>
