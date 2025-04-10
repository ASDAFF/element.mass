<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class Update extends PluginElement {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->get('catalog_product_update') != 'Y' && $this->get('iblock_element_update') != 'Y'){
			$this->setError(static::getMessage('ERROR_NOTHING_SELECTED'));
			return false;
		}
		$bResult = false;
		if($this->get('catalog_product_update') == 'Y' && \Bitrix\Main\Loader::includeModule('catalog')) {
			if($this->updateProduct($intElementId)){
				$bResult = true;
			}
		}
		if($this->get('iblock_element_update') == 'Y') {
			if($this->updateElement($intElementId)){
				$bResult = true;
			}
		}
		return $bResult;
	}
	
	/**
	 *	
	 */
	protected function updateProduct($intElementId){
		if(\CCatalogProduct::update($intElementId, ['ID' => $intElementId])){
			return true;
		}
		else{
			if(is_object($GLOBALS['APPLICATION']->LAST_ERROR)){
				$strError = implode(', ', $GLOBALS['APPLICATION']->LAST_ERROR->getMessages());
			}
			else {
				$strError = static::getMessage('UNKNOWN_ERROR');
			}
			$this->setError($strError);
			$this->log(static::getMessage('ERROR_UPDATE_PRODUCT', ['#ELEMENT_ID#' => $intElementId, '#ERROR#' => $strError]));
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function updateElement($intElementId){
		$obElement = new \CIBlockElement;
		if($this->get('iblock_element_update_with_fields') == 'Y'){
			$arFields = $this->getUpdateFields($intElementId);
		}
		else{
			$arFields = [
				'ID' => $intElementId,
				'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
			];
		}
		$bWorkflow = $this->get('iblock_element_update_workflow') == 'Y';
		$bUpdateSearch = $this->get('iblock_element_update_updatesearch') == 'Y';
		$bResizePictures = $this->get('iblock_element_update_resizepictures') == 'Y';
		if($obElement->update($intElementId, $arFields, $bWorkflow, $bUpdateSearch, $bResizePictures, false)){
			return true;
		}
		else{
			if($obElement->LAST_ERROR){
				$strError = $obElement->LAST_ERROR;
			}
			else {
				$strError = static::getMessage('UNKNOWN_ERROR');
			}
			$this->setError($strError);
			$this->log(static::getMessage('ERROR_UPDATE_ELEMENT', ['#ELEMENT_ID#' => $intElementId, '#ERROR#' => $strError]));
		}
		return false;
	}
	
	/**
	 *	Get element fields for update
	 *	Expiremantally!
	 */
	protected function getUpdateFields($intElementId){
		$arResult = [];
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['FIELDS' => ['*']]);
		foreach($arElement as $strKey => $mValue){
			if(in_array($strKey, ['SHOW_COUNTER', 'SHOW_COUNTER_START', 'SHOW_COUNTER_START_X'])){
				continue;
			}
			elseif(in_array($strKey, ['USER_NAME', 'LOCKED_USER_NAME', 'CREATED_USER_NAME'])){
				continue;
			}
			elseif(in_array($strKey, ['LANG_DIR', 'LID'])){
				continue;
			}
			elseif(in_array($strKey, ['IBLOCK_TYPE_ID', 'IBLOCK_CODE', 'IBLOCK_NAME', 'IBLOCK_EXTERNAL_ID'])){
				continue;
			}
			elseif(in_array($strKey, ['DETAIL_PAGE_URL', 'LIST_PAGE_URL', 'CANONICAL_PAGE_URL'])){
				continue;
			}
			elseif(in_array($strKey, ['CREATED_DATE', 'TIMESTAMP_X', 'TIMESTAMP_X_UNIX', 'DATE_CREATE', 'DATE_CREATE_UNIX'])){
				continue;
			}
			elseif(in_array($strKey, ['CREATED_BY'])){
				continue;
			}
			elseif(in_array($strKey, ['BP_PUBLISHED'])){
				continue;
			}
			elseif(in_array($strKey, ['WF_STATUS_ID', 'WF_STATUS_ID', 'WF_PARENT_ELEMENT_ID', 'WF_LAST_HISTORY_ID', 
				'WF_NEW', 'LOCK_STATUS', 'WF_LOCKED_BY', 'WF_DATE_LOCK', 'WF_COMMENTS'])){
				continue;
			}
			elseif(in_array($strKey, ['IN_SECTIONS'])){
				continue;
			}
			elseif(in_array($strKey, ['DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO'])){
				continue;
			}
			elseif(substr($strKey, 0, 1) != '~'){
				$arResult[$strKey] = isset($arElement['~'.$strKey]) ? $arElement['~'.$strKey] : $mValue;
			}
		}
		foreach(['PREVIEW_PICTURE', 'DETAIL_PICTURE'] as $strKey){
			if(is_numeric($arResult[$strKey]) && $arResult[$strKey] > 0){
				$arResult[$strKey] = [
					'name' => '',
					'type' => '',
					'tmp_name' => '',
					'error' => 4,
					'size' => 0,
					'description' => '',
					'MODULE_ID' => 'iblock',
					'old_file' => $arResult[$strKey],
				];
			}
		}
		$arResult['IBLOCK_SECTION'] = [];
		$resSections = \CIBlockElement::GetElementGroups($intElementId, true, ['ID']);
		while($arSection = $resSections->getNext()){
			$arResult['IBLOCK_SECTION'][] = $arSection['ID'];
		}
		$arResult['PROPERTY_VALUES'] = [];
		$resProps = \CIBlockElement::getProperty($this->intIBlockId, $intElementId, 'SORT', 'ASC');
		while($arProp = $resProps->Fetch()) {
			$strKey = $arProp['PROPERTY_VALUE_ID'] ? $arProp['PROPERTY_VALUE_ID'] : 'n0';
			if($arProp['PROPERTY_TYPE'] == 'F'){
				$arProp['VALUE'] = [
					'name' => '',
					'type' => '',
					'tmp_name' => '',
					'error' => 4,
					'size' => 0,
					'del' => '',
					'old_id' => $arProp['VALUE'],
				];
			}
			$arResult['PROPERTY_VALUES'][$arProp['ID']][$strKey] = [
				'VALUE' => $arProp['VALUE'],
				'DESCRIPTION' => $arProp['DESCRIPTION'],
			];
		}
		if(class_exists('\Bitrix\IBlock\InheritedProperty\SectionValues')){
			$obIPropValues = new \Bitrix\IBlock\InheritedProperty\ElementValues($this->intIBlockId, $intElementId);
			$arResult['IPROPERTY_TEMPLATES'] = $obIPropValues->getValues();
		}
		return $arResult;
	}
	
}

?>