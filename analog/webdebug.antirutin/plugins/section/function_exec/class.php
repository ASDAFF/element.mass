<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class FunctionExec extends PluginSection {
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:HTML', 'N'], 'IS_WRITEABLE' => 'Y'],
		'SEO' => ['IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		if($this->isEmpty('function_name')){
			$this->setError(static::getMessage('ERROR_NO_FUNCTION'));
			return false;
		}
		return $this->processSectionByFunction($intSectionId);
	}
	
	/**
	 *	Process one section with selected function
	 */
	protected function processSectionByFunction($intSectionId){
		$bDebug = $this->get('php_debug_mode') == 'Y';
		$arValues = $this->getValues($intSectionId);
		$arFunc = $this->getFunctionArray($this->get('function_name'));
		if($bDebug){
			$this->debug('Function: '.var_export($arFunc['FUNC'], true));
		}
		foreach($arValues as $key => $obValue){
			$mValue = $obValue->getValue();
			$bHtmlValue = is_array($mValue) && count($mValue) == 2 && isset($mValue['TEXT']) && isset($mValue['TYPE']);
			$mValueLink = &$mValue;
			if($bHtmlValue) {
				$mValueLink = &$mValue['TEXT'];
			}
			$arArguments = [];
			foreach($arFunc['ARGS'] as $strArg => $arArg){
				if($strArg == 'VALUE'){
					$arArguments[] = $mValueLink;
				}
				else{
					$strArgumentValue = $this->getFuncArgument($strArg);
					if(is_string($strArgumentValue) && !strlen($strArgumentValue)){
						$strArgumentValue = null;
						/*
						if(!$arArg['REQUIRED']){
							break;
						}
						else{
							$strArgumentValue = null;
						}
						*/
					}
					$arArguments[] = $strArgumentValue;
				}
			}
			$this->prepareArguments($arFunc, $arArguments);
			$mValueLink = call_user_func_array($arFunc['NAMESPACE'].$arFunc['FUNC'], $arArguments);
			$obValue->setValue($mValue);
			if($bDebug){
				$this->debug('Arguments: '.var_export($arArguments, true));
				$this->debug('Result: '.htmlspecialcharsbx(print_r($mValue, true)));
			}
		}
		if(!$bDebug){
			$this->saveValues($intSectionId, $arValues);
		}
		else{
			$this->setBreaked(true);
		}
		return true;
	}
	
	/**
	 *	Prepare arguments
	 */
	protected function prepareArguments(&$arFunc, &$arArguments){
		foreach($arArguments as $key => &$value){
			switch($value){
				case '#TRUE#':
					$value = true;
					break;
				case '#FALSE#':
					$value = false;
					break;
				case '#NULL#':
					$value = null;
					break;
			}
		}
		if(isset($arFunc['CALLBACK'])){
			call_user_func_array($arFunc['CALLBACK'], [&$arFunc, &$arArguments]);
		}
	}
	
	/**
	 *	Get list (tree) of function
	 */
	protected function getFunctionList($bNoGroups=false){
		return require __DIR__.'/../../../include/shared/'.pathinfo(__DIR__, PATHINFO_BASENAME).'/include.php';
	}
	
	/**
	 *	Get array of selected function
	 */
	protected function getFunctionArray($strFuncName){
		$arFunctions = $this->getFunctionList(true);
		return $arFunctions[$strFuncName];
	}
	
	/**
	 *	Get saved function argument
	 */
	protected function getFuncArgument($strArgument){
		return $this->get('arg_'.$strArgument);
	}
	
	/**
	 *	Get field/property values
	 */
	protected function getValues($intSectionId){
		$arResult = [];
		$strField = $this->get('field');
		if($this->isSectionField($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arSection[$strField],
			];
		}
		elseif($strProperty = $this->isSectionProperty($strField)){
			$arSelect = [$strField];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
			$mValue = $arSection['PROPERTIES'][$strProperty];
			$mValue = is_array($mValue) ? $mValue : (strlen($mValue) ? [$mValue] : []);
			foreach($mValue as $value){
				$arResult[] = [
					'FIELD' => $strField,
					'VALUE' => $value,
				];
			}
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arFeatures = ['SEO' => true];
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arFeatures, true);
			$arResult[] = [
				'FIELD' => $strField,
				'VALUE' => $arSection['SEO'][$strSeoField],
			];
		}
		foreach($arResult as $key => $arValue){
			$arResult[$key] = new ValueItem($arValue);
		}
		return $arResult;
	}
	
	/**
	 *	Save values for element
	 */
	protected function saveValues($intSectionId, $arValues){
		$bResult = false;
		$strField = $this->get('field');
		if(!empty($arValues)){
			if($this->isSectionField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$bResult = $this->update($intSectionId, [$strField => $obFirst->getValue()]);
				}
			}
			elseif($strProperty = $this->isSectionProperty($strField)){
				$bMultiple = $this->isPropertyMultiple($strProperty);
				$bDescription = $arProperty['WITH_DESCRIPTION'] == 'Y';
				$arSaveValues = $this->transformObjectToValue($arValues, $bMultiple, false, $bDescription);
				$bResult = $this->update($intSectionId, [$strProperty => $arSaveValues]);
			}
			elseif($strSeoField = $this->isSeoField($strField)){
				$obFirst = $this->cutMultipleValue($arValues, static::MULTIPLE_MODE_FIRST);
				if(is_object($obFirst)){
					$strSeoFieldFull = IBlock::$arSeoMapSection[$strSeoField];
					$this->setSeoField($intSectionId, $strSeoFieldFull, $obFirst->getValue());
				}
			}
		}
		return $bResult;
	}
	
}

?>