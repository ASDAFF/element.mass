<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement,
	\WD\Antirutin\Html2Text,
	\WD\Antirutin\Html2Text_cp1251,
	\WD\Antirutin\Html2Text_utf8,
	\Bitrix\Main\Loader;

class ChangeTypeTextHtml extends PluginElement
{
	
	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S:HTML']],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S:HTML']],
	];

	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *    Execute!
	 */
	public function processElement($intElementId) : bool{
		if($this->isEmpty('source_field')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		$bResult = false;
		$arSelect = $this->get('source_field');
		foreach ($arSelect as $strSelect){
			if($intPropertyId = $this->isProperty($strSelect)){
				$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['PROPERTY_ID' => [$intPropertyId]]);
				$arProperty = $arElement['PROPERTIES'][$intPropertyId];
				if(!empty($arProperty)){
					if(is_array($arProperty['~VALUE']) && isset($arProperty['~VALUE']['TYPE']) && strlen($arProperty['~DESCRIPTION'])){
						$arProperty['~VALUE']['DESCRIPTION'] = $arProperty['~DESCRIPTION'];
					}
					if($this->isNeedPropertyUpdate($arProperty, $this->get('type_field'))){
						$bResult = $this->setPropertyType($intElementId, $intPropertyId, $this->get('type_field'), $arProperty['~VALUE'], $arProperty['MULTIPLE']);
					}
					else{
						$bResult = true;
					}
				}
			}
			else {
				$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['FIELDS' => [$strSelect, $strSelect.'_TYPE']]);
				$strText = $arElement['~'.$strSelect];
				$strType = $arElement[$strSelect.'_TYPE'];
				if(toUpper($strType) != $this->get('type_field')){
					$this->convertHtmlToText($strText, $strType);
					$arFields = [
						$strSelect => $strText,
						$strSelect.'_TYPE' => toLower($this->get('type_field')),
					];
					$bResult = $this->update($intElementId, $arFields);
				}
				else {
					$bResult = false;
				}
			}
		}
		return $bResult;
	}

	private function setPropertyType($intElementId, $intPropertyId, $strType, $arValue, $strMultiple): bool {
		$arPropertyValues = [];
		if ($strMultiple == 'Y') {
			foreach ($arValue as $arValueItem) {
				$this->convertHtmlToText($arValueItem['TEXT'], $arValueItem['TYPE']);
				$arPropertyValues[$intPropertyId][] = [
					'VALUE' => [
						'TEXT' => $arValueItem['TEXT'],
						'TYPE' => $strType,
					],
					'DESCRIPTION' => $arValueItem['DESCRIPTION'],
				];
			}
		} else {
			$this->convertHtmlToText($arValue['TEXT'], $arValue['TYPE']);
			$arPropertyValues[$intPropertyId] = [
				'VALUE' => [
					'TEXT' => $arValue['TEXT'],
					'TYPE' => $strType,
				],
				'DESCRIPTION' => $arValue['DESCRIPTION'],
			];
		}
		\CIBlockElement::SetPropertyValuesEx($intElementId, $this->intIBlockId, $arPropertyValues);
		return true;
	}

	private function isNeedPropertyUpdate($arProperty, string $strType): bool{
		$bResult = false;
		if(empty($arProperty)){
			return $bResult;
		}
		if($arProperty['MULTIPLE'] == 'Y'){
			foreach ($arProperty['VALUE'] as $arValue){
				if($arValue['TYPE'] != $strType){
					$bResult = true;
				}
			}
		}
		else{
			if($arProperty['VALUE']['TYPE'] != $strType){
				$bResult = true;
			}
		}
		return $bResult;
	}

	private function convertHtmlToText(&$strText, $strOldType){
		$strOldType = toUpper($strOldType);
		$strNewType = toUpper($this->get('type_field'));
		if($this->get('html_to_text') == 'Y' && $strOldType == 'HTML' && $strNewType == 'TEXT'){
			new Html2Text(); # For autoload
			$arOptions = [
				'do_links' => 'none',
				'width' => '0',
			];
			if(Helper::isUtf()){
				$obHtml = new Html2Text_utf8($strText, $arOptions);
			}
			else{
				$obHtml = new Html2Text_cp1251($strText, $arOptions);
			}
			$strText = $obHtml->getText();
			unset($obHtml);
		}
	}

}