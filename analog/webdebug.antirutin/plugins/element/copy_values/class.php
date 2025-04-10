<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class CopyValues extends PluginElement {
	
	const SAVE_CURRENT_VALUES = 'save_current_values';
	const REMOVE_DUPLICATES = 'remove_duplicates';
	const SKIP_NULL_VALUES = 'skip_null_values';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['!CODE' => ['CREATED_BY__NAME', 'MODIFIED_BY__NAME', 'WF_%', 'SHOW_HISTORY', 'SHOW_NEW']],
		'PROPERTIES' => ['!TYPE_FULL' => ['S:video']],
		'SEO' => true,
		'CATALOG' => ['!CODE' => ['VAT_VALUE', 'MEASURE_UNIT', 'MEASURE_NAME', 'OFFERS']],
		'BARCODE' => true,
		'PRICES' => true,
		#
		'OFFERS.FIELDS' => ['!CODE' => ['CREATED_BY__NAME', 'MODIFIED_BY__NAME', 'WF_%', 'SHOW_HISTORY', 'SHOW_NEW']],
		'OFFERS.PROPERTIES' => ['!TYPE_FULL' => ['S:video']],
		'OFFERS.SEO' => true,
		'OFFERS.CATALOG' => ['!CODE' => ['VAT_VALUE', 'MEASURE_UNIT', 'MEASURE_NAME', 'OFFERS']],
		'OFFERS.BARCODE' => true,
		'OFFERS.PRICES' => true,
	];
	
	protected $arFieldsFilter2 = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', '!TYPE_FULL' => ['S:video']],
		'IBLOCK' => ['IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
		'SEO' => true,
		#
		'OFFERS.FIELDS' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.PROPERTIES' => ['IS_WRITEABLE' => 'Y', '!TYPE_FULL' => ['S:video']],
		'OFFERS.IBLOCK' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.PRICES' => ['IS_WRITEABLE' => 'Y'],
		'OFFERS.SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('source')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		if($this->isEmpty('target')){
			$this->setError(static::getMessage('ERROR_NO_TARGET_FIELD'));
			return false;
		}
		#
		$strSource = $this->get('source');
		$strTarget = $this->get('target');
		#
		$intIBlockOffersId = null;
		if($this->isOfferField($strSource) || $this->isOfferField($strTarget)){
			$arCatalog = Helper::getCatalogArray($this->intIBlockId);
			$intIBlockOffersId = $arCatalog['OFFERS_IBLOCK_ID'];
		}
		$strCase = ($this->isOfferField($strSource) ? 'O' : 'P').'-'.($this->isOfferField($strTarget) ? 'O' : 'P');
		switch($strCase){
			case 'P-P':
				$bResult = $this->processElementPP($intElementId);
				break;
			case 'P-O':
				$bResult = $this->processElementPO($intElementId, $intIBlockOffersId);
				break;
			case 'O-O':
				$bResult = $this->processElementOO($intElementId, $intIBlockOffersId);
				break;
			case 'O-P':
				$bResult = $this->processElementOP($intElementId, $intIBlockOffersId);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Copy values product-to-product
	 */
	protected function processElementPP($intElementId){
		$arValues = $this->getFieldValue($intElementId);
		if($this->isSkipNullValues() && $this->checkSourceValueIsEmpty($arValues)){
			$this->log(sprintf('Empty values for element #%s', $intElementId), true);
			return true;
		}
		return $this->processField($intElementId, $arValues);
	}
	
	/**
	 *	Copy values product-to-offer
	 */
	protected function processElementPO($intElementId, $intIBlockOffersId){
		$arValues = $this->getFieldValue($intElementId);
		if($this->isSkipNullValues() && $this->checkSourceValueIsEmpty($arValues)){
			$this->log(sprintf('Empty values for element #%s', $intElementId), true);
			return true;
		}
		$arOffers = $this->getElementOffers($intElementId);
		if(is_array($arOffers) && !empty($arOffers)){
			foreach($arOffers as $intOfferId){
				$this->processField($intOfferId, $arValues, $intIBlockOffersId);
			}
			return true;
		}
		else{
			#$this->setError(static::getMessage('ERROR_NO_OFFERS'));
			return true;
		}
		return false;
	}
	
	/**
	 *	Copy values offer-to-offer
	 */
	protected function processElementOO($intElementId, $intIBlockOffersId){
		$arOffers = $this->getElementOffers($intElementId);
		if(is_array($arOffers) && !empty($arOffers)){
			foreach($arOffers as $intOfferId){
				$arValues = $this->getFieldValue($intOfferId, $intIBlockOffersId);
				if($this->isSkipNullValues() && $this->checkSourceValueIsEmpty($arValues)){
					$this->log(sprintf('Empty values for offer #%s', $intOfferId), true);
					return true;
				}
				$this->processField($intOfferId, $arValues, $intIBlockOffersId);
			}
			return true;
		}
		else{
			#$this->setError(static::getMessage('ERROR_NO_OFFERS'));
			return true;
		}
		return false;
	}
	
	/**
	 *	Copy values offer-to-product
	 */
	protected function processElementOP($intElementId, $intIBlockOffersId){
		$arOfferFilter = $this->getOffersFilter();
		$arOffers = $this->getElementOffers($intElementId, $arOfferFilter);
		if(is_array($arOffers) && !empty($arOffers)){
			foreach($arOffers as $intOfferId){
				$arValues = $this->getFieldValue($intOfferId, $intIBlockOffersId);
				if($this->isSkipNullValues() && $this->checkSourceValueIsEmpty($arValues)){
					$this->log(sprintf('Empty values for offer #%s', $intOfferId), true);
					return true;
				}
				$this->processField($intElementId, $arValues);
				break;
			}
			return true;
		}
		else{
			#$this->setError(static::getMessage('ERROR_NO_OFFERS'));
			return true;
		}
		return false;
	}

	/**
	 * 	Check if source value empty (for prevent save if checkbox checked)
	 */
	protected function checkSourceValueIsEmpty($mValues){
		$mValues = $this->transformObjectToValue($mValues);
		$mValues = is_array($mValues) ? implode('', $mValues) : $mValues;
		$mValues = trim($mValues);
		return strlen($mValues) ? false : true;
	}

	/**
	 * Get filter for get offers
	 */
	protected function getOffersFilter(){
		$arOfferFilter = null;
		#
		$strField = $this->get('source');
		if($strOfferField = $this->isOfferField($strField)){
			if($intPropertyId = $this->isProperty($strOfferField)){
				$arOfferFilter['!'.$strOfferField] = false;
			}
		}
		#
		return $arOfferFilter;
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		if(\Bitrix\Main\Loader::includeModule('currency') && class_exists('\CCurrencyLang')){
			require_once($this->strDirectory.'/include/currencylang.php');
		}
	}
	
	// *******************************************************************************************************************
	// Get methods
	// *******************************************************************************************************************

	/**
	 *	Include classes
	 */
	protected function getFieldValue($intElementId, $intIBlockId=null){
		$arResult = [];
		$strField = $this->get('source');
		if($strOfferField = $this->isOfferField($strField)){
			$strField = $strOfferField;
		}
		$strHtmlType = null;
		$arField = [];
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($this->isField($strField)){
			$arFeatures = [
				'FIELDS' => [$strField],
			];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$arResult = $this->formatValueField($strField, $arElement);
			if(in_array($strField, ['PREVIEW_TEXT', 'DETAIL_TEXT'])){
				$strHtmlType = $arElement[$strField.'_TYPE'];
			}
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arFeatures = [
				'PROPERTY_ID' => [$intPropertyId],
				'EMPTY_PROPERTIES' => true,
			];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			$arProperty = array_merge($arElement['PROPERTIES'][$intPropertyId], [
				'ELEMENT_ID' => $intElementId,
				'TYPE_FULL' => $arProperty['PROPERTY_TYPE'].
					(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : ''),
			]);
			$arField = $arProperty;
			$arResult = $this->formatValueProperty($arProperty);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arFeatures = ['SEO' => true];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			$arResult[] = $arElement['SEO'][$strSeoField];
		}
		elseif($intPriceId = $this->isPriceExt($strField, $strSuffix)){
			$arField = null;
			$fPrice = null;
			$strCurrency = null;
			#
			$arFeatures = ['PRICES' => [$intPriceId]];
			$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
			#
			if(!strlen($strSuffix)){
				if(is_array($arElement['PRICES'][$intPriceId])){
					$arField = $arElement['PRICES'][$intPriceId];
					$fPrice = floatVal($arElement['PRICES'][$intPriceId]['PRICE']);
					$fPrice = Helper::interpretPrice($fPrice, $this->get('optimal_price_delta'));
					$strCurrency = $arElement['PRICES'][$intPriceId]['CURRENCY'];
				}
			}
			elseif($strSuffix == 'CURRENCY'){
				if(is_array($arElement['PRICES'][$intPriceId])){
					$arResult[] = $arElement['PRICES'][$intPriceId]['CURRENCY'];
				}
			}
			else{
				$strSiteId = $this->get('optimal_price_site_id');
				$arPrice = $arElement['PRICES'][$intPriceId];
				$strCurrency = $arPrice['CURRENCY'];
				$arPrices = [$arPrice];
				$arOptimalPrice = Helper::getOptimalPrice($intElementId, $strSiteId, $arPrices, $strCurrency);
				switch($strSuffix){
					case 'WITH_DISCOUNT':
						$fPrice = $arOptimalPrice['DISCOUNT_PRICE'];
						$fPrice = Helper::interpretPrice($fPrice, $this->get('optimal_price_delta'));
						break;
					case 'WITH_DISCOUNT__CURR':
						$arResult[] = $this->formatCurrency($arOptimalPrice['DISCOUNT_PRICE'], $strCurrency);
						break;
					case 'DISCOUNT':
						$fPrice = $arOptimalPrice['DISCOUNT'];
						$fPrice = Helper::interpretPrice($fPrice, $this->get('optimal_price_delta'));
						break;
					case 'DISCOUNT__CURR':
						$arResult[] = $this->formatCurrency($arOptimalPrice['DISCOUNT'], $strCurrency);
						break;
					case 'PERCENT':
						$fPercent = $arOptimalPrice['PERCENT'];
						$fPercent = Helper::interpretPrice($fPercent, $this->get('optimal_price_delta'));
						$arResult[] = $fPercent;
						break;
					case 'PERCENT__SIGN':
						$arResult[] = floatVal($arOptimalPrice['PERCENT']).'%';
						break;
					case 'PERCENT__CURR':
						$arResult[] = $this->formatCurrency($arOptimalPrice['PERCENT'], $strCurrency);
						break;
					case 'CURR':
						$arResult[] = $this->formatCurrency($arOptimalPrice['BASE_PRICE'], $strCurrency);
						break;
				}
			}
			if($fPrice){
				$arValue = [
					'FIELD' => $strField,
					'FIELD_ARRAY' => $arField,
					'VALUE' => $fPrice,
					'CURRENCY' => $strCurrency,
				];
				$arResult[] = new ValueItem($arValue);
			}
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			if($strCatalogField == 'MEASURE_RATIO'){
				$ratio = 1;
				$arQuery = ['filter' => ['PRODUCT_ID' => $intElementId], 'select' => ['RATIO']];
				if($arMeasure = \Bitrix\Catalog\MeasureRatioTable::getList($arQuery)->fetch()){
					$ratio = $arMeasure['RATIO'];
				}
				$arValue = [
					'FIELD' => $strField,
					'FIELD_ARRAY' => [],
					'VALUE' => $ratio,
				];
				$arResult[] = new ValueItem($arValue);
			}
			else{
				if(in_array($strCatalogField, ['BARCODE', 'BARCODE_MULTI'])){
					$arFeatures = ['BARCODE' => true];
				}
				elseif($intStoreId = $this->isStoreAmount($strField)){
					$arFeatures = ['STORES' => [$intStoreId]];
				}
				else{
					$arFeatures = ['CATALOG' => true];
				}
				$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
				$arResult = $this->getValueCatalogField($strCatalogField, $arElement);
			}
		}
		# Set empty value if source value not found
		if(empty($arResult)){
			$arResult[] = new ValueItem([]);
		}
		# Transform result array using `Item` class
		$arResultTmp = [];
		foreach($arResult as $key => $value){
			if(is_object($value)){
				$arResultTmp[] = $value;
			}
			else{
				$arValue = [
					'FIELD' => $strField,
					'FIELD_ARRAY' => $arProperty,
					'VALUE' => $value,
					'DESCRIPTION' => $arProperty['VALUE_DESCRIPTION'][$key],
				];
				if(is_array($arProperty['VALUE_HTML_TYPE'])){
					$arValue['HTML_TYPE'] = $arProperty['VALUE_HTML_TYPE'][$key];
				}
				elseif($strHtmlType){
					$arValue['HTML_TYPE'] = $strHtmlType;
				}
				if($arProperty['TYPE_FULL'] == 'S:HTML' && is_array($value)){
					$arValue['VALUE'] = toUpper($value['TYPE']) == 'HTML' ? html_entity_decode($value['TEXT']) : $value['TEXT'];
					$arValue['HTML_TYPE'] = $value['TYPE'];
				}
				$arResultTmp[] = new ValueItem($arValue);
			}
		}
		return $arResultTmp;
	}
	
	/**
	 *	Format values (eg, for S:directory: brand123 => 'Samsung')
	 */
	protected function formatValueField($strField, $arElement){
		$arResult = [];
		#
		$arValue = $arElement['~'.$strField];
		$arValue = is_array($arValue) ? $arValue : (!is_null($arValue) && strlen($arValue) ? [$arValue] : []);
		$arPropertyFake = [];
		$arPropertyFake['VALUE_ARRAY'] = $arValue;
		#
		if($strField == 'ACTIVE'){
			$arResult = $this->formatValue_S_checkbox($arPropertyFake);
		}
		elseif(in_array($strField, ['PREVIEW_TEXT', 'DETAIL_TEXT'])){
			$strValue = $arElement['~'.$strField];
			switch($this->get('source_s_html')){
				case 'html_to_text':
					$strValue = \HTMLToTxt($strValue, '', [], false);
					break;
				case 'strip_tags':
					$strValue = strip_tags($strValue);
					break;
				default:
					# Nothing
					break;
			}
			$arResult[] = $strValue;
		}
		elseif(in_array($strField, ['PREVIEW_PICTURE', 'DETAIL_PICTURE'])){
			if(is_array($arElement[$strField])){
				$arPropertyFake['VALUE_ARRAY'] = [$arElement[$strField]];
				$arResult = $this->formatValue_F($arPropertyFake);
			}
		}
		elseif(in_array($strField, ['CREATED_BY', 'MODIFIED_BY'])){
			$arResult = $this->formatValue_N_userid($arPropertyFake);
		}
		elseif($strField == 'TAGS'){
			$strTags = html_entity_decode($arElement['~'.$strField]);
			if($this->get('tags_multiple') == 'Y'){
				$arTags = preg_split('#,\s*#', $strTags);
				$arResult = array_unique($arTags);
			}
			else{
				$arResult[] = $strTags;
			}
		}
		elseif($strField == 'DETAIL_PAGE_URL'){
			$arResult[] = $this->formatUrl($arElement[$strField]);
		}
		else{
			$mValue = $arElement['~'.$strField];
			if(is_array($mValue) && !empty($mValue) || is_string($mValue) && strlen($mValue)){
				$arResult[] = $mValue;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Format values (eg, for S:directory: brand123 => 'Samsung')
	 */
	protected function formatValueProperty(&$arProperty){
		$arResult = [];
		#
		$arValue = $arProperty['VALUE'];
		#
		if($arProperty['TYPE_FULL'] == 'S:HTML'){
			$bIsHtmlValue = is_array($arValue) && isset($arValue['TEXT']) && isset($arValue['TYPE']);
			$arValue = (is_array($arValue) && !$bIsHtmlValue) ? $arValue : 
				(!is_null($arValue) && is_array($arValue) ? [$arValue] : []);
		}
		else{
			$arValue = is_array($arValue) ? $arValue : 
				(!is_null($arValue) && strlen($arValue) ? [$arValue] : []);
		}
		$arProperty['VALUE_ARRAY'] = $arValue;
		#
		$arDescr = $arProperty['DESCRIPTION'];
		$arDescr = is_array($arDescr) ? $arDescr : (!is_null($arDescr) && strlen($arDescr) ? [$arDescr] : []);
		$arProperty['VALUE_DESCRIPTION'] =  $arDescr;
		#
		$strType = $arProperty['PROPERTY_TYPE'];
		$strTypeFull = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
		if($strTypeFull == 'S:directory'){
			$arResult = $this->formatValue_S_directory($arProperty);
		}
		elseif($strType == 'L'){
			$arResult = $this->formatValue_L($arProperty);
		}
		elseif($strType == 'E'){
			$arResult = $this->formatValue_E($arProperty);
		}
		elseif($strType == 'G'){
			$arResult = $this->formatValue_G($arProperty);
		}
		elseif($strType == 'F'){
			$arResult = $this->formatValue_F($arProperty);
		}
		elseif($strTypeFull == 'S:_Checkbox'){
			$arResult = $this->formatValue_S_checkbox($arProperty);
		}
		elseif($strTypeFull == 'S:Money'){
			$arResult = $this->formatValue_S_money($arProperty);
		}
		elseif($strTypeFull == 'S:UserID'){
			$arResult = $this->formatValue_S_userid($arProperty);
		}
		elseif($strType == 'S'){
			$arResult = is_array($arProperty['VALUE_ARRAY']) ? $arProperty['VALUE_ARRAY'] : 
				(strlen($arProperty['VALUE_ARRAY']) ? [$arProperty['VALUE_ARRAY']] : []);
		}
		else{
			$arResult = $arProperty['VALUE_ARRAY'];
		}
		return $arResult;
	}
	
	/**
	 *	Format values for S:directory
	 */
	protected function formatValue_S_directory($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$strEntityDataClass = IBlock::getHighloadClass($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']);
			if(strlen($strEntityDataClass)){
				$arMap = [
					'title' => 'UF_NAME',
					'id' => 'ID',
					'value' => 'UF_XML_ID',
				];
				$strKey = $arMap[$this->get('source_s_directory')];
				$arSort = [$strKey => 'ASC'];
				$arFilter = ['UF_XML_ID' => $arProperty['VALUE_ARRAY']];
				$arSelect = ['UF_XML_ID', $strKey];
				$resSata = $strEntityDataClass::getlist([
					'order' =>  $arSort,
					'filter' => $arFilter,
					'select' => $arSelect,
				]);
				/*
				$arProperty['VALUE_ARRAY'] = [];
				while($arItem = $resSata->fetch()) {
					$arProperty['VALUE_ARRAY'][] = $arItem[$strKey];
				}
				*/
				while($arItem = $resSata->fetch()) {
					foreach($arProperty['VALUE_ARRAY'] as $key => $value){
						if($value == $arItem['UF_XML_ID']){
							$arProperty['VALUE_ARRAY'][$key] = $arItem[$strKey];
						}
					}
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:_Checkbox
	 */
	protected function formatValue_S_checkbox($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){ // $value = Y, N, or D
				switch($this->get('format_s_checkbox')){
					case 'yes_no':
						$arProperty['VALUE_ARRAY'][$key] = Helper::getMessage('WDA_'.$value);
						break;
					case 'y_n':
						$arProperty['VALUE_ARRAY'][$key] = $value;
						break;
					case '1_0':
						$arProperty['VALUE_ARRAY'][$key] = $value == 'Y' ? '1' : '0';
						break;
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:Money
	 */
	protected function formatValue_S_money($arProperty){
		$arResult = [];
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){
				$arPrice = explode('|', $value);
				switch($this->get('source_s_money')){
					case 'formatted':
						$arResult[] = Helper::currencyFormat($arPrice[0], $arPrice[1]);
						break;
					case 'price_currency':
						$arResult[] = $value;
						break;
					case 'number':
						$arResult[] = $arPrice[0];
						break;
					default:
						$arValue = [
							'FIELD' => 'PROPERTY_'.$arProperty['ID'],
							'FIELD_ARRAY' => $arProperty,
							'VALUE' => $arPrice[0],
							'CURRENCY' => $arPrice[1],
						];
						$arResult[] = new ValueItem($arValue);
						break;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Format values for S:_CheckboxD
	 */
	protected function formatValue_S_checkboxD($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			foreach($arProperty['VALUE_ARRAY'] as $key => $value){ // $value = Y, N, or D
				switch($this->get('format_s_checkboxd')){
					case 'yes_no':
						$arProperty['VALUE_ARRAY'][$key] = Helper::getMessage('WDA_'.$value);
						break;
					case 'y_n':
						$arProperty['VALUE_ARRAY'][$key] = $value;
						break;
					case '1_0':
						$arProperty['VALUE_ARRAY'][$key] = $value == 'Y' ? '1' : ($value == 'N' ? '0' : '-');
						break;
				}
			}
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for L
	 */
	protected function formatValue_L($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arMap = [
				'value' => 'VALUE_ENUM',
				'xml_id' => 'VALUE_XML_ID',
				'enum_id' => 'VALUE_ENUM_ID',
			];
			$strKey = $arMap[$this->get('source_l')];
			$arResult = $arProperty[$strKey];
			if(!is_array($arResult)){
				$arResult = strlen($arResult) ? [$arResult] : [];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for E
	 */
	protected function formatValue_E($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			$arFilter = [
				'ID' => $arProperty['VALUE_ARRAY'],
			];
			if($arProperty['LINK_IBLOCK_ID']){
				$arFilter['IBLOCK_ID'] = $arProperty['LINK_IBLOCK_ID'];
			}
			$arMap = [
				'title' => 'NAME',
				'code' => 'CODE',
				'xml_id' => 'XML_ID',
				'id' => 'ID',
			];
			$strKey = $arMap[$this->get('source_e')];
			$arSort = [$strKey => 'ASC'];
			$arSelect = ['ID', $strKey];
			$resElements = \CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
			while($arElement = $resElements->getNext()){
				$arResult[] = $arElement[$strKey];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for G
	 */
	protected function formatValue_G($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			$arFilter = [
				'ID' => $arProperty['VALUE_ARRAY'],
			];
			if($arProperty['LINK_IBLOCK_ID']){
				$arFilter['IBLOCK_ID'] = $arProperty['LINK_IBLOCK_ID'];
			}
			$arMap = [
				'title' => 'NAME',
				'code' => 'CODE',
				'xml_id' => 'XML_ID',
				'id' => 'ID',
			];
			$strKey = $arMap[$this->get('source_g')];
			$arSort = [$strKey => 'ASC'];
			$arSelect = ['ID', $strKey];
			$resSections = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
			while($arSection = $resSections->getNext()){
				$arResult[] = $arSection[$strKey];
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for F
	 */
	protected function formatValue_F($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arProperty['VALUE_DESCRIPTION'] = is_array($arProperty['VALUE_DESCRIPTION']) ? $arProperty['VALUE_DESCRIPTION'] : [];
			foreach($arProperty['VALUE_ARRAY'] as $key => $arFile){
				if(is_numeric($arFile)){
					$arFile = \CFile::getFileArray($arFile);
				}
				if(is_array($arFile)){
					switch($this->get('source_f')){
						case 'src_rel':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['SRC'];
							break;
						case 'src_abs':
							$arProperty['VALUE_ARRAY'][$key] = Helper::root().$arFile['SRC'];
							break;
						case 'src_url':
							$arProperty['VALUE_ARRAY'][$key] = $this->formatUrl($arFile['SRC']);
							break;
						case 'description':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['DESCRIPTION'];
							break;
						case 'id':
							$arProperty['VALUE_ARRAY'][$key] = $arFile['ID'];
							break;
					}
					$arProperty['VALUE_DESCRIPTION'][$key] = $arFile['DESCRIPTION'];
				}
			}
		}
		$arResult = [];
		foreach($arProperty['VALUE_ARRAY'] as $key => $value){
			$arValue = [
				'FIELD' => $strField,
				'FIELD_ARRAY' => $arProperty,
				'VALUE' => $value,
				'DESCRIPTION' => $arProperty['VALUE_DESCRIPTION'][$key],
			];
			$arResult[] = new ValueItem($arValue);
		}
		return $arResult;
	}
	
	/**
	 *	Format values for N:_UserId
	 */
	protected function formatValue_N_userid($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			foreach($arProperty['VALUE_ARRAY'] as $intUserId){
				if(is_numeric($intUserId) && $intUserId > 0){
					$strUser = $this->formatUserName($intUserId, $this->get('source_n_userid'));
					if(strlen($strUser)){
						$arResult[] = $strUser;
					}
				}
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	Format values for S:UserID
	 */
	protected function formatValue_S_userid($arProperty){
		if(!empty($arProperty['VALUE_ARRAY'])){
			$arResult = [];
			foreach($arProperty['VALUE_ARRAY'] as $intUserId){
				if(is_numeric($intUserId) && $intUserId > 0){
					$strUser = $this->formatUserName($intUserId, $this->get('source_s_userid'));
					if(strlen($strUser)){
						$arResult[] = $strUser;
					}
				}
			}
			return $arResult;
		}
		return $arProperty['VALUE_ARRAY'];
	}
	
	/**
	 *	
	 */
	protected function formatUserName($intUserId, $strFormat){
		$strResult = false;
		$arSelect = ['ID', 'NAME', 'LAST_NAME', 'LOGIN', 'EMAIL'];
		$resUser = \CUser::getList($by='ID', $order='ASC', ['ID' => $intUserId], ['FIELDS' => $arSelect]);
		if($arUser = $resUser->getNext()){
			switch($strFormat){
				case 'default':
					$strResult = sprintf('[%d] (%s) %s', $arUser['ID'], $arUser['LOGIN'], $arUser['NAME']);
					break;
				case 'name_lastname':
					$strResult = sprintf('%s %s', $arUser['NAME'], $arUser['LAST NAME']);
					break;
				case 'name':
					$strResult = $arUser['NAME'];
					break;
				case 'login':
					$strResult = $arUser['LOGIN'];
					break;
				case 'email':
					$strResult = $arUser['EMAIL'];
					break;
				case 'id':
					$strResult = $arUser['ID'];
					break;
			}
		}
		return $strResult;
	}
	
	/**
	 *	Format values for catalog fields
	 */
	protected function getValueCatalogField($strCatalogField, $arElement){
		$arResult = [];
		$strField = 'CATALOG_'.$strCatalogField;
		$arCatalogFieldsFromElement = [
			'QUANTITY',
			'QUANTITY_RESERVED',
			'WEIGHT',
			'WIDTH',
			'HEIGHT',
			'LENGTH',
			'PURCHASING_PRICE',
			'PURCHASING_CURRENCY',
		];
		if(in_array($strCatalogField, $arCatalogFieldsFromElement)){
			$arResult[] = $arElement[$strField];
		}
		elseif($strCatalogField == 'AVAILABLE'){
			$arPropertyFake = [];
			$arPropertyFake['VALUE_ARRAY'] = strlen($arElement[$strField]) ? [$arElement[$strField]] : [];
			$arResult = $this->formatValue_S_checkbox($arPropertyFake);
		}
		elseif($strCatalogField == 'VAT_ID'){
			$arVatList = Helper::getVatList();
			if($arElement[$strField]){
				$arVat = $arVatList[$arElement[$strField]];
				if(is_numeric($arVat['RATE']) && $arVat['RATE'] > 0){
					switch($this->get('source_n_vat')){
						case 'text':
							$arResult[] = $arVat['NAME'];
							break;
						case 'number':
							$arResult[] = floatVal($arVat['RATE']);
							break;
						case 'id':
							$arResult[] = $arVat['ID'];
							break;
					}
				}
				else{
					$arResult[] = $arVat['NAME'];
				}
			}
		}
		elseif($strCatalogField == 'VAT_INCLUDED'){
			$arPropertyFake = [];
			$arPropertyFake['VALUE_ARRAY'] = strlen($arElement[$strField]) ? [$arElement[$strField]] : [];
			$arResult = $this->formatValue_S_checkbox($arPropertyFake);
		}
		elseif($strCatalogField == 'MEASURE_ID'){
			$intMeasure = $arElement['CATALOG_MEASURE'];
			if($intMeasure){
				$arMeasures = Helper::getMeasuresList();
				$arMeasure = $arMeasures[$intMeasure];
				switch($this->get('source_n_measure')){
					case 'title':
						$arResult[] = $arMeasure['MEASURE_TITLE'];
						break;
					case 'symbol_rus':
						$arResult[] = $arMeasure['SYMBOL_RUS'];
						break;
					case 'symbol_int':
						$arResult[] = $arMeasure['SYMBOL_INTL'];
						break;
					case 'code_letter':
						$arResult[] = $arMeasure['SYMBOL_LETTER_INTL'];
						break;
					case 'code':
						$arResult[] = $arMeasure['CODE'];
						break;
					case 'id':
						$arResult[] = $arMeasure['ID'];
						break;
				}
			}
		}
		elseif(in_array($strCatalogField, ['QUANTITY_TRACE', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE'])){
			$arPropertyFake = [];
			$strValue = $arElement[$strField.'_ORIG'];
			if(is_null($strValue)){
				$strValue = $arElement[$strField];
			}
			$arPropertyFake['VALUE_ARRAY'] = strlen($strValue) ? [$strValue] : [];
			$arResult = $this->formatValue_S_checkboxD($arPropertyFake);
		}
		elseif(in_array($strCatalogField, ['BARCODE_MULTI'])){
			$arPropertyFake = [];
			$arPropertyFake['VALUE_ARRAY'] = strlen($arElement[$strField]) ? [$arElement[$strField]] : [];
			$arResult = $this->formatValue_S_checkbox($arPropertyFake);
		}
		elseif($strCatalogField == 'BARCODE'){
			$arResult = is_array($arElement[$strField]) ? $arElement[$strField] : 
				(strlen($arElement[$strField]) ? [$arElement[$strField]] : []);
		}
		elseif($strCatalogField == 'PRODUCT_GROUP'){
			$arValue = IBlock::getElementProductGroup($arElement['ID']);
			$arValue['FIELD'] = $strField;
			$arValue['FIELD_ARRAY'] = $arValue;
			$arValue['VALUE'] = sprintf('%s [%d]', $arValue['PRODUCT_GROUP_NAME'], $arValue['PRODUCT_GROUP_ID']);
			$arResult[] = new ValueItem($arValue);
		}
		elseif($intStoreId = $this->isStoreAmount($strField)){
			$arResult[] = floatVal($arElement['STORES'][$intStoreId]['AMOUNT']);
		}
		else{
			$arResult = is_array($arElement[$strField]) ? $arElement[$strField] : 
				(strlen($arElement[$strField]) ? [$arElement[$strField]] : []);
		}
		return $arResult;
	}
	
	// *******************************************************************************************************************
	// Set methods
	// *******************************************************************************************************************
	
	/**
	 *	Process (save) single field
	 */
	protected function processField($intElementId, $arValues, $intIBlockId=null){
		$strField = $this->get('target');
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($strOfferField = $this->isOfferField($strField)){
			$strField = $strOfferField;
		}
		if(is_array($arValues) && empty($arValues)){
			$arValues = false;
		}
		if($this->isField($strField)){
			return $this->processElementField($intElementId, $strField, $arValues);
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			return $this->processElementProperty($intElementId, $intIBlockId, $intPropertyId, $arValues);
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			return $this->processElementSeoField($intElementId, $strSeoField, $arValues);
		}
		elseif($intPriceId = $this->isPrice($strField)){
			return $this->processElementPrice($intElementId, $intPriceId, $arValues);
		}
		elseif($intStoreId = $this->isStoreAmount($strField)){
			return $this->processElementStoreAmount($intElementId, $intStoreId, $arValues);
		}
		elseif($strCatalogField = $this->isCatalogField($strField)){
			return $this->processElementCatalogField($intElementId, $strCatalogField, $arValues);
		}
		$this->log(static::getMessage('LOG_FIELD_UNKNOWN', ['#FIELD#' => $strField]));
		return false;
	}
	
	
	/**
	 *	Process field
	 */
	protected function processElementField($intElementId, $strField, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, false, false);
		switch($strField){
			case 'NAME':
				$bResult = $this->update($intElementId, ['NAME' => $mSaveValue]);
				break;
			case 'DATE_CREATE':
				$bResult = $this->update($intElementId, ['DATE_CREATE' => $mSaveValue]);
				break;
			case 'ACTIVE':
				$bResult = $this->update($intElementId, ['ACTIVE' => ($mSaveValue == 'Y' ? 'Y' : 'N')]);
				break;
			case 'ACTIVE_FROM':
				$bResult = $this->update($intElementId, ['ACTIVE_FROM' => $mSaveValue]);
				break;
			case 'ACTIVE_TO':
				$bResult = $this->update($intElementId, ['ACTIVE_TO' => $mSaveValue]);
				break;
			case 'SORT':
				$bResult = $this->update($intElementId, ['SORT' => $mSaveValue]);
				break;
			case 'PREVIEW_TEXT':
			case 'DETAIL_TEXT':
				$arFields = [
					$strField => $mSaveValue,
				];
				$strHtmlType = $this->getValueHtmlType($mSaveValue, $this->mObjectValues);
				if($strHtmlType){
					$arFields[$strField.'_TYPE'] = $strHtmlType;
				}
				$bResult = $this->update($intElementId, $arFields);
				break;
			case 'PREVIEW_TEXT_TYPE':
			case 'DETAIL_TEXT_TYPE':
				$bResult = $this->update($intElementId, [$strField => $arValues]);
				break;
			case 'PREVIEW_PICTURE':
			case 'DETAIL_PICTURE':
				$arFile = $this->makeFileArray($intElementId, $mSaveValue);
				if(is_array($arFile)){
					$arFile['DESCRIPTION'] = $arFile['description'] = $this->mObjectValues->getDescription();
					$arSaveValue[] = $arFile;
					$bResult = $this->update($intElementId, [$strField => $arFile]);
				}
				else{
					$bResult = false;
				}
				break;
			case 'CODE':
				$bResult = $this->update($intElementId, ['CODE' => $mSaveValue]);
				break;
			case 'EXTERNAL_ID':
			case 'XML_ID':
				$bResult = $this->update($intElementId, ['EXTERNAL_ID' => $mSaveValue]);
				break;
			case 'TAGS':
				$bResult = $this->update($intElementId, ['TAGS' => $mSaveValue]);
				break;
			case 'CREATED_BY':
				$bResult = $this->update($intElementId, ['CREATED_BY' => $mSaveValue]);
				break;
			case 'MODIFIED_BY':
				$bResult = $this->update($intElementId, ['MODIFIED_BY' => $mSaveValue]);
				break;
		}
		return $bResult;
	}
	
	/**
	 *	Process property
	 */
	protected function processElementProperty($intElementId, $intIBlockId, $intPropertyId, $arValues){
		$arProperty = $this->getProperty($intPropertyId, true, $intIBlockId);
		if(!is_array($arProperty)){
			$this->log(static::getMessage('LOG_PROPERTY_NOT_FOUND', ['#IBLOCK_ID#' => $intIBlockId, 
				'#PROPERTY_ID#' => $intPropertyId]));
			return false;
		}
		switch($arProperty['PROPERTY_TYPE']){
			case 'S':
				return $this->processElementPropertyS($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
			case 'N':
				return $this->processElementPropertyN($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
			case 'L':
				return $this->processElementPropertyL($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
			case 'E':
				return $this->processElementPropertyE($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
			case 'G':
				return $this->processElementPropertyG($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
			case 'F':
				return $this->processElementPropertyF($intElementId, $intIBlockId, $arProperty, $arValues);
				break;
		}
		return false;
	}
	protected function processElementPropertyS($intElementId, $intIBlockId, $arProperty, $arValues){
		/*
		case 'Money':
		case 'HTML':
		case 'video':
		case 'Date':
		case 'DateTime':
		case 'map_yandex':
		case 'map_google':
		case 'UserID':
		case 'TopicID':
		case 'FileMan':
		*/
		switch($arProperty['USER_TYPE']){
			case 'directory':
				$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
					$this->get('multiple_mode'), false);
				$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
				$arSaveValue = [];
				$strTableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];
				# Set
				foreach($mSaveValue as $value){
					if(is_null($value) || !mb_strlen($value)){
						continue;
					}
					$arHighloadItem = IBlock::getHighloadItems($strTableName, [], ['UF_NAME' => $value]);
					if(is_array($arHighloadItem) && !empty($arHighloadItem)){
						$arSaveValue[] = key($arHighloadItem);
					}
					else{
						$strXmlId = \CUtil::translit($value, LANGUAGE_ID, [
							'max_len' => 255,
							'change_case' => 'L',
							'replace_space' => '_',
							'replace_other' => '_',
							'delete_repeat_replace' => true,
						]);
						$arItemFields = [
							'UF_NAME' => $value,
							'UF_XML_ID' => $strXmlId,
						];
						$strClass = IBlock::getHighloadClass($strTableName);
						$obResult = $strClass::add($arItemFields);
						if($obResult->isSuccess()){
							$arSaveValue[] = $strXmlId;
						}
					}
				}
				if(is_array($arSaveValue) && empty($arSaveValue)){
					$arSaveValue = false;
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
				break;
			case 'Money':
				$arSaveValue = [];
				foreach($arValues as $obValue){
					$strValue = sprintf('%s|%s', $obValue->getValue(), $obValue->get('CURRENCY'));
					$arSaveValue[] = $strValue;
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
				break;
			case 'HTML':
				$arSaveValue = [];
				foreach($arValues as $obValue){
					$strHtmlType = 
					$arValue = [
						'VALUE' => [
							'TEXT' => $obValue->getValue(),
							'TYPE' => $this->getValueHtmlType($obValue->getValue(), $obValue),
						],
					];
					if(strlen($obValue->getDescription())){
						$arValue['DESCRIPTION'] = $obValue->getDescription();
					}
					$arSaveValue[] = $arValue;
				}
				$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
				break;
			default:
				$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y', 
					$this->get('multiple_mode'), true);
				$this->setPropertyValue($intElementId, $arProperty['ID'], $mSaveValue, null, $intIBlockId);
				break;
		}
		return true;
	}
	protected function processElementPropertyN($intElementId, $intIBlockId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'), true);
		$this->setPropertyValue($intElementId, $arProperty['ID'], $mSaveValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyL($intElementId, $intIBlockId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y',
			$this->get('multiple_mode'), false);
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		foreach($mSaveValue as $value){
			if(is_null($value) || !mb_strlen($value)){
				continue;
			}
			$arFilter = ['PROPERTY_ID' => $arProperty['ID'], 'VALUE' => $value];
			if($arEnum = \CIBlockPropertyEnum::getList([], $arFilter)->getNext()){
				$arSaveValue[] = $arEnum['ID'];
			}
			else{
				$arEnumFields = [
					'PROPERTY_ID' => $arProperty['ID'],
					'VALUE' => $value,
				];
				$obEnum = new \CIBlockPropertyEnum;
				if($intEnumId = $obEnum->add($arEnumFields)){
					$arSaveValue[] = $intEnumId;
				}
			}
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyE($intElementId, $intIBlockId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y', 
			$this->get('multiple_mode'), false);
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		# Check LINK_IBLOCK_ID
		if(!$arProperty['LINK_IBLOCK_ID']){
			$this->setError(static::getMessage('ERROR_NO_LINK_IBLOCK_ID', ['#PROPERTY_ID#' => $arProperty['ID']]));
			return false;
		}
		# Set
		if($this->get('copy_raw_element_id') == 'Y'){
			$arSaveValue = $mSaveValue;
		}
		else{
			foreach($mSaveValue as $value){
				if(is_null($value) || !mb_strlen($value)){
					continue;
				}
				$arFilter = ['IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], '=NAME' => $value];
				if($arElement = \CIBlockElement::getList([], $arFilter, false, ['nTopCount' => 1])->getNext()){
					$arSaveValue[] = $arElement['ID'];
				}
				else{
					$arElementFields = [
						'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
						'NAME' => $value,
						'CODE' => \CUtil::translit($value, LANGUAGE_ID, [
							'max_len' => 255,
							'change_case' => 'L',
							'replace_space' => '_',
							'replace_other' => '_',
							'delete_repeat_replace' => true,
						]),
					];
					$obElement = new \CIBlockElement;
					if($intNewElementId = $obElement->add($arElementFields)){
						$arSaveValue[] = $intNewElementId;
					}
					else{
						$this->setError($obElement->LAST_ERROR);
						return false;
					}
				}
			}
		}
		#
		if(is_array($arSaveValue) && empty($arSaveValue)){
			$arSaveValue = false;
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyG($intElementId, $intIBlockId, $arProperty, $arValues){
		$mSaveValue = $this->transformObjectToValue($arValues, $arProperty['MULTIPLE'] == 'Y', 
			$this->get('multiple_mode'), false);
		$mSaveValue = is_array($mSaveValue) ? $mSaveValue : (strlen($mSaveValue) ? [$mSaveValue] : []);
		$arSaveValue = [];
		# Check LINK_IBLOCK_ID
		if(!$arProperty['LINK_IBLOCK_ID']){
			$this->setError(static::getMessage('ERROR_NO_LINK_IBLOCK_ID', ['#PROPERTY_ID#' => $arProperty['ID']]));
			return false;
		}
		# Set
		foreach($mSaveValue as $value){
			if(is_null($value) || !mb_strlen($value)){
				continue;
			}
			$arFilter = ['IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], '=NAME' => $value];
			if($arElement = \CIBlockSection::getList([], $arFilter, false, ['nTopCount' => 1])->getNext()){
				$arSaveValue[] = $arElement['ID'];
			}
			else{
				$arElementFields = [
					'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
					'NAME' => $value,
					'CODE' => \CUtil::translit($value, LANGUAGE_ID, [
						'max_len' => 255,
						'change_case' => 'L',
						'replace_space' => '_',
						'replace_other' => '_',
						'delete_repeat_replace' => true,
					]),
				];
				$obSection = new \CIBlockSection;
				if($intNewSectionId = $obSection->add($arElementFields)){
					$arSaveValue[] = $intNewSectionId;
				}
				else{
					$this->setError($obSection->LAST_ERROR);
					return false;
				}
			}
		}
		#
		if(is_array($arSaveValue) && empty($arSaveValue)){
			$arSaveValue = false;
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
		return true;
	}
	protected function processElementPropertyF($intElementId, $intIBlockId, $arProperty, $arValues){
		$arSaveValue = [];
		if($arProperty['MULTIPLE'] != 'Y'){
			$obValue = $this->cutMultipleValue($arValues, $this->get('multiple_mode'));
			$arValues = [];
			if($obValue){
				$arValues[] = $obValue;
			}
		}
		foreach($arValues as $obValue){
			$arFile = $this->makeFileArray($intElementId, $obValue->getValue());
			if(is_array($arFile)){
				$arFile['DESCRIPTION'] = $arFile['description'] = $obValue->getDescription();
				$arSaveValue[] = $arFile;
			}
		}
		$this->setPropertyValue($intElementId, $arProperty['ID'], $arSaveValue, null, $intIBlockId);
		return true;
	}
		
	/**
	 *	Process seo-field
	 */
	protected function processElementSeoField($intElementId, $strSeoField, $arValues){
		$obValue = $this->cutMultipleValue($arValues, $this->get('multiple_mode'));
		$strSeoFieldFull = IBlock::$arSeoMapElement[$strSeoField];
		$arFields = [
			'IPROPERTY_TEMPLATES' => [
				$strSeoFieldFull => $obValue->getValue(),
			],
		];
		$bResult = $this->update($intElementId, $arFields, false, false, false);
		return $bResult;
	}
	
	/**
	 *	Process price
	 */
	protected function processElementPrice($intElementId, $intPriceId, $arValues){
		$arSuccess = [];
		foreach($arValues as $key => $obValue){
			$fPrice = $obValue->getValue();
			$strCurrency = $obValue->get('CURRENCY');
			if(!strlen($strCurrency)){
				$strCurrency = Helper::getBaseCurrency();
			}
			$arSuccess[] = Helper::setProductPrice($intElementId, $intPriceId, $fPrice, $strCurrency);
		}
		return count(array_unique($arSuccess)) == 1 && $arSuccess[0] === true;
	}
	
	/**
	 *	Process store amount
	 */
	protected function processElementStoreAmount($intElementId, $intStoreId, $arValues){
		$arSuccess = [];
		foreach($arValues as $key => $obValue){
			$fAmount = $obValue->getValue();
			$arSuccess[] = Helper::setProductStoreAmount($intElementId, $intStoreId, $fAmount);
		}
		return count(array_unique($arSuccess)) == 1 && $arSuccess[0] === true;
	}
	
	/**
	 *	Process catalog field
	 */
	protected function processElementCatalogField($intElementId, $strField, $arValues){
		// Multiple values
		if($strField == 'BARCODE'){
			$arValues = $this->transformObjectToValue($arValues, true);
			return Helper::setProductBarcodes($intElementId, $arValues, $this->isSaveValues());
		}
		// Single value
		else{
			$obValue = $this->cutMultipleValue($arValues, $this->get('multiple_mode'));
			if($strField == 'PURCHASING_PRICE'){ // see 2nd argument for Helper::setProductField()
				$strField = [
					'PURCHASING_PRICE' => $obValue->getValue(),
				];
				if(strlen($obValue->get('CURRENCY'))){
					$strField['PURCHASING_CURRENCY'] = $obValue->get('CURRENCY');
				}
				else{
					$strField['PURCHASING_CURRENCY'] = Helper::getBaseCurrency();
				}
				$arValues = null;
			}
			elseif($strField == 'MEASURE_RATIO'){
				return Helper::setProductMeasureRatio($intElementId, $obValue->getValue());
			}
			elseif($strField == 'PRODUCT_GROUP'){
				$mSaveValue = null;
				$arGroups = IBlock::getProductGroups();
				$arGroups = array_map(function($strGroup){
					return toLower($strGroup);
				}, $arGroups);
				$strValue = $obValue->getValue();
				if(is_numeric($strValue) && is_array($arGroups[$strValue])){
					$mSaveValue = $strValue;
				}
				elseif($intKey = array_search(toLower($strValue), $arGroups)){
					$mSaveValue = $intKey;
				}
				else{
					foreach($arGroups as $intGroupId => $strGroup){
						if(strpos($strGroup, $strValue) === 0){
							$mSaveValue = $intGroupId;
						}
					}
				}
				Helper::setProductField($intElementId, 'UF_PRODUCT_GROUP', $mSaveValue);
			}
			else{
				if($strField == 'MEASURE_ID'){
					$strField = 'MEASURE';
				}
				elseif($strField == 'NEGATIVE_AMOUNT_TRACE'){ // Bitrix bugfix, 2020-04-01
					$strField = [
						'NEGATIVE_AMOUNT_TRACE' => $obValue->getValue(),
						'CAN_BUY_ZERO' => $obValue->getValue(),
					];
					$arValues = null;
				}
			}
			return Helper::setProductField($intElementId, $strField, $obValue->getValue());
		}
		return false;
	}
	
	// *******************************************************************************************************************
	// Other methods
	// *******************************************************************************************************************
	
	/**
	 *	Get text format
	 */
	protected function getValueHtmlType($strValue, $obValue){
		$strType = false;
		$strHtmlType = toLower($this->get('html_type'));
		if($strHtmlType == 'auto') {
			if(is_object($obValue) && $obValue->get('HTML_TYPE')){
				if(in_array(toLower($obValue->get('HTML_TYPE')), ['text', 'html'])){
					$strType = toLower($obValue->get('HTML_TYPE'));
				}
			}
			elseif(is_string($strValue)){
				if(preg_match('#<[a-z]+[^>]*>#', $strValue)){
					$strType = 'html';
				}
			}
		}
		else{
			if(in_array($strHtmlType, ['text', 'html'])){
				$strType = $strHtmlType;
			}
		}
		return $strType;
	}
	
	/**
	 *	Make file array for $strValue
	 */
	protected function makeFileArray($intElementId, $strValue){
		$mResult = false;
		if(is_numeric($strValue) && $strValue > 0){
			$mResult = \CFile::makeFileArray($strValue);
		}
		elseif(strlen($strValue)){
			if(is_file(Helper::root().$strValue)){
				$mResult = \CFile::makeFileArray(Helper::root().$strValue);
			}
			elseif(is_file($strValue)){
				$mResult = \CFile::makeFileArray($strValue);
			}
			elseif(preg_match('#^http[s]?://.*?$#', $strValue)){
				$mResult = \CFile::makeFileArray($strValue);
			}
		}
		if(is_array($mResult) && $this->get('file_convert_name') == 'Y'){
			$arFeatures = ['FIELDS' => ['NAME']];
			$arElement = IBlock::getElementArray($intElementId, false /*$this->intIBlockId*/, $arFeatures);
			$strFilename = \CUtil::translit($arElement['~NAME'], LANGUAGE_ID, [
				'max_len' => 255,
				'change_case' => 'L',
				'replace_space' => '_',
				'replace_other' => '_',
				'delete_repeat_replace' => true,
			]);
			$strExtension = pathinfo($mResult['name'], PATHINFO_EXTENSION);
			$strFilename .= '.'.$strExtension;
			$mResult['name'] = $strFilename;
		}
		return $mResult;
	}
	
	/**
	 *	Is checkbox 'save values' checked?
	 */
	protected function isSaveValues(){
		return $this->get(static::SAVE_CURRENT_VALUES) == 'Y';
	}
	
	/**
	 *	Is checkbox 'remove duplicates' checked?
	 */
	protected function isRemoveDuplicates(){
		return $this->get(static::REMOVE_DUPLICATES) == 'Y';
	}
	
	/**
	 *	Is skip null values
	 */
	protected function isSkipNullValues(){
		return $this->get(static::SKIP_NULL_VALUES) == 'Y';
	}
	
	/**
	 *	Format currency
	 *	Its very monstrously because we need to trim insignificant zeros in admin section
	 */
	protected function formatCurrency($fPrice, $strCurrency){
		if(\Bitrix\Main\Loader::includeModule('currency')){
			$mCallback = [__NAMESPACE__.'\CopyValuesCurrencyLang', 'CurrencyFormat'];
			return call_user_func($mCallback, $fPrice, $strCurrency, true);
		}
		else{
			return $fPrice.' '.$strCurrency;
		}
	}
	
	/**
	 *	Format URL
	 */
	protected function formatUrl($strRelativeUrl){
		$strDomain = $this->get('site_domain');
		$strDomain = preg_replace('#^(.*?)[/]*$#', '$1', $strDomain);
		return $strDomain.$strRelativeUrl;
	}
	
}

?>