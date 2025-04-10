<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ImageRename extends PluginElement {
	
	const GROUP = 'IMAGES';

	const MODE_TRANSLITERATE_NAME = 'TRANSLITERATE_NAME';
	const MODE_USE_ORIGINAL_NAME = 'USE_ORIGINAL_NAME';
	
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
		$strMethod = $this->get('method');
		#
		$arField = is_array($arField) ? array_filter($arField) : $arField;
		if(empty($arField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		#
		$arElement = $this->getElementArray($intElementId);
		$strElementName = trim($arElement['NAME']);
		#
		foreach($arField as $strField) {
			if($this->isField($strField)){
				if(is_array($arElement[$strField])){
					$this->renameFile($arElement[$strField], $arElement['NAME'], $strMethod);
				}
			}
			elseif($intPropertyId = $this->isProperty($strField)) {
				$mValue = $arElement['PROPERTIES'][$intPropertyId]['VALUE'];
				if(is_array($mValue)){
					foreach($mValue as $intFileId){
						if(is_numeric($intFileId) && $arFile = \CFile::getFileArray($intFileId)){
							$this->renameFile($arFile, $arElement['NAME'], $strMethod);
						}
					}
				}
				else{
					if(is_numeric($mValue) && $arFile = \CFile::getFileArray($mValue)){
						$this->renameFile($arFile, $arElement['NAME'], $strMethod);
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
	 *	Get array for current element
	 */
	protected function getElementArray($intElementId){
		$arField = $this->get('field');
		if(is_array($arField)){
			$arFeatures = [
				'FIELDS' => ['NAME'],
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
	 * Rename file physically and then rename file in database
	 */
	protected function renameFile($arFile, $strElementName, $strMethod){
		$bResult = false;
		if(strlen($strOldFile = $arFile['SRC'])){
			$arNewFilename = $this->getNewFilenameArray($arFile, $strElementName, $strMethod);
			if(strlen($strDir = $arNewFilename['dirname'])){
				if(strlen($strFilename = $arNewFilename['filename'])){
					if(strlen($strBasename = $arNewFilename['basename'])){
						if(strlen($strExt = $arNewFilename['extension'])){
							if(strlen($strNewFile = $this->obtainNewPhysicalFilename($strDir, $strFilename, $strExt))){
								if($strOldFile != $strNewFile){
									if(rename(Helper::root().$strOldFile, Helper::root().$strNewFile)){
										$strNewFileBasename = pathinfo($strNewFile, PATHINFO_BASENAME);
										$strSql = sprintf("UPDATE b_file SET FILE_NAME = '%s' WHERE ID = %d;",
											Helper::forSql($strNewFileBasename), $arFile['ID']);
										\Bitrix\Main\Application::getConnection()->query($strSql);
										\CFile::cleanCache($arFile['ID']);
										$bResult = true;
									}
								}
								else{
									$bResult = true;
								}
							}
						}
					}
				}
			}
		}
		return $bResult;
	}

	/**
	 * $arFile is array of bitrix file (array from b_file)
	 * $strElementName is iblock element name
	 * $strMethod is static::MODE_TRANSLITERATE_NAME || static::MODE_USE_ORIGINAL_NAME
	 * @return array ['filename' => '', 'extension' => '']
	 */
	protected function getNewFilenameArray($arFile, $strElementName, $strMethod){
		$arResult = [];
		$arModesAllowed = [
			static::MODE_TRANSLITERATE_NAME,
			static::MODE_USE_ORIGINAL_NAME,
		];
		if(!in_array($strMethod, $arModesAllowed)){
			$strMethod = reset($arModesAllowed);
		}
		if($strMethod == static::MODE_USE_ORIGINAL_NAME){
			$arFileinfo = pathinfo($arFile['ORIGINAL_NAME']);
			$arResult['dirname'] = pathinfo($arFile['SRC'], PATHINFO_DIRNAME);
			$arResult['filename'] = $this->translitName($arFileinfo['filename']);
			$arResult['extension'] = $arFileinfo['extension'];
			$arResult['basename'] = $this->buildBasename($arResult['filename'], $arResult['extension']);
		}
		elseif($strMethod == static::MODE_TRANSLITERATE_NAME){
			$arFileinfo = pathinfo($arFile['FILE_NAME']);
			$arResult['dirname'] = pathinfo($arFile['SRC'], PATHINFO_DIRNAME);
			$arResult['filename'] = $this->translitName($strElementName);
			$arResult['extension'] = $arFileinfo['extension'];
			$arResult['basename'] = $this->buildBasename($arResult['filename'], $arResult['extension']);
		}
		return $arResult;
	}

	protected function translitName($strName){
		return \CUtil::translit($strName, LANGUAGE_ID, [
			'max_len' => 255,
			'change_case' => 'L',
			'replace_space' => '-',
			'replace_other' => '',
			'delete_repeat_replace' => true,
		]);
	}

	/**
	 * Get new filename (example: /upload/iblock/abc/image.jpg) using index (if new filename already taken)
	 */
	protected function obtainNewPhysicalFilename($strDir, $strFilename, $strExtension){
		$strResult = null;
		$intIndex = 0;
		while(true){
			$strFilenameWithIndex = $intIndex > 0 ? sprintf('%s-%d', $strFilename, $intIndex) : $strFilename;
			$strResult = sprintf('%s/%s', $strDir, $this->buildBasename($strFilenameWithIndex, $strExtension));
			if(!is_file(Helper::root().$strResult)){
				break;
				
			}
			$intIndex++;
		}
		return $strResult;
	}

	protected function buildBasename($strFilename, $strExtension){
		return $strFilename.(strlen($strExtension) ? '.'.$strExtension : '');
	}
	
}

?>