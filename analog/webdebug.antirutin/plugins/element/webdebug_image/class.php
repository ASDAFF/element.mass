<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class WebdebugImage extends PluginElement {
	
	const GROUP = 'MODULES';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE' => 'F'],
		'PROPERTIES' => ['TYPE' => 'F'],
	];
	
	protected $arDeleteFiles = [];
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if(!$this->checkModuleInstalled()){
			$this->setError(static::getMessage('ERROR_NO_MODULE'));
			return false;
		}
		if(!strlen($this->get('source'))){
			$this->setError(static::getMessage('ERROR_NO_SOURCE'));
			return false;
		}
		if(!strlen($this->get('target'))){
			$this->setError(static::getMessage('ERROR_NO_TARGET'));
			return false;
		}
		if(!strlen($this->get('profile'))){
			$this->setError(static::getMessage('ERROR_NO_PROFILE'));
			return false;
		}
		# Get images
		$arImagesId = $this->getElementImages($intElementId);
		# Process images
		$arImages = $this->processImages($arImagesId);
		# Save images
		$bResult = $this->setElementImages($intElementId, $arImages);
		# Delete tmp images
		$this->deleteTmpImages();
		#
		return $bResult;
	}
	
	/**
	 *	Get elements images
	 */
	protected function getElementImages($intElementId){
		$arResult = [];
		$strField = $this->get('source');
		if($this->isField($strField)){
			$arFeatures = ['FIELDS' => [$strField], 'SKIP_FIELD_PICTURES' => true];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			if(is_numeric($arElement[$strField]) && $arElement[$strField] > 0){
				$arResult[] = $arElement[$strField];
			}
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = ['PROPERTY_ID' => [$intPropertyId]];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			if(is_array($arProperty['VALUE'])){
				foreach($arProperty['VALUE'] as $key => $intValue){
					$intValueId = $arProperty['PROPERTY_VALUE_ID'][$key];
					$arResult[$intValueId] = $intValue;
				}
			}
			elseif(is_numeric($arProperty['VALUE']) && $arProperty['VALUE']){
				$intValueId = $arProperty['PROPERTY_VALUE_ID'];
				$arResult[$intValueId] = $arProperty['VALUE'];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Set elements images
	 */
	protected function setElementImages($intElementId, $arValues){
		$bResult = false;
		$strField = $this->get('target');
		if($this->isField($strField)){
			$arFields = [$strField => reset($arValues)];
			$bResult = $this->update($intElementId, $arFields, false, false, true);
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$bResult = $this->setPropertyValue($intElementId, $intPropertyId, $arValues);
		}
		return $bResult;
	}
	
	/**
	 *	Process all images
	 */
	protected function processImages($arImagesId){
		$arResult = [];
		if(is_array($arImagesId) && !empty($arImagesId)){
			$intProfileId = $this->get('profile');
			foreach($arImagesId as $intValueId => $intImageId){
				$arImage = \CFile::getFileArray($intImageId);
				if(is_array($arImage)){
					$strImageSrc = $this->processImage($intProfileId, $arImage['SRC']);
					if(strlen($strImageSrc)) {
						$strImageSrcAbs = Helper::root().$strImageSrc;
						if(is_file($strImageSrcAbs) && filesize($strImageSrcAbs)){
							$arNewFile = \CFile::makeFileArray($strImageSrcAbs);
							if(is_array($arNewFile)) {
								$arNewFile = array_merge($arNewFile, [
									'MODULE_ID' => $arImage['MODULE_ID'],
									'DESCRIPTION' => $arImage['DESCRIPTION'],
									'description' => $arImage['DESCRIPTION'],
									'EXTERNAL_ID' => $arImage['EXTERNAL_ID'],
									'external_id' => $arImage['EXTERNAL_ID'],
								]);
								$arResult[$intValueId] = $arNewFile;
								$this->arDeleteFiles[] = $strImageSrc;
							}
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Process single image by src
	 */
	protected function processImage($intProfileId, $strImageSrc){
		return $GLOBALS['APPLICATION']->includeComponent(
			'webdebug:image',
			'',
			[
				'PROFILE' => $intProfileId,
				'RETURN' => 'SRC',
				'CACHE_IMAGE' => 'N',
				'IMAGE' => $strImageSrc,
				'DESCRIPTION' => '',
				'DISPLAY_ERRORS' => 'N'
			],
			false,
			['HIDE_ICONS' => 'Y']
		);
	}
	
	/**
	 *	Delete temp images
	 */
	protected function deleteTmpImages(){
		if(is_array($this->arDeleteFiles)){
			foreach($this->arDeleteFiles as $strFile) {
				# Remove file
				@unlink(Helper::root().$strFile);
				# Remove dir (recursively from last child to root, but /upload/ and /bitrix/)
				$arFileName = array_slice(explode('/',$strFile), 1, -1);
				for($i = count($arFileName); $i>=0; $i--){
					$strDir = implode('/', array_slice($arFileName, 0, $i));
					if(!empty($strDir)) {
						if(in_array(ToLower($strDir), ['upload', 'bitrix'])){
							break;
						}
						if(is_dir(Helper::root().'/'.$strDir)){
							@rmdir(Helper::root().'/'.$strDir);
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Is module installed?
	 */
	protected function checkModuleInstalled(){
		return \Bitrix\Main\Loader::includeModule('webdebug.image');
	}
	
	/**
	 *	Get webdebug.image profiles
	 */
	protected function getWebdebugImageProfiles(){
		$arResult = [];
		if(\Bitrix\Main\Loader::includeModule('webdebug.image')){
			$obWebdebugImageProfile = new \CWebdebugImageProfile;
			$resProfiles = $obWebdebugImageProfile->getList(['SORT' => 'ASC', 'NAME' => 'ASC'], []);
			while($arProfile = $resProfiles->getNext(false, false)){
				if(empty($arProfile['NAME'])){
					$arProfile['NAME'] = static::getMessage('WD_IMAGE_PROFILE_NONAME');
				}
				$arResult[$arProfile['ID']] = $arProfile;
			}
		}
		return $arResult;
	}
	
}

?>