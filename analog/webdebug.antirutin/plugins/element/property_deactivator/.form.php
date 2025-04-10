<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$intSort = intVal($this->get('sort'));
if($intSort <= 0){
	$intSort = 500;
}

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ACTION_TYPE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('action_type');?>">
			<?=Helper::selectBox($this->getInputName('action_type'), [
				'deactivate' => static::getMessage('DEACTIVATE'),
				'activate' => static::getMessage('ACTIVATE'),
				'all' => static::getMessage('ALL'),
			], $this->get('action_type'), null, 'data-role="action_type"');?>
		</div>
	</div>
</div>
