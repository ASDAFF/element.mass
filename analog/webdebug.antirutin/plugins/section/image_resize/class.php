<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class ImageResize extends PluginSection {
	
	const GROUP = 'IMAGES';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE' => 'F'],
		'PROPERTIES' => ['TYPE' => 'F'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intElementId){
		$bResult = true;
		$arField = $this->get('field');
		$intWidth = $this->get('width');
		$intHeight = $this->get('height');
		#
		$arField = is_array($arField) ? array_filter($arField) : $arField;
		if(empty($arField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		elseif(!is_numeric($intWidth) || $intWidth <= 0){
			$this->setError(static::getMessage('ERROR_NO_WIDTH'));
			return false;
		}
		elseif(!is_numeric($intHeight) || $intHeight <= 0){
			$this->setError(static::getMessage('ERROR_NO_HEIGHT'));
			return false;
		}
		#
		$arSection = $this->getSectionArray($intElementId);
		#
		$arDeleteFiles = [];
		#
		$arUpdateFields = [];
		$arUpdateProperties = [];
		foreach($arField as $strField) {
			if($this->isSectionField($strField)){
				if(is_array($arSection[$strField])){
					$arNewImages = $this->resizeAll($arSection[$strField], $arSection[$strField]['DESCRIPTION'], $arDeleteFiles);
					if($arNewImages) {
						if(!$this->update($intElementId, [$strField => $arNewImages])){
							$bResult = false;
						}
					}
				}
			}
			elseif($strPropertyCode = $this->isSectionProperty($strField)) {
				$arNewImages = $this->resizeAll($arSection['PROPERTIES'][$strPropertyCode], null, $arDeleteFiles);
				if($arNewImages) {
					if(!$this->update($intElementId, [$strField => $arNewImages])){
						$bResult = false;
					}
					elseif(!empty($arDeleteFiles)){
						foreach($arDeleteFiles as $intImageId){
							\CFile::delete($intImageId);
						}
					}
				}
			}
		}
		return $bResult;
	}
	
	// *******************************************************************************************************************
	// Other methods
	// *******************************************************************************************************************
	
	/**
	 *	
	 */
	protected function getSectionArray($intElementId){
		$arField = $this->get('field');
		if(is_array($arField)){
			$arSelect = [];
			foreach($arField as $strField){
				if($this->isSectionField($strField)){
					$arSelect[] = $strField;
				}
				elseif($this->isSectionProperty($strField)){
					$arSelect[] = $strField;
				}
			}
			return IBlock::getSectionArray($intElementId, $this->intIBlockId, $arSelect);
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function resizeAll($mValue, $mDescription=null, &$arDeleteFiles=null) {
		if($mValue === false || is_array($mValue) && empty($mValue)){
			return true;
		}
		$arDeleteFiles = is_array($arDeleteFiles) ? $arDeleteFiles : [];
		$bMultiple = is_array($mValue) && !array_key_exists('ID', $mValue);
		# Multiple properties
		if($bMultiple) {
			$bHaveChanges = false;
			$arValueNew = [];
			foreach($mValue as $key => $intImageId) {
				if($intImageId) {
					$arImage = \CFile::getFileArray($intImageId);
					if($arImage){
						$arResizedImg = static::resizeSingleImage($arImage);
						if($arResizedImg){
							# Save new value
							$bHaveChanges = true;
							$arValueNew[] = $arResizedImg;
							$arDeleteFiles[] = $intImageId;
						}
						else{
							# Save exists value
							$arValueNew[] = [
								'name' => '',
								'type' => '',
								'tmp_name' => '',
								'error' => 4,
								'size' => 0,
								'del' => '',
								'description' => $mDescription[$key],
								'old_id' => $intImageId,
							];
						}
					}
				}
			}
			if($bHaveChanges){
				return $arValueNew;
			}
		}
		# Single properties, fields
		else {
			$arImage = is_array($mValue) ? $mValue : \CFile::getFileArray($mValue);
			$arResizedImg = static::resizeSingleImage($arImage);
			if($arResizedImg) {
				$arResizedImg['description'] = $mDescription;
				return $arResizedImg;
			}
		}
		return false;
	}
	
	/**
	 *	It takes array of file OR id of file
	 *	@return array of file OR false
	 */
	protected function resizeSingleImage(array $arImage) {
		$strMethod = $this->get('method');
		$arAllowedMethods = [
			'BX_RESIZE_IMAGE_PROPORTIONAL',
			'BX_RESIZE_IMAGE_EXACT',
			'BX_RESIZE_IMAGE_PROPORTIONAL_ALT',
		];
		if(!in_array($strMethod, $arAllowedMethods)){
			$strMethod = reset($arAllowedMethods);
		}
		$strMethod = constant($strMethod);
		$arSize = [
			'width' => $this->get('width'),
			'height' => $this->get('height'),
		];
		if($arImage['WIDTH'] <= $arSize['width'] && $arImage['HEIGHT'] <= $arSize['height']){
			return false;
		}
		$arFilters = [];
		if($this->get('sharpen') == 'Y'){
			$arFilters[] = ['name' => 'sharpen', 'precision' => 15];
		}
		$arResizedImage = \CFile::resizeImageGet($arImage, $arSize, $strMethod, true, $arFilters);
		$strRoot = \Bitrix\Main\Context::getCurrent()->getServer()->getDocumentRoot();
		if(is_array($arResizedImage) && is_file($strRoot.$arResizedImage['src'])) {
			return \CFile::makeFileArray($strRoot.$arResizedImage['src']);
		}
		return false;
	}
	
}

?>