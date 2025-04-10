<?php
namespace WD\Antirutin\Plugins\Element;

use
\WD\Antirutin\Helper,
\WD\Antirutin\IBlock,
\WD\Antirutin\ValueItem,
\WD\Antirutin\PluginElement;

class ExtractImagesFromText extends PluginElement {
	
	const GROUP = 'IMAGES';

	const MODE_TEXT_CUT = 'cut';
	const MODE_TEXT_COPY = 'copy';

	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S:HTML']],
		'PROPERTIES' => ['TYPE_FULL' => ['S:HTML']],
	];
	
	protected $arFieldsPictureMain = [
		'FIELDS' => ['TYPE_FULL' => ['F']],
		'PROPERTIES' => ['TYPE_FULL' => ['F'], 'MULTIPLE' => 'N'],
	];
	
	protected $arFieldsPictureMore = [
		'PROPERTIES' => ['TYPE_FULL' => ['F'], 'MULTIPLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *	Execute!
	 */

	public function processElement($intElementId){
		if(!strlen($this->get('html_field'))){
			$this->setError(static::getMessage('ERROR_NO_HTML_FIELD'));
			return false;
		}
		$arHtmlValues = $this->getHtmlValues($intElementId);
		$arImages = $this->findImages($arHtmlValues);
		if($this->saveImages($intElementId, $arImages)){
			$this->saveTexts($intElementId, $arHtmlValues);
		}
		return true;
	}

	protected function getHtmlValues($intElementId){
		$arResult = [];
		$strField = $this->get('html_field');
		if($this->isField($strField)){
			$arFeatures = [
				'FIELDS' => [$strField, $strField.'_TYPE'],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			$arResult[] = new ValueItem(['TEXT' => $arElement[$strField], 'TYPE' => $arElement[$strField.'_TYPE']]);
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
				'EMPTY_PROPERTIES' => false,
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			if($arElement['PROPERTIES'][$intPropertyId]['MULTIPLE'] == 'Y'){
				if(is_array($arElement['PROPERTIES'][$intPropertyId]['~VALUE'])){
					foreach($arElement['PROPERTIES'][$intPropertyId]['~VALUE'] as $key => $arValue){
						$obValue = new ValueItem(['TEXT' => $arValue['TEXT'], 'TYPE' => $arValue['TYPE']]);
						$obValue->set('VALUE_ID', $arElement['PROPERTIES'][$intPropertyId]['PROPERTY_VALUE_ID'][$key]);
						$obValue->set('DESCRIPTION', $arElement['PROPERTIES'][$intPropertyId]['DESCRIPTION'][$key]);
						$arResult[] = $obValue;
					}
				}
			}
			else{
				$arValue = $arElement['PROPERTIES'][$intPropertyId]['~VALUE'];
				$arResult[] = new ValueItem(['TEXT' => $arValue['TEXT'], 'TYPE' => $arValue['TYPE']]);
			}
		}
		return $arResult;
	}

	protected function findImages(&$arHtmlValues){
		$arImages = [];
		foreach($arHtmlValues as $obValue){
			if(strlen($strHtml = $obValue->get('TEXT'))){
				$strHtml = preg_replace_callback('#<img\b([^>]+)>#is', function($arMatch)use(&$arImages){
					$strAttr = trim($arMatch[1], ' /');
					if(preg_match('#src=["\'](.*?)["\']#is', $strAttr, $arMatch2)){
						$arImages[] = $arMatch2[1];
					}
					return '';
				}, $strHtml);
				$obValue->set('TEXT', $strHtml);
			}
		}
		return $arImages;
	}

	protected function saveImages($intElementId, $arImages){
		if(empty($arImages)){
			return false;
		}
		$strMain = $this->get('field_picture_main');
		$strMore = $this->get('field_picture_more');
		$strMainImage = false;
		$arMoreImage = false;
		if(strlen($strMain) && count($arImages) > 0){
			if(!empty($arImages)){
				$arFeatures = [
					'FIELDS' => [$strMain],
				];
				$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
				if(empty($arElement[$strMain])){
					$strMainImage = array_shift($arImages);
				}
			}
		}
		if(!empty($arImages)){
			$arMoreImage = array_values($arImages);
		}
		if($strMainImage !== false){
			$this->saveMainImage($intElementId, $strMainImage);
		}
		if($arMoreImage !== false){
			$this->saveMoreImages($intElementId, $arMoreImage);
		}
		return true;
	}

	protected function saveMainImage($intElementId, $strMainImage){
		$strField = $this->get('field_picture_main');
		if($this->isField($strField)){
			$arFeatures = [
				'FIELDS' => [$strField],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			if(is_array($arElement[$strField]) && $arElement[$strField]['EXTERNAL_ID'] == $strMainImage){
				return;
			}
			$strExternalId = $this->getImageExternalId($strImage);
			$arFile = \CFile::makeFileArray($strMainImage, false, false, $strExternalId);
			if(is_array($arFile)){
				$this->update($intElementId, [$strField => $arFile]);
			}
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		}
	}

	protected function saveMoreImages($intElementId, $arMoreImage){
		$strField = $this->get('field_picture_more');
		if($intPropertyId = $this->isProperty($strField)){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			$arValue = $arElement['PROPERTIES'][$intPropertyId]['VALUE'];
			$arValueId = $arElement['PROPERTIES'][$intPropertyId]['PROPERTY_VALUE_ID'];
			if(!is_array($arValue)){
				$arValue = [$arValue];
				$arValueId = [$arValueId];
			}
			#
			$arFiles = [];
			$arExist = [];
			foreach($arValue as $key => $intFileId){
				if($intFileId){
					$arFiles[$key] = \CFile::getFileArray($intFileId);
					if(strlen($arFiles[$key]['EXTERNAL_ID'])){
						$arExist[$arFiles[$key]['EXTERNAL_ID']] = $arFiles[$key]['FILE_SIZE'];
					}
				}
			}
			# Save old files
			$arResultFiles = [];
			foreach($arFiles as $key => $arFile){
				$arResultFiles[$arValueId[$key]] = ['VALUE' => ['name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0]];
			}
			# Add new files
			$intNewFileIndex = 0;
			foreach($arMoreImage as $strImage){
				$strExternalId = $this->getImageExternalId($strImage);
				$arImage = \CFile::makeFileArray($strImage, false, false, $strExternalId);
				$bExists = isset($arExist[$strExternalId]) && $arExist[$strExternalId] == $arImage['size'];
				if(!$bExists){
					$arResultFiles['n'.$intNewFileIndex++] = $arImage;
					$arExist[$strExternalId] = $arImage['size'];
				}
			}
			$this->setPropertyValue($intElementId, $intPropertyId, $arResultFiles, null, $this->intIBlockId);
		}
	}

	protected function getImageExternalId($strImage){
		return md5($strImage);
	}

	protected function saveTexts($intElementId, $arHtmlValues){
		if($this->get('mode_text') == static::MODE_TEXT_CUT){
			$strField = $this->get('html_field');
			if($this->isField($strField)){
				foreach($arHtmlValues as $obValue){
					$arFields = [
						$strField => $obValue->get('TEXT'),
						$strField.'_TYPE' => $obValue->get('TYPE'),
					];
					$this->update($intElementId, $arFields);
					break;
				}
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$arProperty = $this->getProperty($intPropertyId, true, $this->intIBlockId);
				$arValue = [];
				if($arProperty['MULTIPLE'] == 'Y'){
					foreach($arHtmlValues as $obValue){
						$arValue[$obValue->get('VALUE_ID')] = [
							'VALUE' => [
								'TEXT' => $obValue->get('TEXT'),
								'TYPE' => $obValue->get('TYPE'),
							],
							'DESCRIPTION' => $obValue->get('DESCRIPTION'),
						];
					}
				}
				else{
					foreach($arHtmlValues as $obValue){
						$arValue = [
							'VALUE' => [
								'TEXT' => $obValue->get('TEXT'),
								'TYPE' => $obValue->get('TYPE'),
							],
							'DESCRIPTION' => $obValue->get('DESCRIPTION'),
						];
						break;
					}
				}
				$this->setPropertyValue($intElementId, $intPropertyId, $arValue, null, $this->intIBlockId);
			}
		}
	}

}
