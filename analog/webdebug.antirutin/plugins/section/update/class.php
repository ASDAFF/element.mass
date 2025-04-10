<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class Update extends PluginSection {
	
	private $arCacheSectionFields = [];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if($this->get('iblock_section_update') != 'Y'){
			$this->setError(static::getMessage('ERROR_NOTHING_SELECTED'));
			return false;
		}
		$bResult = false;
		if($this->get('iblock_section_update') == 'Y') {
			if($this->updateSection($intSectionId)){
				$bResult = true;
			}
		}
		return $bResult;
	}
	
	/**
	 *	
	 */
	protected function updateSection($intSectionId){
		$obSection = new \CIBlockSection;
		if($this->get('iblock_section_update_with_fields') == 'Y'){
			$arFields = $this->getUpdateFields($intSectionId);
		}
		else{
			$arFields = [
				'ID' => $intSectionId,
				'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
			];
		}
		$bResort = $this->get('iblock_section_update_resort') == 'Y';
		$bUpdateSearch = $this->get('iblock_section_update_updatesearch') == 'Y';
		$bResizePictures = $this->get('iblock_section_update_resizepictures') == 'Y';
		if($obSection->update($intSectionId, $arFields, $bResort, $bUpdateSearch, $bResizePictures)){
			return true;
		}
		else{
			if($obSection->LAST_ERROR){
				$strError = $obSection->LAST_ERROR;
			}
			else {
				$strError = static::getMessage('UNKNOWN_ERROR');
			}
			$this->setError($strError);
			$this->log(static::getMessage('ERROR_UPDATE_SECTION', ['#SECTION_ID#' => $intSectionId, '#ERROR#' => $strError]));
		}
		return false;
	}
	
	/**
	 *	Get section fields for update
	 *	Expiremantally!
	 */
	protected function getUpdateFields($intSectionId){
		$arResult = [];
		$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId);
		foreach($arSection as $strKey => $mValue){
			if(in_array($strKey, ['PROPERTIES'])){
				continue;
			}
			elseif(in_array($strKey, ['LIST_PAGE_URL', 'SECTION_PAGE_URL'])){
				continue;
			}
			elseif(in_array($strKey, ['SOCNET_GROUP_ID'])){
				continue;
			}
			elseif(in_array($strKey, ['IBLOCK_TYPE_ID', 'IBLOCK_CODE', 'IBLOCK_EXTERNAL_ID'])){
				continue;
			}
			elseif(in_array($strKey, ['EXTERNAL_ID'])){
				continue;
			}
			elseif(in_array($strKey, ['DEPTH_LEVEL', 'LEFT_MARGIN', 'RIGHT_MARGIN'])){
				continue;
			}
			elseif(in_array($strKey, ['GLOBAL_ACTIVE', 'LEFT_MARGIN', 'RIGHT_MARGIN'])){
				continue;
			}
			elseif(in_array($strKey, ['CREATED_BY'])){
				continue;
			}
			elseif(in_array($strKey, ['DATE_CREATE', 'TIMESTAMP_X'])){
				continue;
			}
			elseif(substr($strKey, 0, 1) != '~'){
				$arResult[$strKey] = isset($arSection['~'.$strKey]) ? $arSection['~'.$strKey] : $mValue;
			}
		}
		if(is_array($arSection['PROPERTIES'])){
			$arAllFields = $this->getAllSectionProps();
			foreach($arSection['PROPERTIES'] as $strKey => $mValue){
				if(substr($strKey, 0, 1) != '~'){
					if($arAllFields[$strKey]['USER_TYPE_ID'] == 'file'){
						if(is_array($mValue)){
							foreach($mValue as $key => $mValueItem){
								$mValue[$key] = [
									'name' => '',
									'type' => '',
									'tmp_name' => '',
									'error' => 4,
									'size' => 0,
									'del' => null,
									'old_id' => $mValueItem,
								];
							}
						}
						elseif(is_numeric($mValue)){
							$mValue = [
								'name' => '',
								'type' => '',
								'tmp_name' => '',
								'error' => 4,
								'size' => 0,
								'del' => '',
								'old_id' => $mValue,
							];
						}
						$arResult[$strKey] = $mValue;
					}
					else{
						$arResult[$strKey] = isset($arSection['PROPERTIES']['~'.$strKey]) ? $arSection['PROPERTIES']['~'.$strKey] 
							: $mValue;
					}
				}
			}
		}
		foreach(['PICTURE', 'DETAIL_PICTURE'] as $strKey){
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
		if(class_exists('\Bitrix\IBlock\InheritedProperty\SectionValues')){
			$obIPropValues = new \Bitrix\IBlock\InheritedProperty\SectionValues($this->intIBlockId, $intSectionId);
			$arResult['IPROPERTY_TEMPLATES'] = $obIPropValues->getValues();
		}
		return $arResult;
	}
	
	/**
	 *	Get sections props with caching
	 */
	protected function getAllSectionProps(){
		if(!empty($this->arCacheSectionFields)){
			return $this->arCacheSectionFields;
		}
		$this->arCacheSectionFields = IBlock::getSectionUserFields($this->intIBlockId);
		return $this->arCacheSectionFields;
	}
	
}

?>