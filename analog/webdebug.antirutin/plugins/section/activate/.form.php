<?
use WD\Antirutin\Helper;
?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('MODE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('mode');?>">
			<?=Helper::selectBox($this->getInputName('mode'), [
				'activate_absolutely' => static::getMessage('MODE_ABSOLUTELY_ACTIVE'),
				'element_exist' => static::getMessage('MODE_ELEMENT_EXIST'),
				'deactivate_absolutely' => static::getMessage('MODE_ABSOLUTELY_DEACTIVE'),
				'element_absent' => static::getMessage('MODE_ELEMENT_ABSENT'),
			], $this->get('mode'), false, 'data-role="mode"');?>
		</div>
	</div>
</div>
