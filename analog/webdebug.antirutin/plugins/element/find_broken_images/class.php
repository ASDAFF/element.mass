<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

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
		$arElement = &$arParams[2];
		$intElementId = $arElement['ID'];
		$arFeatures = [
			'FIELDS' => ['PREVIEW_PICTURE', 'DETAIL_PICTURE'],
			'PROPERTY_ID' => [],
		];
		$resProps = \CIBlockProperty::getList([], ['IBLOCK_ID' => $this->intIBlockId, 'PROPERTY_TYPE' => 'F']);
		while($arProp = $resProps->getNext()){
			$arFeatures['PROPERTY_ID'][] = $arProp['ID'];
		}
		$arElementTmp = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
		# Find all pictures
		$arPicturesAll = [];
		if(is_array($arElementTmp['PREVIEW_PICTURE'])){
			$arPicturesAll['PREVIEW_PICTURE'] = $arElementTmp['PREVIEW_PICTURE']['SRC'];
		}
		if(is_array($arElementTmp['DETAIL_PICTURE'])){
			$arPicturesAll['DETAIL_PICTURE'] = $arElementTmp['DETAIL_PICTURE']['SRC'];
		}
		if(is_array($arElementTmp['PROPERTIES'])){
			foreach($arElementTmp['PROPERTIES'] as $arProperty){
				if(!is_array($arPicturesAll['PROPERTY_'.$arProperty['ID']])){
					$arPicturesAll['PROPERTY_'.$arProperty['ID']] = [];
				}
				if($arProperty['PROPERTY_TYPE'] == 'F' && !empty($arProperty['VALUE'])){
					if(is_numeric($arProperty['VALUE'])){
						$intValueId = $arProperty['PROPERTY_VALUE_ID'];
						$arPicturesAll['PROPERTY_'.$arProperty['ID']][$intValueId] = \CFile::getPath($arProperty['VALUE']);
					}
					elseif(is_array($arProperty['VALUE'])){
						foreach($arProperty['VALUE'] as $key => $intFileId){
							if(is_numeric($intFileId)){
								$intValueId = $arProperty['PROPERTY_VALUE_ID'][$key];
								$arPicturesAll['PROPERTY_'.$arProperty['ID']][$intValueId] = \CFile::getPath($intFileId);
							}
						}
					}
				}
			}
		}
		$arElement[static::PICTURES] = $arPicturesAll;
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
		$arElement[static::ADD_CODE] = $arPicturesBroken;
		#
		unset($arElementTmp);
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
	protected function onAfterLoggerCsvWriteLine(&$arParams, &$arResult){
		$arElement = &$arParams[2];
		if(!empty($arElement[static::ADD_CODE]) && $this->get('delete_broken_images') == 'Y'){
			$this->deleteBrokenImages($arElement);
		}
	}
	
	/**
	 *	
	 */
	protected function onLoggerCsvGetLineItem(&$arParams, &$arResult){
		$arElement = &$arParams[2];
		if($arParams[3] == static::ADD_CODE){
			$arResult['CSV_LINE'] = implode("\n", $arElement[static::ADD_CODE]);
			return true;
		}
		return false;
	}
	
	/**
	 *	Delete broken images
	 */
	protected function deleteBrokenImages($arElement){
		$arPicturesAll = $arElement[static::PICTURES];
		$arPicturesBroken = $arElement[static::ADD_CODE];
		if(is_array($arPicturesAll) && is_array($arPicturesBroken) && !empty($arPicturesBroken)){
			$bModified = true;
			foreach($arPicturesAll as $strField => $mValue){
				if($this->isField($strField)){
					if(in_array($mValue, $arPicturesBroken)){
						$this->update($arElement['ID'], [$strField => ['del' => 'Y']], false, false, false);
						$bModified = true;
					}
				}
				elseif($intPropertyId = $this->isProperty($strField)){
					$arSaveValue = [];
					$bSave = false;
					foreach($mValue as $intValueId => $strFile){
						if(in_array($strFile, $arPicturesBroken)){
							$bSave = true;
							$arSaveValue[$intValueId] = [
								'del' => 'Y',
							];
						}
						else{
							$arSaveValue[$intValueId] = [
								'VALUE' => [
									'name' => '',
									'type' => '',
									'tmp_name' => '',
									'error' => 4,
									'size' => 0,
								],
							];
						}
					}
					if($bSave){
						$this->setPropertyValue($arElement['ID'], $intPropertyId, $arSaveValue);
						$bModified = true;
					}
				}
			}
			#
			if($bModified && $this->get('reset_tmp_id') == 'Y'){
				$this->update($arElement['ID'], ['TMP_ID' => '0'], false, false, false);
			}
		}
	}
	
}
?>