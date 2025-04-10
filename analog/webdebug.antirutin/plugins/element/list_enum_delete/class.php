<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ListEnumDelete extends PluginElement {
	
	const DUPLICATE_MODE_VALUE = 1;
	const DUPLICATE_MODE_XML_ID = 2;
	
	protected $bSingleTime = true;
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE_FULL' => ['L']],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$intPropertyId = $this->isProperty($this->get('field'));
		if(!$intPropertyId){
			$this->setError(static::getMessage('ERROR_NO_PROPERTY'));
			return false;
		}
		$bClarify = $this->get('select_delete_enums') == 'Y';
		if($bClarify){
			$arUnusedEnums = $this->get('value');
		}
		else{
			$arUnusedEnums = $this->getUnusedEnums($intPropertyId, true);
		}
		foreach($arUnusedEnums as $intEnumId){
			if($arEnum = \CIBlockPropertyEnum::getList([], ['PROPERTY_ID' => $intPropertyId, 'ID' => $intEnumId])->fetch()){
				\CIBlockPropertyEnum::delete($intEnumId);
			}
		}
		return true;
	}
	
	/**
	 *	AJAX: Load field input
	 */
	protected function loadSelectValue(&$arJson){
		return $this->buildSelectOptions($this->arPost['field']);
	}
	
	/**
	 *	Get enums (for properties of types L, E)
	 */
	protected function buildSelectOptions($strField, $arSelectedValues=null){
		$strResult = '';
		$arSelectedValues = is_array($arSelectedValues) ? $arSelectedValues : [];
		if($intPropertyId = $this->isProperty($strField)){
			$strResult .= sprintf('<option value="" data-default="Y">%s</option>', static::getMessage('VALUE_DEFAULT'));
			foreach($this->getUnusedEnums($intPropertyId) as $arEnum){
				$strResult .= sprintf('<option value="%s"%s>%s [%d]</option>', $arEnum['ID'], 
					(in_array($arEnum['ID'], $arSelectedValues) ? ' selected="selected"' : ''), $arEnum['VALUE'],
					$arEnum['ID']);
			}
		}
		return $strResult;
	}
	
	/**
	 * 	Get enums that are not using
	 *	@return array of ['ID', 'VALUE']
	 *	@return array [1, 2, 3] if $bJustId = true
	 */
	protected function getUnusedEnums($intPropertyId, $bJustId=false){
		$arResult = [];
		if(is_array($arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId))){
			switch($arProperty['PROPERTY_TYPE']){
				case 'L':
					// Get all enums in property
					$arResult = array_map(function($arItem){
						return array_intersect_key($arItem, array_flip(['ID', 'VALUE', 'XML_ID']));
					}, IBlock::getEnumsL($intPropertyId, $this->intIBlockId));
					// Get just used enums (using grouping in CIBlockElement::getList)
					$arUsedEnums = $this->getUsedEnums($intPropertyId);
					// Get difference: this is unused enums
					$arResult = array_diff_key($arResult, $arUsedEnums);
					if($bJustId){
						$arResult = array_keys($arResult);
					}
					break;
			}
		}
		return $arResult;
	}

	/**
	 * Get array of used enums
	 * 
	 * @return [type]
	 */
	protected function getUsedEnums($intPropertyId){
		$arResult = [];
		if(is_array($arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId))){
			$arFilter = [
				'IBLOCK_ID' => $this->intIBlockId,
			];
			switch($arProperty['PROPERTY_TYPE']){
				case 'L':
					$strKeyValue = sprintf('PROPERTY_%d_VALUE', $intPropertyId);
					$strKeyEnumId = sprintf('PROPERTY_%d_ENUM_ID', $intPropertyId);
					$resEnums = \CIBLockElement::getList(['CNT' => 'DESC'], $arFilter, ['PROPERTY_'.$intPropertyId]);
					while($arEnum = $resEnums->fetch()){
						if($arEnum[$strKeyEnumId] && $arEnum['CNT']){
							$arResult[$arEnum[$strKeyEnumId]] = [
								'VALUE' => $arEnum[$strKeyValue],
								'COUNT' => $arEnum['CNT'],
							];
						}
					}
					break;
			}
		}
		return $arResult;
	}
	
}

?>