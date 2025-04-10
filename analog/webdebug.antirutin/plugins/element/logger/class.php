<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class Logger extends PluginElement {
	
	const CSV_SEPARATOR = ';';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['!CODE' => ['WF_%', 'SHOW_HISTORY', 'SHOW_NEW']],
		'PROPERTIES' => [],
		'SEO' => [],
		'CATALOG' => ['!CODE' => ['OFFERS', 'VAT_ID', 'MEASURE_ID', 'BARCODE_MULTI']],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
		'STORES' => [],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	public function __construct(){
		parent::__construct();
		$this->arCustomFields = array_merge($this->arCustomFields, [
			'WD_1C_CODE' => [
				'NAME' => static::getMessage('FIELD_WD_1C_CODE'),
				'TYPE' => 'S',
			],
			'SECTION_NAME' => [
				'NAME' => static::getMessage('FIELD_SECTION_NAME'),
				'TYPE' => 'S',
			],
			'FILES_SIZE' => [
				'NAME' => static::getMessage('FIELD_FILES_SIZE'),
				'TYPE' => 'N',
			],
		]);
	}
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if(!strlen(trim($this->get('filename')))){
			$this->setError(static::getMessage('ERROR_NO_FILENAME'));
			return false;
		}
		elseif($this->get('format') == 'CSV' && !strlen($this->get('fields'))){
			$this->setError(static::getMessage('ERROR_NO_CVS_FIELDS'));
			return false;
		}
		$strFormat = $this->get('format');
		$strFilename = $this->getFilename();
		$strFilenameAbs = $_SERVER['DOCUMENT_ROOT'].$strFilename;
		$arFields = $this->getValuesArray($this->get('fields'));
		$arFieldsOriginal = $arFields;
		#
		if(empty($strFilename)){
			return false;
		}
		#
		$arElement = $this->getElementArray($intElementId, $arFields);
		#
		$arFields = $arFieldsOriginal;
		#
		switch($strFormat){
			case 'TXT':
				if($this->isFirst()) {
					if(is_file($strFilenameAbs)){
						@unlink($strFilenameAbs);
					}
				}
				$strLine = sprintf('[%d] %s', $intElementId, $this->escapeTxt($arElement['~NAME']));
				$this->convertEncoding($strLine);
				$this->writeToFile($strLine);
				break;
			case 'CSV':
				if($this->isFirst()) {
					if(is_file($strFilenameAbs)){
						@unlink($strFilenameAbs);
					}
				}
				// Write header
				if($this->isFirst()) {
					$this->writeCsvHeader($arFields, $arElement);
				}
				// Write line
				$this->writeCsvLine($arFields, $arElement);
				break;
		}
		return true;
	}
	
	protected function getFilename(){
		return $this->get('filename');
	}
	
	protected function getValuesArray($strValue){
		$arFields = explode(',', $strValue);
		$arFieldsAvailable = array_keys($this->getFieldsPlain());
		return array_intersect($arFields, $arFieldsAvailable);
	}
	
	protected function getElementArray($intElementId, $arFields){
		$arFeatures = [
			'FIELDS' => [],
			'PROPERTY_ID' => [],
			'EMPTY_PROPERTIES' => true,
		];
		if(in_array('WD_1C_CODE', $arFields)){
			$resProps = \CIBlockProperty::getList([], ['IBLOCK_ID' => $this->intIBlockId, 'CODE' => 'CML2_TRAITS']);
			while($arProp = $resProps->getNext()){
				$arFields[] = 'PROPERTY_'.$arProp['ID'];
			}
		}
		if(in_array('SECTION_NAME', $arFields)){
			$arFeatures['SECTION'] = true;
		}
		if(in_array('FILES_SIZE', $arFields)){
			if(!in_array('PREVIEW_PICTURE', $arFields)){
				$arFields[] = 'PREVIEW_PICTURE';
			}
			if(!in_array('DETAIL_PICTURE', $arFields)){
				$arFields[] = 'DETAIL_PICTURE';
			}
			$resProps = \CIBlockProperty::getList([], ['IBLOCK_ID' => $this->intIBlockId, 'PROPERTY_TYPE' => 'F']);
			while($arProp = $resProps->getNext()){
				$strPropCode = 'PROPERTY_'.$arProp['ID'];
				if(!in_array($strPropCode, $arFields)){
					$arFields[] = $strPropCode;
				}
			}
			$intKey = array_search('FILES_SIZE', $arFields);
			if($intKey !== false){
				unset($arFields[$intKey]);
			}
		}
		foreach($arFields as $strField){
			if($this->isField($strField)){
				$arFeatures['FIELDS'][] = $strField;
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$arFeatures['PROPERTY_ID'][] = $intPropertyId;
			}
			elseif($this->isSeoField($strField)){
				$arFeatures['SEO'] = true;
			}
			elseif($strField == 'CATALOG_BARCODE'){
				$arFeatures['BARCODE'] = true;
			}
			elseif($this->isStoreAmount($strField)){
				$arFeatures['STORES'] = true;
			}
			elseif($this->isPrice($strField)){
				$arFeatures['PRICES'] = true;
			}
			elseif($this->isCatalogField($strField)){
				$arFeatures['CATALOG'] = true;
			}
		}
		$this->handler('onLoggerGetFeatures', [&$arHandlerResult, $intElementId, $arFields, &$arFeatures]);
		$arResult = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures, true);
		$this->handler('onLoggerGetElementArray', [&$arHandlerResult, $intElementId, $arFields, &$arResult, $arFeatures]);
		return $arResult;
	}
	
	protected function writeCsvHeader($arFields, $arElement){
		$arLine = [];
		foreach($arFields as $strField){
			if($this->handler('onLoggerCsvGetHeaderItem', [&$arHandlerResult, &$arFields, &$arElement, &$strField])){
				$strLine = $arHandlerResult['CSV_HEADER'];
			}
			elseif($this->isFieldWithLang($strField)) {
				$strLine = Helper::getMessage('IBLOCK_FIELD_'.$strField);
			}
			elseif(in_array($strField, ['DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID', 'SECTION_NAME', 'CREATED_BY__NAME', 'MODIFIED_BY__NAME'])){
				$strLine = static::getMessage('FIELD_'.$strField);
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$bPropFound = false;
				foreach($arElement['PROPERTIES'] as $arProperty){
					if($arProperty['ID'] == $intPropertyId) {
						$strLine = $arProperty['NAME'];
						$bPropFound = true;
						break;
					}
				}
			}
			elseif ($intPriceId = $this->isPrice($strField)){
				$arPrice = Helper::getPrice($intPriceId);
				$strLine = strlen($arPrice['NAME_LANG']) ? $arPrice['NAME_LANG'] : $arPrice['NAME'];
			}
			elseif(preg_match('#^CATALOG_CURRENCY_(\d+)$#', $strField, $arMatch)){
				$intPriceId = $arMatch[1];
				$arPrice = Helper::getPrice($intPriceId);
				if($arPrice){
					$strPriceName = strlen($arPrice['NAME_LANG']) ? $arPrice['NAME_LANG'] : $arPrice['NAME'];
					$strLine = static::getMessage('FIELD_CATALOG_CURRENCY', ['#PRICE_NAME#' => $strPriceName]);
				}
			}
			elseif($intStoreId = $this->isStoreAmount($strField)){
				$strLine = static::getMessage('FIELD_CATALOG_STORE', [
					'#STORE_NAME#' => $arElement['STORES'][$intStoreId]['TITLE'],
				]);
			}
			elseif ($strCatalogField = $this->isCatalogField($strField)){
				$strLine = static::getMessage('FIELD_CATALOG_'.$strCatalogField);
				if(!strlen($strLine)){
					$strLine = $strCatalogField;
				}
			}
			elseif($strSeoField = $this->isSeoField($strField)){
					$strLine = static::getMessage('FIELD_SEO_'.$strSeoField);
			}
			elseif($strField == 'WD_1C_CODE'){
				$strLine = static::getMessage('FIELD_WD_1C_CODE');
			}
			elseif($strField == 'FILES_SIZE'){
				$strLine = static::getMessage('FIELD_FILES_SIZE');
			}
			else{
				$strLine = $strField;
			}
			$strLine = trim($this->escapeCsv($strLine));
			$arLine[] = $strLine;
		}
		$strLine = implode(static::CSV_SEPARATOR, $arLine);
		$this->convertEncoding($strLine);
		$this->writeToFile($strLine);
	}
	
	protected function writeCsvLine($arFields, $arElement){
		if($this->handler('onLoggerCsvWriteLine', [&$arHandlerResult, &$arFields, &$arElement]) === false){ // Handler for check whether plugin checks need to export
			return;
		}
		foreach($arFields as $strField){
			$strLine = '';
			if($this->handler('onLoggerCsvGetLineItem', [&$arHandlerResult, &$arFields, &$arElement, &$strField])){
				$strLine = $arHandlerResult['CSV_LINE'];
			}
			elseif(in_array($strField, ['ACTIVE'])) {
				$strLine = $arElement[$strField] == 'Y' ? Helper::getMessage('WDA_Y') : Helper::getMessage('WDA_N');
			}
			elseif(in_array($strField, ['PREVIEW_PICTURE', 'DETAIL_PICTURE'])) {
				if(is_array($arElement[$strField])){
					$strLine = $this->makeFullUrl($arElement[$strField]['SRC']);
				}
			}
			elseif(in_array($strField, ['DETAIL_PAGE_URL'])) {
				$strLine = $this->makeFullUrl($arElement[$strField]);
			}
			elseif(preg_match('#^(CREATED|MODIFIED)_BY__NAME$#', $strField, $arMatch)) {
				$intUserId = $arElement[$arMatch['1'].'_BY'];
				if($intUserId){
					$arUser = \CUser::getList($by='ID', $order='ASC', ['ID' => $intUserId], ['FIELDS' => ['LOGIN', 'NAME', 'LAST_NAME']])->fetch();
					if($arUser){
						$strLine = trim(sprintf('(%s) %s %s', $arUser['LOGIN'], $arUser['NAME'], $arUser['LAST_NAME']));
					}
				}
			}
			elseif($this->isField($strField)) {
				$strLine = $arElement['~'.$strField];
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif ($strField == 'SECTION_NAME') {
				$strLine = $arElement['SECTION']['NAME'];
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				foreach($arElement['PROPERTIES'] as $arProperty){
					if($arProperty['ID'] == $intPropertyId) {
						$arValue = $arProperty['VALUE'];
						if($arProperty['PROPERTY_TYPE'] == 'S' && !strlen($arProperty['USER_TYPE'])){
							$arValue = $arProperty['~VALUE'];
						}
						if($arProperty['PROPERTY_TYPE'] == 'F') {
							if(!is_array($arValue) && is_numeric($arValue) && $arValue) {
								$arValue = array($arValue);
							}
							foreach($arValue as $Key => $strItem){
								if(is_numeric($strItem)){
									$arValue[$Key] = $this->makeFullUrl(\CFile::getPath($strItem));
								}
							}
						}
						if(is_array($arValue)){
							$arValue = implode(', ', $arValue);
						}
						$strLine = $arValue;
						if($this->get('for_excel') == 'Y'){
							$this->formatNumberForExcel($strLine);
						}
						break;
					}
				}
			}
			elseif ($intPriceId = $this->isPrice($strField)){
				$fPrice = $arElement['PRICES'][$intPriceId]['PRICE'];
				$strLine = is_numeric($fPrice) ? floatVal($fPrice) : $fPrice;
			}
			elseif(preg_match('#^CATALOG_CURRENCY_(\d+)$#', $strField, $arMatch)){
				$strLine = $arElement['PRICES'][$arMatch[1]]['CURRENCY'];
			}
			elseif($intStoreId = $this->isStoreAmount($strField)){
				$strLine = $arElement['STORES'][$intStoreId]['AMOUNT'];
			}
			elseif ($strCatalogField = $this->isCatalogField($strField)){
				$strLine = $arElement[$strField];
				if(in_array($strCatalogField, ['AVAILABLE', 'VAT_INCLUDED', 'QUANTITY_TRACE', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE'])){
					$strLine = Helper::getMessage('WDA_'.($strLine == 'Y' ? 'Y' : 'N'));
				}
				elseif(in_array($strCatalogField, ['MEASURE_UNIT', 'MEASURE_NAME'])){
					if($arElement['CATALOG_MEASURE']){
						$arMeasures = Helper::getMeasuresList();
						if($strCatalogField == 'MEASURE_UNIT'){
							$strLine = $arMeasures[$arElement['CATALOG_MEASURE']]['SYMBOL_RUS'];
						}
						elseif($strCatalogField == 'MEASURE_NAME'){
							$strLine = $arMeasures[$arElement['CATALOG_MEASURE']]['MEASURE_TITLE'];
						}
					}
				}
				elseif($strCatalogField == 'MEASURE_RATIO'){
					$strLine = '';
					if(\Bitrix\Main\Loader::includeModule('catalog')){
						$resRatio = \CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $arElement['ID']]);
						if($arRatio = $resRatio->getNext(false, false)) {
							$strLine = $arRatio['RATIO'];
						}
					}
				}
				elseif($strCatalogField == 'BARCODE'){
					if(is_array($strLine)){
						$strLine = implode(', ', $strLine);
					}
				}
				elseif($strCatalogField == 'VAT_VALUE'){
					$fVatRate = Helper::getVatRateById($arElement['CATALOG_VAT_ID']);
					if(is_numeric($fVatRate)){
						$fVatRate = floatVal($fVatRate);
					}
					$strLine = $fVatRate;
				}
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif($strSeoField = $this->isSeoField($strField)){
				$strLine = $arElement['SEO'][$strSeoField];
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif($strField == 'WD_1C_CODE'){
				$strLine = '';
				foreach($arElement['PROPERTIES'] as $arProperty){
					if($arProperty['CODE'] == 'CML2_TRAITS' && is_array($arProperty['VALUE']) && $arProperty['DESCRIPTION']){
						foreach($arProperty['DESCRIPTION'] as $key => $strDescription){
							if($strDescription == static::getMessage('FIELD_WD_1C_CODE_TRAIT', true)){
								$strLine = $arProperty['VALUE'][$key];
								if($this->get('for_excel') == 'Y'){
									$this->formatNumberForExcel($strLine);
								}
							}
						}
					}
				}
			}
			elseif($strField == 'FILES_SIZE'){
				$intSize = 0;
				$strLine = '';
				if(is_array($arElement['PREVIEW_PICTURE'])){
					$intSize += $arElement['PREVIEW_PICTURE']['FILE_SIZE'];
				}
				if(is_array($arElement['DETAIL_PICTURE'])){
					$intSize += $arElement['DETAIL_PICTURE']['FILE_SIZE'];
				}
				foreach($arElement['PROPERTIES'] as $arProperty){
					if($arProperty['PROPERTY_TYPE'] == 'F'){
						if(is_array($arProperty['VALUE'])){
							foreach($arProperty['VALUE'] as $intFileId){
								if(is_numeric($intFileId)){
									$arFile = \CFile::getFileArray($intFileId);
									$intSize += $arFile['FILE_SIZE'];
								}
							}
						}
						elseif(is_numeric($arProperty['VALUE'])){
							$arFile = \CFile::getFileArray($arProperty['VALUE']);
							$intSize += $arFile['FILE_SIZE'];
						}
					}
				}
				$strLine = round($intSize / 1024, 2);
				$strLine = str_replace('.', ',', $strLine);
			}
			elseif(isset($arElement[$strField])){
				$strLine = $arElement[$strField];
			}
			$strLine = trim($this->escapeCsv($strLine));
			$arLine[] = $strLine;
		}
		$strLine = implode(static::CSV_SEPARATOR, $arLine);
		$this->convertEncoding($strLine);
		$this->writeToFile($strLine);
		$this->handler('onAfterLoggerCsvWriteLine', [&$arHandlerResult, $arFields, $arElement, $arLine, $strLine]);
	}
	
	protected function isFieldWithLang($strField){
		$arFields = [
			'ID',
			'NAME',
			'CODE',
			'ACTIVE',
			'XML_ID',
			'SORT',
			'PREVIEW_TEXT',
			'PREVIEW_TEXT_TYPE',
			'PREVIEW_PICTURE',
			'DETAIL_TEXT',
			'DETAIL_TEXT_TYPE',
			'DETAIL_PICTURE',
			'DATE_ACTIVE_FROM',
			'DATE_ACTIVE_TO',
			'ACTIVE_FROM',
			'ACTIVE_TO',
			'SHOW_COUNTER',
			'SHOW_COUNTER_START',
			'TAGS',
			'DATE_CREATE',
			'CREATED_BY',
			'TIMESTAMP_X',
			'MODIFIED_BY',
			'IBLOCK_ID',
		];
		return in_array($strField, $arFields);
	}
	
	protected function writeToFile($strLine){
		$strFilename = $this->getFilename();
		$resHandle = fopen($_SERVER['DOCUMENT_ROOT'].$strFilename, 'a+');
		@flock($resHandle, LOCK_EX);
		fwrite($resHandle, $strLine.PHP_EOL);
		@flock($resHandle, LOCK_UN);
		fclose($resHandle);
	}
	
	protected function escapeTxt($strValue){
		$arReplace = [
			"\n" => '\n',
			"\r" => '',
			"\t" => '',
		];
		return str_replace(array_keys($arReplace), array_values($arReplace), $strValue);
	}
	
	protected function escapeCsv($strValue){
		return sprintf('"%s"', str_replace('"', '""' ,$strValue));
	}
	
	protected function convertEncoding(&$strValue){
		$strEncoding = $this->get('encoding');
		if($this->get('for_excel') == 'Y'){
			$strEncoding = 'CP1251';
		}
		if($strEncoding == 'CP1251' && Helper::isUtf()) {
			$strValue = Helper::convertEncoding($strValue, 'UTF-8', 'CP1251');
		}
		elseif($strEncoding == 'UTF-8' && !Helper::isUtf()){
			$strValue = Helper::convertEncoding($strValue, 'CP1251', 'UTF-8');
		}
	}
	
	protected function formatNumberForExcel(&$mNumber){
		if(is_numeric($mNumber)){
			$mNumber = sprintf('="%s"', $mNumber);
		}
	}
	
	/**
	 *	/catalog/product1.html => https://www.site.ru/catalog/product1.html
	 */
	protected function makeFullUrl($strUrl){
		$strUrl = $this->get('site_domain').$strUrl;
		return $strUrl;
	}
	
}

?>