<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arWdImageProfiles = array_map(function($arProfile){
	return sprintf('[%d] %s', $arProfile['ID'], $arProfile['NAME']);
}, $this->getWebdebugImageProfiles());

?>

<?if(!$this->checkModuleInstalled()):?>

	<?=Helper::showNote(static::getMessage('ERROR_NO_MODULE_FULL'));?>
	<input type="hidden" value="Y" data-role="no_module" />

<?else:?>

	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('SOURCE', true);?>
		</div>
		<div class="plugin-form__field-value">
			<div id="<?=$this->getId('source');?>">
				<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('source'),
					$this->get('source'), 'data-role="source"', true);?>
			</div>
		</div>
	</div>

	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('TARGET', true);?>
		</div>
		<div class="plugin-form__field-value">
			<div id="<?=$this->getId('target');?>">
				<?=IBlock::showAvailableFields($this->getFields(), $this->strEntityType, $this->getInputName('target'),
					$this->get('target'), 'data-role="target"', true);?>
			</div>
		</div>
	</div>

	<div class="plugin-form__field">
		<div class="plugin-form__field-title">
			<?=$this->fieldName('PROFILE', true);?>
		</div>
		<div class="plugin-form__field-value">
			<div id="<?=$this->getId('profile');?>">
				<?=Helper::selectBox($this->getInputName('profile'), $arWdImageProfiles, $this->get('profile'), false, 
					'data-role="profile"');?>
			</div>
		</div>
	</div>

<?endif?>

<input type="hidden" data-role="error_no_module" value="<?=static::getMessage('ERROR_NO_MODULE');?>" />
<input type="hidden" data-role="error_no_source" value="<?=static::getMessage('ERROR_NO_SOURCE');?>" />
<input type="hidden" data-role="error_no_target" value="<?=static::getMessage('ERROR_NO_TARGET');?>" />
<input type="hidden" data-role="error_no_profile" value="<?=static::getMessage('ERROR_NO_PROFILE');?>" />
