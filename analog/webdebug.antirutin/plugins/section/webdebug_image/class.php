<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class WebdebugImage extends PluginSection {
	
	const GROUP = 'IMAGES';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE' => 'F'],
		'PROPERTIES' => ['TYPE' => 'F'],
	];
	
	protected $arDeleteFiles = [];
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
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
		$arImagesId = $this->getSectionImages($intSectionId);
		# Process images
		$arImages = $this->processImages($arImagesId);
		# Save images
		$bResult = $this->setSectionImages($intSectionId, $arImages);
		# Delete tmp images
		$this->deleteTmpImages();
		#
		return $bResult;
	}
	
	/**
	 *	Get Sections images
	 */
	protected function getSectionImages($intSectionId){
		$arResult = [];
		$strField = $this->get('source');
		if($this->isSectionField($strField)){
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, [$strField]);
			if(is_array($arSection[$strField]) && $arSection[$strField]['ID'] > 0){
				$arResult[] = $arSection[$strField]['ID'];
			}
		}
		elseif($strProperty = $this->isSectionProperty($strField)){
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, [$strProperty]);
			$arProperty = $arSection['PROPERTIES'][$strProperty];
			if(is_array($arProperty)){
				foreach($arProperty as $key => $intValue){
					$arResult[] = $intValue;
				}
			}
			elseif(is_numeric($arProperty) && $arProperty){
				$arResult[] = $arProperty;
			}
		}
		return $arResult;
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
	 *	Set Sections images
	 */
	protected function setSectionImages($intSectionId, $arValues){
		$bResult = false;
		$strField = $this->get('target');
		if($this->isSectionField($strField)){
			$arFields = [$strField => reset($arValues)];
			$bResult = $this->update($intSectionId, $arFields, false, false, true);
		}
		elseif($strProperty = $this->isSectionProperty($strField)){
			if(is_array($arValues[0])){
				if($arProperty = $this->getProperty($strProperty)){
					if($arProperty['MULTIPLE'] != 'Y'){
						$arValues = reset($arValues);
					}
				}
			}
			$bResult = $this->update($intSectionId, [$strProperty => $arValues]);
		}
		return $bResult;
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
			$resProfiles = \CWebdebugImageProfile::getList(['SORT' => 'ASC', 'NAME' => 'ASC'], []);
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