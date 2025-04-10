<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class PropertyDelete extends PluginElement {
	
	const DUPLICATE_MODE_VALUE = 1;
	const DUPLICATE_MODE_XML_ID = 2;
	
	protected $bSingleTime = true;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$arSelectedUnusedProps = $this->getSelectedUnusedProperties();
		foreach($arSelectedUnusedProps as $intPropId){
			$this->deleteProperty($intPropId);
		}
		return true;
	}

	/**
	 * Get active properties for current iblock
	 */
	protected function getProperties(){
		$arResult = [];
		$arSort = ['SORT' => 'ASC', 'ID' => 'ASC'];
		$arFilter = ['IBLOCK_ID' => $this->intIBlockId];
		$resProps = \CIBlockProperty::getList($arSort, $arFilter);
		while($arProp = $resProps->fetch()){
			$arResult[$arProp['ID']] = $arProp;
		}
		return $arResult;
	}

	/**
	 * Get unused properties for current iblock
	 */
	protected function getUnusedProperties(){
		$arResult = [];
		$arPropsAll = $this->getProperties();
		foreach($arPropsAll as $intPropId => $arProp){
			if(!$this->isPropertyUsed($intPropId)){
				$arResult[$intPropId] = $arProp;
			}
		}
		return $arResult;
	}

	/**
	 * Get unused properties for current iblock
	 */
	protected function getSelectedUnusedProperties(){
		$arResult = [];
		$bManualSelect = $this->get('select_delete_props') == 'Y';
		if($bManualSelect){
			$arResult = $this->get('delete_props');
			if(!is_array($arResult)){
				$arResult = [];
			}
		}
		else{
			$arResult = array_keys($this->getUnusedProperties());
		}
		return $arResult;
	}

	/**
	 * Check property used
	 */
	protected function isPropertyUsed($intPropertyId){
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'!PROPERTY_'.$intPropertyId => false,
		];
		return !!\CIBlockElement::getList([], $arFilter, false, ['nTopCount' => 1], ['ID'])->fetch();
	}

	/**
	 * Deactivate single property
	 */
	protected function deleteProperty($intPropId){
		$bResult = true;
		if($arProp = \CIBlockProperty::getList([], ['ID' => $intPropId])->fetch()){
			if($arProp['IBLOCK_ID'] == $this->intIBlockId){
				$bResult = \CIBlockProperty::delete($intPropId);
			}
		}
		return $bResult;
	}
	
}

?>