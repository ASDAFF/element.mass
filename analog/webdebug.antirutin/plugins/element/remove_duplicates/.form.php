<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FIELD', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('field');?>">
			<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('field'),
				$this->get('field'), 'data-role="field"', true);?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('WHICH', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('which');?>">
			<?=Helper::selectBox($this->getInputName('which'), array_map(function($strItem){
				return static::getMessage('WHICH_'.$strItem);
			}, $this->arWhich), $this->get('which'), false, 'data-role="which"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ACTION', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('action');?>">
			<?=Helper::selectBox($this->getInputName('action'), [
				'deactivate' => static::getMessage('ACTION_DEACTIVATE'),
				'delete' => static::getMessage('ACTION_DELETE'),
			], $this->get('action'), false, 'data-role="action"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('EXCLUDE_PROPERTIES', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('exclude_properties');?>">
			<?=IBlock::showAvailableFields($this->getFields($this->arFieldsFilter2), $this->strEntityType, 
				$this->getInputName('exclude_properties', true),
				$this->get('exclude_properties'), 'multiple="multiple" size="8" data-role="exclude_properties"', 
				true, false, false, false);?>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="prompt_delete" value="<?=static::getMessage('PROMPT_DELETE');?>" />
<input type="hidden" data-role="prompt_cancelled" value="<?=static::getMessage('PROMPT_CANCELLED');?>" />
