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
		<div id="<?=$this->getId('action');?>">
			<?=Helper::selectBox($this->getInputName('mode'), $this->getModes(), $this->get('mode'), null, 'data-role="mode"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field" data-role="offer_generator_modes_div">
	<?/* SIMPLE */?>
	<div data-mode="simple">
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('COUNT', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('count');?>">
					<input type="text" name="<?=$this->getInputName('count');?>" value="<?=$this->get('count');?>" size="5"
						data-role="count" />
				</div>
			</div>
		</div>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('ACTIVE', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('active');?>">
					<input type="checkbox" name="<?=$this->getInputName('active');?>" value="Y" data-role="count" 
						<?if($this->get('active') == 'Y'):?>checked<?endif?>/>
				</div>
			</div>
		</div>
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('COPY_PRICES', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div id="<?=$this->getId('copy_prices');?>">
					<input type="checkbox" name="<?=$this->getInputName('copy_prices');?>" value="Y" data-role="copy_prices" 
						<?if($this->get('copy_prices') == 'Y'):?>checked<?endif?>/>
				</div>
			</div>
		</div>
		<input type="hidden" data-role="error_simple_no_count" value="<?=static::getMessage('ERROR_SIMPLE_NO_COUNT');?>" />
	</div>
	<?/* DEFAULT */?>
	<div data-mode="default">
		<div class="plugin-form__field">
			<div class="plugin-form__field-title">
				<?=$this->fieldName('DEFAULT_PROPS', true);?>
			</div>
			<div class="plugin-form__field-value">
				<div>
					<?=IBlock::showAvailableFields($this->getFields($this->arFields_ModeDefault_Offers), $this->strEntityType, 
						$this->getInputName('default_props', true), $this->get('default_props'), 'multiple data-role="default_props"', true);?>
				</div>
			</div>
		</div>
		<?=Helper::showError(static::getMessage('DEFAULT_WARNING'), static::getMessage('DEFAULT_WARNING_DETAILS'));?>
		<input type="hidden" data-role="error_default_no_props" value="<?=static::getMessage('ERROR_DEFAULT_NO_PROPS');?>" />
	</div>
	<?/**/?>
</div>

