<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$strField = $this->get('field');
$bFieldSelected = strlen($strField) && array_key_exists($strField, $this->getFieldsPlain());

$arValue = $this->get('value');
$arValue = is_array($arValue) ? $arValue : (strlen($arValue) ? [$arValue] : []);

if(!$this->isCatalogAvailable()){
	print Helper::showError(static::getMessage('ERROR_NO_CATALOG'));
	return;
}

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

<div class="plugin-form__field"<?if(!$bFieldSelected):?> style="display:none;"<?endif?>>
	<div class="plugin-form__field-title">
		<?=$this->fieldName('VALUE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('value');?>">
			<div data-role="select_value">
				<select name="<?=$this->getInputName('value', true);?>" data-role="value" multiple="multiple" size="10">
					<?if($bFieldSelected):?>
						<?=$this->buildSelectOptions($this->get('field'), $arValue);?>
					<?endif?>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SITE_ID');?>
	</div>
	<div class="plugin-form__field-value">
		<div>
			<?
			$arSitesId = array_map(function($arSite){
				return Helper::formatSite($arSite);
			}, Helper::getSitesList());
			print Helper::selectBox($this->getInputName('site_id'), $arSitesId, $this->get('site_id'), null, 
				'data-role="site_id"');
			?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<span>
		<input type="hidden" name="<?=$this->getInputName('use_offers');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('use_offers');?>" value="Y" 
			<?if($this->get('use_offers') == 'Y'):?>checked="checked"<?endif?> 
			id="<?=$this->getId('use_offers');?>" data-role="use_offers" />
	</span>
	<label for="<?=$this->getId('use_offers');?>">
		<?=$this->fieldName('USE_OFFERS', true, false);?>
	</label>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
<input type="hidden" data-role="error_no_value" value="<?=static::getMessage('ERROR_NO_VALUE');?>" />
