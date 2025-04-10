<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

require_once(__DIR__.'/../logger/class.php');

class FindBrokenImages extends Logger {
	
	const GROUP = 'IMAGES';
	
	const ADD_CODE = 'PICTURES_BROKEN';
	const PICTURES = 'PICTURES_ALL';
	
	/**
	 *	
	 */
	public function __construct(){
		parent::__construct();
		$this->arCustomFields = array_merge($this->arCustomFields, [
			static::ADD_CODE => [
				'NAME' => static::getMessage('FIELD_BROKEN_PICTURES'),
				'TYPE' => 'S',
			],
		]);
	}
	
	/**
	 *	Changes to css
	 */
	protected function prepareCss(&$strCss){
		$strCss = str_replace('div[data-plugin-form="LOGGER"]', 'div[data-plugin-form="'.static::getCode().'"]', $strCss);
	}
	
	/**
	 *	
	 */
	protected function onLoggerCsvGetHeaderItem(&$arParams, &$arResult){
		if($arParams[3] == static::ADD_CODE){
			$arResult['CSV_HEADER'] = static::getMessage('FIELD_BROKEN_PICTURES');
			return true;
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function onLoggerCsvWriteLine(&$arParams, &$arResult){
		$arSection = &$arParams[2];
		$intSectiontId = $arSection['ID'];
		$arSelect = [
			'PICTURE',
			'DETAIL_PICTURE',
		];
		$arFilter = [
			'ENTITY_ID' => 'IBLOCK_2_SECTION',
			'USER_TYPE_ID' => 'file',
		];
		$resUserFields = \CUserTypeEntity::getList(['ID' => 'ASC'], $arFilter);
		while($arUserField = $resUserFields->fetch()){
			$arSelect[] = $arUserField['FIELD_NAME'];
		}
		$arSectionTmp = IBlock::getSectionArray($intSectiontId, $this->intIBlockId, $arSelect);
		# Find all pictures
		$arPicturesAll = [];
		if(is_array($arSectionTmp['PICTURE'])){
			$arPicturesAll['PICTURE'] = $arSectionTmp['PICTURE']['SRC'];
		}
		if(is_array($arSectionTmp['DETAIL_PICTURE'])){
			$arPicturesAll['DETAIL_PICTURE'] = $arSectionTmp['DETAIL_PICTURE']['SRC'];
		}
		if(is_array($arSectionTmp['PROPERTIES'])){
			foreach($arSectionTmp['PROPERTIES'] as $strPropertyCode => $mValue){
				if(substr($strPropertyCode, 0, 1) == '~'){
					continue;
				}
				if(!is_array($arPicturesAll[$strPropertyCode])){
					$arPicturesAll[$strPropertyCode] = [];
				}
				if(!empty($mValue)){
					$arProperty = $this->getProperty($strPropertyCode);
					if($arProperty['PROPERTY_TYPE'] == 'F'){
						if(is_numeric($mValue)){
							$arPicturesAll[$strPropertyCode][$mValue] = \CFile::getPath($mValue);
						}
						elseif(is_array($mValue)){
							foreach($mValue as $intFileId){
								if(is_numeric($intFileId)){
									$arPicturesAll[$strPropertyCode][$intFileId] = \CFile::getPath($intFileId);
								}
							}
						}
					}
				}
			}
		}
		$arSection[static::PICTURES] = $arPicturesAll;
		# Check broken links
		$arPicturesBroken = [];
		foreach($arPicturesAll as $strField => $mValue){
			if(is_array($mValue)){
				foreach($mValue as $key => $value){
					if($this->isImageBroken($value)){
						$arPicturesBroken[] = $this->makeFullUrl($value);
					}
				}
			}
			else{
				if($this->isImageBroken($mValue)){
					$arPicturesBroken[] = $this->makeFullUrl($mValue);
				}
			}
		}
		# Save to array
		$arSection[static::ADD_CODE] = $arPicturesBroken;
		#
		unset($arSectionTmp);
		return !empty($arPicturesBroken);
	}
	
	/**
	 *	Check single image
	 */
	protected function isImageBroken($value){
		if(!is_file($_SERVER['DOCUMENT_ROOT'].$value) || !filesize($_SERVER['DOCUMENT_ROOT'].$value)){
			return true;
		}
		elseif($this->get('check_content') == 'Y'){
			$arImageSize = getImageSize($_SERVER['DOCUMENT_ROOT'].$value);
			if(!is_array($arImageSize)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function onLoggerCsvGetLineItem(&$arParams, &$arResult){
		$arSection = &$arParams[2];
		if($arParams[3] == static::ADD_CODE){
			$arResult['CSV_LINE'] = implode("\n", $arSection[static::ADD_CODE]);
			if(!empty($arSection[static::ADD_CODE]) && $this->get('delete_broken_images') == 'Y'){
				$this->deleteBrokenImages($arSection);
			}
			return true;
		}
		return false;
	}
	
	/**
	 *	Delete broken images
	 */
	protected function deleteBrokenImages($arSection){
		$arPicturesAll = $arSection[static::PICTURES];
		$arPicturesBroken = $arSection[static::ADD_CODE];
		#
		if(is_array($arPicturesAll) && is_array($arPicturesBroken) && !empty($arPicturesBroken)){
			foreach($arPicturesAll as $strField => $mValue){
				if($this->isSectionField($strField)){
					if(in_array($mValue, $arPicturesBroken)){
						$this->update($arSection['ID'], [$strField => ['del' => 'Y']], false, false, false);
					}
				}
				elseif($this->isSectionProperty($strField)){
					$arSaveValue = [];
					$bSave = false;
					foreach($mValue as $intFileId => $strFile){
						if(in_array($strFile, $arPicturesBroken)){
							$bSave = true;
							$arSaveValue[] = [
								'name' => '',
								'type' => '',
								'tmp_name' => '',
								'error' => 4,
								'size' => 0,
								'del' => 'Y',
								'old_id' => $intFileId,
							];
						}
						else{
							$arSaveValue[] = [
								'name' => '',
								'type' => '',
								'tmp_name' => '',
								'error' => 4,
								'size' => 0,
								'old_id' => $intFileId,
							];
						}
					}
					if($bSave){
						$this->update($arSection['ID'], [$strField => $arSaveValue], false, false, false);
					}
				}
			}
		}
	}
	
}
?>