<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class CalculateCount extends PluginSection {
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE' => ['S', 'N']],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if(!strlen($this->get('field'))){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		$bResult = false;
		$strField = $this->get('field');
		if(strlen($strField)){
			$arFields = [
				$strField => $this->getSectionElementsCount($intSectionId),
			];
			$bResult = $this->update($intSectionId, $arFields);
		}
		return $bResult;
	}
	
	/**
	 *	Get count for section
	 */
	protected function getSectionElementsCount($intSectionId){
		$arFilter = $this->buildFilterByParams($intSectionId);
		$intCount = \CIBlockElement::getList([], $arFilter, []);
		return intVal($intCount);
	}
	
	/**
	 *	Build filter for elements
	 */
	protected function buildFilterByParams($intSectionId){
		$arResult = [];
		if($this->get('filter_active') == 'Y'){
			$arResult['ACTIVE'] = 'Y';
		}
		if($this->get('filter_active_date') == 'Y'){
			$arResult['ACTIVE_DATE'] = 'Y';
		}
		if($this->get('include_subsections') == 'Y'){
			$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
		}
		$arResult['SECTION_ID'] = $intSectionId;
		if($this->get('additional_filter') == 'Y'){
			$arCustomFilter = eval($this->get('additional_filter_php').';');
			if(is_array($arCustomFilter)){
				$arResult = array_merge($arResult, $arCustomFilter);
			}
			elseif(is_array($arrFilter)){
				$arResult = array_merge($arResult, $arrFilter);
			}
			elseif(is_array($arFilter)){
				$arResult = array_merge($arResult, $arFilter);
			}
		}
		return $arResult;
	}
	
}

?>