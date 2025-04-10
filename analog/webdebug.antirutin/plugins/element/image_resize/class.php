<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ImageResize extends PluginElement {
	
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
	public function processElement($intElementId){
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
		$arElement = $this->getElementArray($intElementId);
		#
		$arUpdateFields = [];
		$arUpdateProperties = [];
		foreach($arField as $strField) {
			if($this->isField($strField)){
				if(is_array($arElement[$strField])){
					$arNewImages = $this->resizeAll($arElement[$strField], false, $arElement[$strField]['DESCRIPTION']);
					if($arNewImages) {
						if(!$this->update($intElementId, [$strField => $arNewImages], false, false, false, true)){
							$bResult = false;
						}
					}
				}
			}
			elseif($intPropertyId = $this->isProperty($strField)) {
				$arProp = $arElement['PROPERTIES'][$intPropertyId];
				$arNewImages = $this->resizeAll($arProp['VALUE'], $arProp['PROPERTY_VALUE_ID'], $arProp['DESCRIPTION']);
				if($arNewImages) {
					if(!$this->setPropertyValue($intElementId, $intPropertyId, $arNewImages)){
						$bResult = false;
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
	protected function getElementArray($intElementId){
		$arField = $this->get('field');
		if(is_array($arField)){
			$arFeatures = [
				'EMPTY_PROPERTIES' => true,
			];
			foreach($arField as $strField){
				if($this->isField($strField)){
					if(!is_array($arFeatures['FIELDS'])){
						$arFeatures['FIELDS'] = [];
					}
					$arFeatures['FIELDS'][] = $strField;
				}
				elseif($intPropertyId = $this->isProperty($strField)){
					if(!is_array($arFeatures['PROPERTY_ID'])){
						$arFeatures['PROPERTY_ID'] = [];
					}
					$arFeatures['PROPERTY_ID'][] = $intPropertyId;
				}
			}
			return IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function resizeAll($mValue, $mValueId=null, $mDescription=null) {
		if($mValue === false || is_array($mValue) && empty($mValue)){
			return true;
		}
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
							$arValueNew[$mValueId[$key]] = [
								'VALUE' => $arResizedImg,
								'DESCRIPTION' => $mDescription[$key],
							];
						}
						else{
							# Save exists value
							$arValueNew[$mValueId[$key]] = [
								'VALUE' => [
									'name' => '',
									'type' => '',
									'tmp_name' => '',
									'error' => 4,
									'size' => 0,
									'description' => $mDescription[$key],
								],
								'DESCRIPTION' => $mDescription[$key],
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