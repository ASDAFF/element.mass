<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class Logger extends PluginSection {
	
	const CSV_SEPARATOR = ';';
	
	protected $arFieldsFilter = [
		'FIELDS' => [],
		'PROPERTIES' => [],
		'SEO' => [],
	];
	
	protected $arCacheSectionsProps;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	public function __construct(){
		parent::__construct();
		$this->arCustomFields = array_merge($this->arCustomFields, [
			'FILES_SIZE' => [
				'NAME' => static::getMessage('FIELD_FILES_SIZE'),
				'TYPE' => 'N',
			],
		]);
	}
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
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
		$bGetSeo = false;
		foreach($arFields as $key => $value){
			if($this->isSeoField($value)){
				unset($arFields[$key]);
				$bGetSeo = true;
			}
		}
		if($bGetSeo){
			$arFields[] = 'SEO';
		}
		#
		$arSection = $this->getSectionArray($intSectionId, $arFields);
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
				$strLine = sprintf('[%d] %s', $intSectionId, $this->escapeTxt($arSection['~NAME']));
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
					$this->writeCsvHeader($arFields, $arSection);
				}
				// Write line
				$this->writeCsvLine($arFields, $arSection);
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
	
	protected function getSectionFields(){
		if(!$this->arCacheSectionsProps){
			$this->arCacheSectionsProps = [];
			foreach(IBlock::getSectionUserFields($this->intIBlockId) as $strUserField => $arUserField){
				if($arUserField['USER_TYPE_ID'] == 'file'){
					$this->arCacheSectionsProps[] = $strUserField;
				}
			}
		}
		return $this->arCacheSectionsProps;
	}
	
	protected function getSectionArray($intSectionId, $arFields){
		$this->handler('onLoggerGetSectionArray', [&$arHandlerResult, $intSectionId, &$arFields]);
		if(in_array('FILES_SIZE', $arFields)){
			if(!in_array('PICTURE', $arFields)){
				$arFields[] = 'PICTURE';
			}
			if(!in_array('DETAIL_PICTURE', $arFields)){
				$arFields[] = 'DETAIL_PICTURE';
			}
			foreach($this->getSectionFields() as $strUserField){
				if(!in_array($strUserField, $arFields)){
					$arFields[] = $strUserField;
				}
			}
			$intKey = array_search('FILES_SIZE', $arFields);
			if($intKey !== false){
				unset($arFields[$intKey]);
			}
		}
		$arResult = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arFields);
		$this->handler('onLoggerGetElementArray', [&$arHandlerResult, $intSectionId, &$arFields, &$arResult]);
		return $arResult;
	}
	
	protected function writeCsvHeader($arFields, $arSection){
		$arLine = [];
		foreach($arFields as $strField){
			if($this->handler('onLoggerCsvGetHeaderItem', [&$arHandlerResult, &$arFields, &$arSection, &$strField])){
				$strLine = $arHandlerResult['CSV_HEADER'];
			}
			elseif($this->isFieldWithLang($strField)) {
				$strLine = Helper::getMessage('IBLOCK_FIELD_'.$strField);
			}
			elseif(in_array($strField, ['SECTION_PAGE_URL', 'IBLOCK_SECTION_ID', 'CREATED_BY__NAME', 'MODIFIED_BY__NAME'])){
				$strLine = static::getMessage('FIELD_'.$strField);
			}
			elseif($intPropertyId = $this->isProperty($strField)){
				$bPropFound = false;
				foreach($arSection['PROPERTIES'] as $arProperty){
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
					'#STORE_NAME#' => $arSection['STORES'][$intStoreId]['TITLE'],
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
	
	protected function writeCsvLine($arFields, $arSection){
		if($this->handler('onLoggerCsvWriteLine', [&$arHandlerResult, &$arFields, &$arSection]) === false){ // Handler for check whether plugin checks need to export
			return;
		}
		foreach($arFields as $strField){
			$strLine = '';
			if($this->handler('onLoggerCsvGetLineItem', [&$arHandlerResult, &$arFields, &$arSection, &$strField])){
				$strLine = $arHandlerResult['CSV_LINE'];
			}
			elseif(in_array($strField, ['ACTIVE'])) {
				$strLine = $arSection[$strField] == 'Y' ? Helper::getMessage('WDA_Y') : Helper::getMessage('WDA_N');
			}
			elseif(in_array($strField, ['PICTURE', 'DETAIL_PICTURE'])) {
				if(is_array($arSection[$strField])){
					$strLine = $this->makeFullUrl($arSection[$strField]['SRC']);
				}
			}
			elseif(in_array($strField, ['SECTION_PAGE_URL'])) {
				$strLine = $this->makeFullUrl($arSection[$strField]);
			}
			elseif(preg_match('#^(CREATED|MODIFIED)_BY__NAME$#', $strField, $arMatch)) {
				$intUserId = $arSection[$arMatch['1'].'_BY'];
				if($intUserId){
					$arUser = \CUser::getList($by='ID', $order='ASC', ['ID' => $intUserId], ['FIELDS' => ['LOGIN', 'NAME', 'LAST_NAME']])->fetch();
					if($arUser){
						$strLine = trim(sprintf('(%s) %s %s', $arUser['LOGIN'], $arUser['NAME'], $arUser['LAST_NAME']));
					}
				}
			}
			elseif($this->isField($strField)) {
				$strLine = $arSection['~'.$strField];
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif($strField == 'FILES_SIZE'){
				$intSize = 0;
				$strLine = '';
				if(is_array($arSection['PICTURE'])){
					$intSize += $arSection['PICTURE']['FILE_SIZE'];
				}
				if(is_array($arSection['DETAIL_PICTURE'])){
					$intSize += $arSection['DETAIL_PICTURE']['FILE_SIZE'];
				}
				$arFileFields = [];
				foreach($this->getSectionFields() as $strUserField){
					if(!in_array($strUserField, $arFileFields)){
						$arFileFields[] = $strUserField;
					}
				}
				if(is_array($arSection['PROPERTIES'])){
					foreach($arSection['PROPERTIES'] as $strProperty => $mValue){
						if(in_array($strProperty, $arFileFields)){
							if(is_array($mValue)){
								foreach($mValue as $intFileId){
									if(is_numeric($intFileId)){
										$arFile = \CFile::getFileArray($intFileId);
										$intSize += $arFile['FILE_SIZE'];
									}
								}
							}
							elseif(is_numeric($mValue)){
								$arFile = \CFile::getFileArray($mValue);
								$intSize += $arFile['FILE_SIZE'];
							}
						}
					}
				}
				$strLine = round($intSize / 1024, 2);
				$strLine = str_replace('.', ',', $strLine);
			}
			elseif($strPropertyCode = $this->isSectionProperty($strField)){
				$arProperty = $this->getProperty($strPropertyCode);
				$strLine = $arSection['PROPERTIES']['~'.$strPropertyCode];
				if($arProperty['PROPERTY_TYPE'] == 'F'){
					if(is_array($strLine)){
						foreach($strLine as $key => $value){
							if(is_numeric($value)){
								$strLine[$Key] = $this->makeFullUrl(\CFile::getPath($value));
							}
						}
					}
					else{
						if(is_numeric($strLine)){
							$strLine = $this->makeFullUrl(\CFile::getPath($strLine));
						}
					}
				}
				if(is_array($strLine)){
					$strLine = implode("\n", $strLine);
				}
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif($strSeoField = $this->isSeoField($strField)){
				$strLine = $arSection['SEO'][$strSeoField];
				if($this->get('for_excel') == 'Y'){
					$this->formatNumberForExcel($strLine);
				}
			}
			elseif(isset($arSection[$strField])){
				$strLine = $arSection[$strField];
			}
			$strLine = trim($this->escapeCsv($strLine));
			$arLine[] = $strLine;
		}
		$strLine = implode(static::CSV_SEPARATOR, $arLine);
		$this->convertEncoding($strLine);
		$this->writeToFile($strLine);
		$this->handler('onAfterLoggerCsvWriteLine', [&$arHandlerResult, $arFields, $arSection, $arLine, $strLine]);
	}
	
	protected function isFieldWithLang($strField){
		$arFields = [
			'ID',
			'NAME',
			'CODE',
			'ACTIVE',
			'XML_ID',
			'SORT',
			'DESCRIPTION',
			'DESCRIPTION_TYPE',
			'PICTURE',
			'DETAIL_PICTURE',
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