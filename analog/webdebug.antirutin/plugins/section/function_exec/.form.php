<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$arFunctions = $this->getFunctionList(true);
$strFuncName = $this->get('function_name');
$arFuncJson = [];
$strFuncJson = '{}';
if(strlen($strFuncName)){
	$arFuncJson = $this->getFunctionArray($strFuncName);
	if(is_array($arFuncJson) && is_array($arFuncJson['ARGS'])){
		foreach($arFuncJson['ARGS'] as $strArg => $arArg){
			$strArgValue = $this->getFuncArgument($strArg);
			if(!is_null($strArgValue)){
				$arFuncJson['ARGS'][$strArg]['VALUE'] = $strArgValue;
			}
		}
	}
	$strFuncJson = \WD\Antirutin\Json::encode($arFuncJson['ARGS']);
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

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('FUNCTION', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('function');?>">
			<div>
				<select name="<?=$this->getInputName('function_name');?>" data-role="function_name" 
					data-json="<?=htmlspecialcharsbx($strFuncJson);?>">
					<option value=""><?=static::getMessage('FUNCTION_EMPTY');?></option>
					<?foreach($this->getFunctionList() as $strGroup => $arGroup):?>
						<?if(!empty($arGroup['ITEMS'])):?>
							<optgroup label="<?=$arGroup['NAME'];?>">
								<?foreach($arGroup['ITEMS'] as $strFunc => $arFunc):?>
									<option value="<?=$strFunc;?>"<?=$strSelected;?>
										data-arguments="<?=htmlspecialcharsbx(\WD\Antirutin\Json::encode($arFunc['ARGS']));?>"
										<?if($strFunc == $this->get('function_name')):?>selected="selected"<?endif?>
										><?=$strFunc;?> - <?=htmlspecialcharsbx($arFunc['NAME']);?></option>
								<?endforeach?>
							</optgroup>
						<?endif?>
					<?endforeach?>
				</select>
			</div>
			<div data-role="function_arguments"></div>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<label>
		<input type="hidden" name="<?=$this->getInputName('php_debug_mode');?>" value="N" />
		<input type="checkbox" name="<?=$this->getInputName('php_debug_mode');?>" value="Y" data-role="php_debug_mode"
			<?if($this->get('php_debug_mode') != 'N'):?>checked="checked"<?endif?> />
		<?=$this->fieldName('PHP_DEBUG_MODE', true, false);?>
	</label>
</div>

<input type="hidden" data-role="error_no_field" value="<?=static::getMessage('ERROR_NO_FIELD');?>" />
