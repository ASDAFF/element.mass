<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class RemoveDuplicates extends PluginElement {
	
	const PROP_CODE = 'WDA_REMOVE_DUPLICATES';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['CODE' => ['NAME', 'CODE', 'XML_ID']],
		'PROPERTIES' => ['TYPE_FULL' => ['S'], 'IS_WRITEABLE' => 'Y', 'MULTIPLE' => 'N'],
	];
	
	protected $arFieldsFilter2 = [
		'FIELDS' => ['CODE' => ['NAME', 'XML_ID', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']],
		'PROPERTIES' => [],
	];
	
	protected $arCopyFields = ['ACTIVE_FROM', 'ACTIVE_TO', 'NAME', 'SORT', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE',
		'PREVIEW_TEXT', 'DETAIL_TEXT', 'TAGS', 'XML_ID'];
	
	protected $arWhich = [
		1 => 'OLD',
		2 => 'NEW',
	];
	
	protected $arAction = [
		1 => 'DEACTIVATE',
		2 => 'DELETE',
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	public function onStart(){
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'CODE' => static::PROP_CODE,
		];
		$resProp = \CIBlockProperty::getList([], $arFilter);
		if(!($arProp = $resProp->fetch())){
			$obProperty = new \CIBlockProperty;
			$arFields = [
				'NAME' => static::getMessage('PROP_NAME'),
				'CODE' => static::PROP_CODE,
				'SORT' => '500',
				'IBLOCK_ID' => $this->intIBlockId,
				'PROPERTY_TYPE' => 'S',
				'ACTIVE' => 'Y',
				'SECTION_PROPERTY' => 'N',
				'COL_COUNT' => '50',
			];
			if(!$obProperty->add($arFields)){
				return static::getMessage('PROP_CREATE_ERROR', ['#ERROR#' => $obProperty->LAST_ERROR]);
			}
		}
	}
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		#
		$bActual = false;
		if($this->checkElementDuplicate($intElementId, $bActual)){ // Проверка если свойство было записано ранее
			return true;
		}
		#
		$arElements = $this->searchSameElements($intElementId);
		$bResult = $this->processSameElements($arElements);
		#
		if($this->checkElementDuplicate($intElementId, $bActual)){ // Проверка если свойство было записано только что
			return true;
		}
		#
		return $bResult;
	}
	
	/**
	 *	Check element is duplicate
	 */
	protected function checkElementDuplicate($intElementId, &$bActual){
		$bResult = false;
		$strCurSessionId = $this->obWorker->getSessionId();
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'ID' => $intElementId,
		];
		$resElement = \CIBlockElement::getList([], $arFilter, false, false, ['ID', 'PROPERTY_'.static::PROP_CODE]);
		if($arElement = $resElement->fetch()){
			list($strSessionId, $strActualItem) = explode('_', $arElement['PROPERTY_'.static::PROP_CODE.'_VALUE']);
			if($strSessionId == $strCurSessionId){
				$bResult = true;
				$bActual = $strActualItem == 'Y';
				if(!$bActual){
					$this->deleteOrDeactivateElement($intElementId);
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Delete element
	 */
	protected function deleteOrDeactivateElement($intElementId){
		switch($this->get('action')){
			case 'delete':
				\CIBlockElement::delete($intElementId);
				$this->log(static::getMessage('LOG_ELEMENT_DELETE', ['#ID#' => $intElementId]));
				break;
			case 'deactivate':
				$this->obElement->update($intElementId, ['ACTIVE' => 'N'], false, false, false, false);
				$this->log(static::getMessage('LOG_ELEMENT_DELETE', ['#ID#' => $intElementId]));
				break;
		}
	}
	
	/**
	 *	Search same elements for current element
	 */
	protected function searchSameElements($intElementId){
		$arResult = [];
		$strField = $this->get('field');
		$arFeatures = [];
		if($this->isField($strField)){
			$arFeatures['FIELDS'] = [$strField];
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures['PROPERTY_ID'] = [$intPropertyId];
			$arFeatures['EMPTY_PROPERTIES'] = true;
		}
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		$arFilter = [];
		if($this->arSectionsId){
			$arFilter['SECTION_ID'] = $this->arSectionsId;
			if($this->bIncludeSubsections){
				$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
			}
		}
		$bValueFilled = false;
		if($this->isField($strField)){
			if(strlen($arElement[$strField])){
				$arFilter['='.$strField] = $arElement[$strField];
				$bValueFilled = true;
			}
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			if(strlen($arElement['PROPERTIES'][$intPropertyId]['VALUE'])){
				$arFilter['=PROPERTY_'.$intPropertyId] = $arElement['PROPERTIES'][$intPropertyId]['VALUE'];
				$bValueFilled = true;
			}
		}
		if(!$bValueFilled){
			return [];
		}
		$arResult = [];
		$arFeatures = [
			'FIELDS' => $this->arCopyFields,
			'PROPERTY_ID' => true,
			'EMPTY_PROPERTIES' => true,
			'SEO_TEMPLATES' => true,
			'QUANTITY' => true,
			'PRICES' => true,
			'STORES' => true,
			'OFFERS' => true,
			'BARCODE' => true,
		];
		$resSameElements = IBlock::getElementList([], $arFilter, ['ID']);
		while($arSameElement = $resSameElements->fetch()){
			$arResult[$arSameElement['ID']] = IBlock::getElementArray($arSameElement['ID'], $this->intIBlockId, $arFeatures);
		}
		return $arResult;
	}
	
	/**
	 *	Process found items
	 */
	protected function processSameElements($arElements){
		if(is_array($arElements) && count($arElements) >= 2) {
			$this->log(static::getMessage('LOG_FOUND_SAME_ELEMENTS', ['#IDS#' => implode(', ', array_keys($arElements))]));
			uasort($arElements, function($a, $b){
				return $a['ID'] == $b['ID'] ? 0 : ($a['ID'] > $b['ID'] ? 1 : -1);
			});
			$arTargetElement = array_shift(array_values($arElements)); // First element (smallest ID)
			$arSourceElement = array_pop(array_values($arElements)); // Last element (largest ID)
			if($this->arWhich[$this->get('which')] == 'NEW'){
				list($arTargetElement, $arSourceElement) = [$arSourceElement, $arTargetElement];
			}
			$this->log(static::getMessage('LOG_TARGET', ['#ID#' => $arTargetElement['ID']]));
			$this->log(static::getMessage('LOG_SOURCE', ['#ID#' => $arSourceElement['ID']]));
			$arSaveValues = $this->getValuesForSave($arTargetElement, $arSourceElement);
			$bSaved = false;
			if(is_array($arSaveValues) && !empty($arSaveValues)){
				$bSaved = $this->saveElementValues($arTargetElement, $arSaveValues);
			}
			if($bSaved){
				$this->saveSessionMarker(array_keys($arElements), $arTargetElement['ID']);
				$this->copySeoData($arTargetElement, $arSourceElement);
				$this->copyCatalogData($arTargetElement, $arSourceElement);
				$this->moveOffers($arTargetElement, $arSourceElement);
				return true;
			}
			return false;
		}
		return true;
	}
	
	/**
	 *	Get values for save
	 */
	protected function getValuesForSave($arTargetElement, $arSourceElement){
		$arFields = [
			'ACTIVE' => 'Y',
		];
		$arProperties = [];
		$arExlcudeFields = [];
		$arExlcudeProps = [];
		foreach($this->get('exclude_properties') as $key => $strField){
			if($this->isField($strField)){
				$arExlcudeFields[] = $strField;
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$arExlcudeProps[] = $intPropertyId;
			}
		}
		# Process fields
		foreach($this->arCopyFields as $strField){
			if(!in_array($strField, $arExlcudeFields)){
				$this->processFieldValue($arFields, $strField, $arTargetElement, $arSourceElement);
			}
		}
		# Process properties
		if(is_array($arSourceElement['PROPERTIES'])){
			foreach($arSourceElement['PROPERTIES'] as $arProperty){
				if(!in_array($arProperty['ID'], $arExlcudeProps)){
					$this->processPropertyValue($arProperties, $arProperty['ID'], $arTargetElement, $arSourceElement);
				}
			}
		}
		#
		$arResult =  [
			'FIELDS' => $arFields,
			'PROPERTIES' => $arProperties,
		];
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function processFieldValue(&$arResult, $strField, $arTargetElement, $arSourceElement){
		if(in_array($strField, ['PREVIEW_TEXT', 'DETAIL_TEXT'])){
			if(strlen($arSourceElement[$strField])){
				$arResult[$strField] = $arSourceElement[$strField];
				$arResult[$strField.'_TYPE'] = $arSourceElement[$strField.'_TYPE'];
			}
		}
		elseif(in_array($strField, ['PREVIEW_PICTURE', 'DETAIL_PICTURE'])){
			if(is_array($arSourceElement[$strField]) && $arSourceElement[$strField]['ID']){
				$arResult[$strField] = \CFile::makeFileArray($arSourceElement[$strField]['ID'], false, false,
					$arSourceElement['EXTERNAL_ID']);
				$arResult[$strField]['description'] = $arSourceElement['DESCRIPTION'];
			}
		}
		elseif(strlen($arSourceElement[$strField])){
			$arResult[$strField] = $arSourceElement[$strField];
		}
	}
	
	/**
	 *	
	 */
	protected function processPropertyValue(&$arResult, $intPropertyId, $arTargetElement, $arSourceElement){
		$arProperty = $arSourceElement['PROPERTIES'][$intPropertyId];
		$mValue = $arProperty['PROPERTY_TYPE'] == 'L' ? $arProperty['VALUE_ENUM_ID'] : $arProperty['~VALUE'];
		if(is_array($mValue) && !empty($mValue) || strlen($mValue)){
			if($arProperty['PROPERTY_TYPE'] == 'F'){
				if(is_array($mValue)){
					$arResult[$intPropertyId] = [];
					foreach($mValue as $key => $intFileId){
						$arFile = \CFile::makeFileArray($intFileId);
						$arFile['description'] = $arProperty['DESCRIPTION'][$key];
						$arResult[$intPropertyId][] = $arFile;
					}
				}
				else{
					$arFile = \CFile::makeFileArray($mValue);
					$arFile['description'] = $arProperty['DESCRIPTION'];
					$arResult[$intPropertyId] = $arFile;
				}
			}
			elseif($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'HTML'){
				if($arProperty['MULTIPLE'] == 'Y'){
					$arResult[$intPropertyId] = [];
					foreach($mValue as $key => $arValue){
						$arResult[$intPropertyId][] = [
							'VALUE' => $arValue,
							'DESCRIPTION' => $arProperty['DESCRIPTION'][$key],
						];
					}
				}
				else{
					$arResult[$intPropertyId] = [
						'VALUE' => $mValue,
						'DESCRIPTION' => $arProperty['DESCRIPTION'],
					];
				}
			}
			else{
				if($arProperty['MULTIPLE'] == 'Y'){
					foreach($mValue as $key => $strValue){
						$arResult[$intPropertyId][] = [
							'VALUE' => $strValue,
							'DESCRIPTION' => $arProperty['DESCRIPTION'][$key],
						];
					}
				}
				else{
					$arResult[$intPropertyId] = $mValue;
				}
			}
		}
	}

	/**
	 *	Save element
	 */
	protected function saveElementValues($arElement, $arValues){
		$bResult = false;
		if(is_array($arValues['FIELDS']) && !empty($arValues['FIELDS'])){
			$bResult = $this->update($arElement['ID'], $arValues['FIELDS'], false, true, false, true);
		}
		if($bResult && is_array($arValues['PROPERTIES']) && !empty($arValues['PROPERTIES'])){
			$this->setPropertyValue($arElement['ID'], null, null, $arValues['PROPERTIES']);
		}
		return $bResult;
	}
	
	/**
	 *	Save session to property static::PROP_CODE
	 */
	protected function saveSessionMarker($arElementsId, $intTargetId){
		foreach($arElementsId as $intElementId){
			$this->setPropertyValue($intElementId, null, null, [
				static::PROP_CODE => $this->obWorker->getSessionId().'_'.($intTargetId == $intElementId ? 'Y' : 'N'),
			]);
		}
	}
	
	/**
	 *	Copy seo data
	 */
	protected function copySeoData($arTargetElement, $arSourceElement){
		$arSeoFieldsMap = [
			'TITLE' => 'ELEMENT_META_TITLE',
			'KEYWORDS' => 'ELEMENT_META_KEYWORDS',
			'DESCRIPTION' => 'ELEMENT_META_DESCRIPTION',
			'H1' => 'ELEMENT_PAGE_TITLE',
		];
		$arSeoFields = [];
		foreach($arSeoFieldsMap as $strField => $strFieldOriginal){
			$arField = $arSourceElement['SEO_TEMPLATES'][$strField];
			$arSeoFields[$strFieldOriginal] = $arField['INHERITED'] == 'N' ? $arField['TEMPLATE'] : '';
		}
		if(!empty($arSeoFields)){
			$arFields = [
				'IPROPERTY_TEMPLATES' => $arSeoFields,
			];
			$this->obElement->update($arTargetElement['ID'], $arFields, false, false, false, false);
		}
	}
	
	/**
	 *	Copy catalog data, prices, stores, barcodes
	 */
	protected function copyCatalogData($arTargetElement, $arSourceElement){
		$arSourceProduct = \CCatalogProduct::getById($arSourceElement['ID']);
		if(is_array($arSourceProduct)){
			$arSourceProduct['ID'] = $arTargetElement['ID'];
			$arSourceProduct['QUANTITY_TRACE'] = $arSourceProduct['QUANTITY_TRACE_ORIG'];
			$arSourceProduct['CAN_BUY_ZERO'] = $arSourceProduct['CAN_BUY_ZERO_ORIG'];
			$arSourceProduct['NEGATIVE_AMOUNT_TRACE'] = $arSourceProduct['NEGATIVE_AMOUNT_TRACE_ORIG'];
			$arSourceProduct['SUBSCRIBE'] = $arSourceProduct['SUBSCRIBE_ORIG'];
			# Unset waste fields
			unset($arSourceProduct['QUANTITY_TRACE_ORIG']);
			unset($arSourceProduct['CAN_BUY_ZERO_ORIG']);
			unset($arSourceProduct['NEGATIVE_AMOUNT_TRACE_ORIG']);
			unset($arSourceProduct['SUBSCRIBE_ORIG']);
			# Save product
			\CCatalogProduct::add($arSourceProduct);
			# Copy prices
			if(is_array($arSourceElement['PRICES']) && !empty($arSourceElement['PRICES'])){
				foreach($arSourceElement['PRICES'] as $intPriceId => $arPrice){
					Helper::setProductPrice($arTargetElement['ID'], $intPriceId, $arPrice['PRICE'], $arPrice['CURRENCY']);
				}
			}
			# Copy measure ratio
			$resRatio = \CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $arSourceElement['ID']]);
			if($arRatio = $resRatio->getNext(false, false)) {
				Helper::setProductMeasureRatio($arTargetElement['ID'], $arRatio['RATIO']);
			}
			# Copy stores
			if(Helper::isCatalogStoresAvailable() && is_array($arSourceElement['STORES'])){
				if(!empty($arSourceElement['STORES'])){
					foreach($arSourceElement['STORES'] as $arStore){
						Helper::setProductStoreAmount($arTargetElement['ID'], $arStore['STORE_ID'], $arStore['AMOUNT']);
					}
				}
			}
			# Copy barcodes
			if(Helper::isCatalogBarcodeAvailable() && is_array($arSourceElement['CATALOG_BARCODE'])){
				if(!empty($arSourceElement['CATALOG_BARCODE']) && !empty($arSourceElement['CATALOG_BARCODE_FULL'])){
					foreach($arSourceElement['CATALOG_BARCODE_FULL'] as $intBarcodeId => $arBarcode){
						\CCatalogStoreBarCode::delete($intBarcodeId);
					}
					Helper::setProductBarcodes($arTargetElement['ID'], $arSourceElement['CATALOG_BARCODE']);
				}
			}
		}
	}
	
	/**
	 *	Move offers from old to new
	 */
	protected function moveOffers($arTargetElement, $arSourceElement){
		$arCatalog = Helper::getCatalogArray($this->intIBlockId);
		if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID']){
			# If there are offers in old (source) element ...
			if(is_array($arSourceElement['OFFERS']) && !empty($arSourceElement['OFFERS'])){
				# ... then deactivate offers in new (target) element ...
				if(is_array($arTargetElement['OFFERS']) && !empty($arTargetElement['OFFERS'])){
					switch($this->get('action')){
						case 'delete':
							foreach($arTargetElement['OFFERS'] as $intOfferId){
								$this->obElement::delete($intOfferId);
							}
							break;
						case 'deactivate':
							foreach($arTargetElement['OFFERS'] as $intOfferId){
								$this->obElement->update($intOfferId, ['ACTIVE' => 'N'], false, false, false, false);
							}
							break;
					}
				}
				# ... and move offers from old to new
				foreach($arSourceElement['OFFERS'] as $intOfferId){
					$arValue = [
						$arCatalog['OFFERS_PROPERTY_ID'] => $arTargetElement['ID'],
					];
					\CIBlockElement::setPropertyValuesEx($intOfferId, $arCatalog['OFFERS_IBLOCK_ID'], $arValue);
				}
			}
		}
	}
	
}

?>