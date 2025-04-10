<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ImageDuplicatesRemove extends PluginElement {
	
	const GROUP = 'IMAGES';
	
	protected $arFieldsFilter1 = [
		'FIELDS' => ['TYPE' => 'F'],
	];
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE' => 'F'],
	];

	// *******************************************************************************************************************

	public static function getDuplicateMethods(){
		return [
			'SIZE' => static::getMessage('METHOD_SIZE'),
			'NAME' => static::getMessage('METHOD_NAME'),
			'DIMENSIONS' => static::getMessage('METHOD_DIMENSIONS'),
			'HASH' => static::getMessage('METHOD_HASH'),
		];
	}
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('property')){
			$this->setError(static::getMessage('ERROR_NO_PROPERTY'));
			return false;
		}
		if($this->get('method_SIZE') == 'N' && $this->get('method_NAME') == 'N' && $this->get('method_DIMENSIONS') == 'N' && $this->get('method_HASH') == 'N'){
			$this->setError(static::getMessage('ERROR_NO_METHODS'));
			return false;
		}
		$arImages = $this->getElementImages($intElementId);
		$this->removeDuplicates($intElementId, $arImages);
		return true;
	}

	protected function getElementImages($intElementId){
		$arResult = [];
		// Get fields
		if($strField = $this->get('field')){
			$arFeatures = [
				'FIELDS' => [$strField],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			if(is_array($arElement[$strField]) && !empty($arElement[$strField])){
				$arResult[$strField] = $arElement[$strField];
			}
		}
		// Get properties
		if($intPropertyId = $this->isProperty($this->get('property'))){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
				'EMPTY_PROPERTIES' => true,
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			if(is_array($arProperty['VALUE'])){
				foreach($arProperty['VALUE'] as $key => $item){
					if($arFile = $this->getFileById($item)){
						$arResult[$intPropertyId][$arProperty['PROPERTY_VALUE_ID'][$key]] = $arFile;
					}
				}
			}
			else{
				if($arFile = $this->getFileById($arProperty['VALUE'])){
					$arResult[$intPropertyId][$arProperty['PROPERTY_VALUE_ID']] = $arFile;
				}
			}
		}
		//
		return $arResult;
	}

	protected function getFileById($intId){
		if(is_numeric($intId) && $intId > 0){
			return \CFile::getFileArray($intId);
		}
		return false;
	}

	protected function removeDuplicates($intElementId, $arImages){
		$arImagesPlain = $this->findAllImages($arImages);
		$arDuplicates = $this->determineDuplicates($arImagesPlain);
		$arDeleteAll = [];
		foreach($arDuplicates as $arDuplicate){
			if($arDuplicate['COUNT'] > 1){
				$arDelete = array_slice($arDuplicate['ITEMS'], 1);
				foreach($arDelete as $arItem){
					$arDeleteAll[$arItem[0]][] = $arItem[1];
				}
			}
		}
		$arSaveDummy = [
			'VALUE' => ['name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0, 'description' => ''],
			'DESCRIPTION' => '',
		];
		foreach($arDeleteAll as $intPropertyId => $arDeleteValuesId){
			$arPropertyCurrentValues = $arImages[$intPropertyId];
			$arSaveValues = [];
			foreach($arPropertyCurrentValues as $intValueId => $arFile){
				$arSaveValues[$intValueId] = in_array($intValueId, $arDeleteValuesId) ? ['del' => 'Y'] : $arSaveDummy;
			}
			if(!empty($arSaveValues)){
				$this->setPropertyValue($intElementId, $intPropertyId, $arSaveValues);
			}
		}
	}

	protected function findAllImages(array $arImages){
		$arImagesPlain = [];
		foreach($arImages as $strField => $arData){
			if(in_array($strField, ['PREVIEW_PICTURE', 'DETAIL_PICTURE'])){
				$arData['FIELD'] = $strField;
				$arImagesPlain[] = $arData;
			}
			elseif(is_numeric($strField)){
				foreach($arData as $intPropertyId => $arFile){
					$arFile['FIELD'] = $strField;
					$arFile['VALUE_ID'] = $intPropertyId;
					$arImagesPlain[] = $arFile;
				}
			}
		}
		return $arImagesPlain;
	}

	protected function determineDuplicates(array $arImagesPlain){
		$arResult = [];
		foreach($arImagesPlain as $arImage){
			$arData = [];
			if($this->get('method_SIZE') == 'Y'){
				$arData['SIZE'] = $arImage['FILE_SIZE'];
			}
			if($this->get('method_NAME') == 'Y'){
				$arData['NAME'] = $arImage['ORIGINAL_NAME'];
			}
			if($this->get('method_DIMENSIONS') == 'Y'){
				$arData['DIMENSIONS'] = [$arImage['WIDTH'], $arImage['HEIGHT']];
			}
			if($this->get('method_HASH') == 'Y'){
				if(mb_strlen($arImage['SRC']) && is_file($strFile = $_SERVER['DOCUMENT_ROOT'].$arImage['SRC'])){
					if(mb_strlen($strData = file_get_contents($strFile))){
						$arData['HASH'] = md5(base64_encode($strData));
					}
				}
			}
			if(!empty($arData)){
				$key = md5(serialize($arData));
				if(is_array($arResult[$key])){
					$arResult[$key]['COUNT']++;
					$arResult[$key]['ITEMS'][] = [$arImage['FIELD'], $arImage['VALUE_ID']];
					$arResult[$key]['FILES'][] = $arImage['SRC'];
				}
				else{
					$arResult[$key] = [
						'DATA' => $arData,
						'COUNT' => 1,
						'ITEMS' => [
							[$arImage['FIELD'], $arImage['VALUE_ID']],
						],
						'FILES' => [
							$arImage['SRC'],
						],
					];
				}
			}
		}
		return $arResult;
	}
	
}

?>