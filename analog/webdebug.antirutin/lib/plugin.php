<?
namespace WD\Antirutin;

use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

abstract class Plugin {
	
	const GROUP = '';
	const FIELD_NAME = 'actions';
	
	# Lang
	const LANG_PREFIX = 'WDA_PLUGIN';
	
	const MULTIPLE_MODE_COMMA = 'comma';
	const MULTIPLE_MODE_FIRST = 'first';
	const MULTIPLE_MODE_LAST = 'last';
	const MULTIPLE_MODE_MAX = 'max';
	const MULTIPLE_MODE_MIN = 'min';
	
	protected $intProfileId = null; // can be null!
	protected $strId = null; // random ID
	protected $strEntityType;
	protected $intIBlockId = null;
	protected $intOffersIBlockId = null;
	protected $arGet = [];
	protected $arPost = [];
	protected $arSavedData = [];
	protected $obWorker = null;
	protected $strError = null;
	protected $bSingleTime = false;
	protected $strDirectory = null;
	
	protected $arSectionsId = [];
	protected $bIncludeSubsections = false;
	
	protected $obElement = null;
	protected $obSection = null;
	
	protected $arCss = [];
	protected $arJs = [];
	
	protected $mObjectValues;
	
	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y'],
		'SECTION' => ['IS_WRITEABLE' => 'Y'],
		'IBLOCK' => ['IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['IS_WRITEABLE' => 'Y'],
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];
	
	protected $arCustomFields = [];
	
	/********************************************************************************************************************/

	/**
	 *	Get message for plugin
	 */
	public static function getMessage($strMessage, $arReplace=null, $bDebug=false){
		$strMessageResult = '';
		$strClassBase = __CLASS__;
		$strClass = get_called_class();
		while(true){
			$strCode = $strClass::getCode();
			$strPrefix = static::LANG_PREFIX.'_'.$strCode;
			$strMessageResult = Helper::getMessage($strMessage, $strPrefix, $arReplace, $bDebug);
			if(strlen($strMessageResult)){
				break;
			}
			$strClass = get_parent_class($strClass);
			if(!$strClass || in_array($strClass, [$strClassBase, $strClassBase.'Element', $strClassBase.'Section'])){
				break;
			}
		}
		return $strMessageResult;
	}
	
	/** 
	 *	Get group code
	 *	@return string for native groups or array(key => ['CODE' => '...', 'NAME' => '...', 'SORT' => '...']) for custom groups
	 */
	public static function getGroup(){
		if(strlen(static::GROUP)){
			return static::GROUP;
		}
		else{
			return key(Helper::getPluginGroups());
		}
	}
	
	/**
	 *	Is plugin disabled?
	 */
	public static function isDisabled(){
		return false;
	}
	
	/**
	 *	Get plugin code
	 */
	final public static function getCode(){
		$strCode = end(explode('\\', get_called_class()));
		$strCode = Helper::toUnderlineCase($strCode, true);
		return $strCode;
	}
	
	/**
	 *	Get plugin name
	 */
	final public static function getName(){
		return static::getMessage('NAME');
	}
	
	/**
	 *	Get plugin hint (short description)
	 */
	final public static function getHint(){
		return static::getMessage('HINT');
	}
	
	/**
	 *	Get plugin description
	 *	File content is always in UTF-8 !!!
	 */
	final public static function getDescription(){
		$strDescriptionFile = static::getFolder().'/.description.php';
		ob_start();
		if(is_file($strDescriptionFile)){
			include $strDescriptionFile;
		}
		$strResult = ob_get_clean();
		if(!Helper::isUtf()){
			$strResult = Helper::convertEncoding($strResult, 'UTF-8', 'CP1251');
		}
		return $strResult;
	}
	
	/**
	 *	Get folder of current plugin
	 */
	final public static function getFolder($bRelative=false){
		$strClassName = get_called_class();
		$obReflectionClass = new \ReflectionClass($strClassName);
		$strFileName = $obReflectionClass->getFileName();
		$obReflectionClass = null;
		$strResult = pathinfo($strFileName, PATHINFO_DIRNAME);
		if($bRelative) {
			$strResult = substr($strResult, strlen(Helper::root()));
		}
		return $strResult;
	}
	
	/********************************************************************************************************************/
	
	/**
	 *	Create object
	 */
	public function __construct(){
		$this->strId = Helper::randString();
		$this->strDirectory = $this->getDirectory();
		$this->includeClasses();
		$this->obElement = new \CIBlockElement;
		$this->obSection = new \CIBlockSection;
	}
	
	/**
	 *	Set data for current section settings
	 */
	public function setSections($arSectionsId,  $bIncludeSubsections){
		$this->arSectionsId = $arSectionsId;
		$this->bIncludeSubsections = $bIncludeSubsections;
	}
	
	/**
	 *	Execute just for single element?
	 */
	public function isSingleTime(){
		return $this->bSingleTime === true;
	}
	
	/**
	 *	Get plugin directory
	 */
	public function getDirectory(){
		$strClass = get_class($this);
		$obThisClassReflection = new \ReflectionClass($strClass);
		return pathinfo($obThisClassReflection->getFilename(), PATHINFO_DIRNAME);
	}
	
	/**
	 *	Include classes
	 */
	public function includeClasses(){
		//
	}
	
	/**
	 *	Set strEntityType
	 */
	public function setEntityType($strEntityType){
		$this->strEntityType = $strEntityType;
	}
	
	/**
	 *	Set intIBlockId
	 */
	public function setIBlockId($intIBlockId){
		$this->intIBlockId = $intIBlockId;
	}
	
	/**
	 *	Get intIBlockId
	 */
	public function getIBlockId(){
		return $this->intIBlockId;
	}
	
	/**
	 *	Get ID for offers iblock
	 */
	public function getOffersIBlockId(){
		static $arOffersIBlockId = [];
		$this->intOffersIBlockId = &$arOffersIBlockId[$this->intIBlockId];
		if(!isset($this->intOffersIBlockId)){
			$intIBlockOffersId = false;
			$arCatalog = Helper::getCatalogArray($this->intIBlockId);
			if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID']){
				$this->intOffersIBlockId = $arCatalog['OFFERS_IBLOCK_ID'];
			}
		}
		return $this->intOffersIBlockId;
	}
	
	/**
	 *	Set GET and POST data
	 */
	public function setRequestData($arGet, $arPost){
		$this->arGet = $arGet;
		$this->arPost = $arPost;
	}
	
	/**
	 *	Set plugin id
	 */
	public function setId($strId){
		$this->strId = $strId;
	}
	
	/**
	 *	Get plugin random id
	 */
	public function getId($strSuffix=null){
		return $this->strId.(is_string($strSuffix) && strlen($strSuffix) ? '_'.$strSuffix : '');
	}
	
	/**
	 *	Set saved data to object
	 */
	public function setSavedData($arSavedData){
		$this->arSavedData = $arSavedData;
	}
	
	/**
	 *	Get saved data
	 */
	public function getSavedData(){
		return $this->arSavedData;
	}
	
	/**
	 *	Get item from $this->arSavedData
	 */
	public function get($strOption, $mKey=null, $mValue=null){
		if($strOption == 'description'){
			$strOption = 'value_description';
		}
		if(!is_null($mKey)){
			return !is_null($this->arSavedData[$strOption][$mKey]) ? $this->arSavedData[$strOption][$mKey] : $mValue;
		}
		return !is_null($this->arSavedData[$strOption]) ? $this->arSavedData[$strOption] : $mValue;
	}
	
	/**
	 *	Set item to $this->arSavedData
	 */
	public function set($strOption, $mValue){
		$this->arSavedData[$strOption] = $mValue;
	}
	
	/**
	 *	Get all saved data
	 */
	public function getAll(){
		return $this->arSavedData;
	}
	
	/**
	 *	Is value empty?
	 */
	public function isEmpty($strValueKey){
		$mValue = $this->get($strValueKey);
		return Helper::isEmpty($mValue);
	}
	
	/**
	 *	
	 */
	public function fieldName($strFormField, $bHint=true, $bColon=true){
		return static::getMessage($strFormField).($bColon ? ':' : '').($bHint ? ' '.$this->fieldHint($strFormField) : '');
	}
	
	/**
	 *	
	 */
	public function fieldHint($strFormField){
		return Helper::showHint(static::getMessage('HINT_'.$strFormField));
	}
	
	/**
	 *	
	 */
	public function getInputName($strName, $bMultiple=false){
		if(strpos($strName, '[') === false){
			$strName = sprintf('[%s]', $strName);
		}
		return sprintf('%s[%s]%s%s', static::FIELD_NAME, $this->strId, $strName, $bMultiple ? '[]' : '');
	}
	
	/**
	 *	Show settings html
	 */
	public function showSettings(){
		$strSettingsFile = $this->getFileRecursive('.form.php');
		ob_start();
		if(is_file($strSettingsFile)){
			Helper::loadMessages($strSettingsFile);
			print $this->printCss();
			print $this->printCustomCss();
			#Helper::loadMessages($strSettingsFile);
			print sprintf('<div class="plugin-form" data-plugin-form="%s" id="%s">', $this->getCode(), 
				$this->getId());
			print $this->printJs();
			print $this->printCustomJs();
			include $strSettingsFile;
			print '</div>';
		}
		$strResult = ob_get_clean();
		return $strResult;
	}
	
	/**
	 *	Show setting for parent plugin
	 */
	protected function showParentSettings(){
		$strClass = get_parent_class(get_called_class());
		$strFormFilename = $strClass::getFolder().'/.form.php';
		Helper::loadMessages($strFormFilename);
		require $strFormFilename;
	}
	
	/**
	 *	Print CSS to plugin form
	 */
	final function printCss(){
		$strResult = '';
		$strFile = $this->getFileRecursive('style.css');
		if(strlen($strFile) && is_file($strFile)){
			$strCss = file_get_contents($strFile);
			$strFile2 = $this->getFileRecursive('style2.css');
			if(strlen($strFile2) && is_file($strFile2)){
				$strCss .= PHP_EOL.file_get_contents($strFile2);
			}
			$strResult = sprintf('<style>%s</style>', $strCss);
			$this->prepareCss($strResult);
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function prepareCss(&$strCss){
		//
	}
	
	/**
	 *	Print JS to plugin form
	 */
	final function printJs(){
		$strResult = '';
		$strFile = $this->getFileRecursive('script.js');
		if(strlen($strFile) && is_file($strFile)){
			$strJs = file_get_contents($strFile);
			$strFile2 = $this->getFileRecursive('script2.js');
			if(strlen($strFile2) && is_file($strFile2)){
				$strJs .= PHP_EOL.file_get_contents($strFile2);
			}
			$strResult = sprintf('<script>
(function(){
	var
		code = "%1$s",
		id = "%2$s",
		hid = "#%2$s",
		div = $(\'div[data-role="plugin-settings"][data-id="%2$s"]\'),
		title = $.trim($(\'div[data-role="wda-fieldset"] [data-role="wda-fieldset-toggle"]\', div).text());
%3$s;
})();
			</script>',
				$this->getCode(), $this->getId(), $strJs);
			$this->prepareJs($strResult);
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function prepareJs(&$strJs){
		//
	}
	
	/**
	 *	
	 */
	protected function printCustomCss(){
		$strResult = '';
		if(is_array($this->arCss) && !empty($this->arCss)){
			foreach($this->arCss as $strCssFile){
				if(is_file(Helper::root().$strCssFile)){
					$strResult .= sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $strCssFile);
				}
			}
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	protected function printCustomJs(){
		$strResult = '';
		if(is_array($this->arJs) && !empty($this->arJs)){
			foreach($this->arJs as $strJsFile){
				if(is_file(Helper::root().$strJsFile)){
					$strResult .= sprintf('<script type="text/javascript" src="%s"></script>', $strJsFile);
				}
			}
		}
		return $strResult;
	}
	
	/**
	 *	Get file recursively (in this plugin's folder, or in parent plugin's form)
	 */
	protected function getFileRecursive($strFile){
		$strResult = null;
		$strClassBase = __CLASS__;
		$strClass = get_called_class();
		while(true){
			$strFileTest = $strClass::getFolder().'/'.$strFile;
			if(is_file($strFileTest)){
				$strResult = $strFileTest;
				break;
			}
			$strClass = get_parent_class($strClass);
			if(!$strClass || in_array($strClass, [$strClassBase, $strClassBase.'Element', $strClassBase.'Section'])){
				break;
			}
		}
		return $strResult;
	}
	
	/**
	 *	
	 */
	public function customAjax($strAction, &$arJson){
		$strCalledClass = get_called_class();
		$arCalledClassMethods = [];
		$obReflection = new \ReflectionClass($strCalledClass);
		foreach($obReflection->getMethods() as $obMethod){
			if($obMethod->class == $strCalledClass){
				$arCalledClassMethods[] = $obMethod->name;
			}
		}
		$strAction = Helper::toCamelCase($strAction);
		if(in_array($strAction, $arCalledClassMethods)){
			return call_user_func_array([$this, $strAction], [&$arJson]);
		}
	}
	
	/**
	 *	Get available fields with filtering
	 */
	protected function getFields($arFilter=null, $strField=null, $intIBlockId=null){
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($intIBlockId){
			$arFields = IBlock::getAvailableFields($intIBlockId, $this->strEntityType, 
				is_array($arFilter) ? $arFilter : $this->arFieldsFilter);
			if(is_array($this->arCustomFields) && !empty($this->arCustomFields)){
				$this->prepareCustomFields();
				$arFields['SPECIAL_FIELDS'] = [
					'NAME' => Helper::getMessage('WDA_PLUGIN_SPECIAL_FIELDS'),
					'ITEMS' => $this->arCustomFields,
				];
			}
			if($strField){
				foreach($arFields as $strGroup => $arGroup){
					if(is_array($arGroup['ITEMS'])){
						foreach($arGroup['ITEMS'] as $strItem => $arItem){
							if($arGroup['PREFIX'].$strItem == $strField){
								$arItem['GROUP'] = array_merge($arGroup, [
									'CODE' => $strGroup,
									'ITEMS' => null,
								]);
								unset($arItem['GROUP']['ITEMS']);
								return $arItem;
								break;
							}
						}
					}
				}
			}
			else{
				return $arFields;
			}
		}
		return [];
	}
	
	/**
	 *	Get available fields with filtering
	 */
	protected function getFieldsPlain($arFilter=null, $intIBlockId=null){
		$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
		if($intIBlockId){
			$arFields = IBlock::getAvailableFieldsPlain($intIBlockId, $this->strEntityType, 
				is_array($arFilter) ? $arFilter : $this->arFieldsFilter);
			if(is_array($this->arCustomFields) && !empty($this->arCustomFields)){
				$this->prepareCustomFields();
				$arFields = array_merge($arFields, $this->arCustomFields);
			}
			return $arFields;
		}
		return [];
	}
	
	/**
	 *	
	 */
	protected function prepareCustomFields(){
		if(is_array($this->arCustomFields) && !empty($this->arCustomFields)){
			foreach($this->arCustomFields as $key => $arField){
				$this->arCustomFields[$key]['CODE'] = $key;
			}
		}
	}
	
	/**
	 *	Show hint
	 */
	protected function hint($strMessageId){
		print Helper::showHint(static::getMessage('HINT_'.$strMessageId));
	}
	
	/**
	 *	Set worker
	 */
	public function setWorker($obWorker){
		$this->obWorker = $obWorker;
		$this->intProfileId = $obWorker->getProfileId();
	}
	
	/**
	 *	Is processing first element?
	 */
	public function isFirst(){
		return $this->obWorker->getSessionKey('COUNT_SUCCESS') == 0;
	}
	
	/**
	 *	Logging
	 */
	public function log($mMessage, $bDebug=false){
		$arLogMessage = [
			'PREFIX' => static::getCode(),
			'MESSAGE' => $mMessage,
		];
		Log::add($arLogMessage, $this->intProfileId, $bDebug);
	}
	
	/**
	 *	Wrapper for Worker::addDebugMessage()
	 */
	public function debug($strMessage, $bPrettyPrint=true, $bSet=false){
		if($bPrettyPrint){
			ob_start();
			Helper::P($strMessage);
			$strMessage = ob_get_clean();
		}
		else{
			$strMessage = is_array($strMessage) ? print_r($strMessage, true) : (is_string($strMessage) ? $strMessage : var_export($strMessage, true));
		}
		// Debug for process
		if($this->obWorker){
			if($bSet) {
				$this->obWorker->setDebugMessage($strMessage);
			}
			else{
				$this->obWorker->addDebugMessage($strMessage);
			}
		}
		// Debug for .form.php
		else{
			print $strMessage;
		}
	}
	
	/**
	 *	Get is breaked
	 */
	public function getBreaked(){
		return $this->obWorker->getBreaked();
	}
	
	/**
	 *	Set breaked
	 */
	public function setBreaked($bBreaked=true){
		$this->obWorker->setBreaked($bBreaked);
	}
	
	/**
	 *	Execute event on start
	 */
	public function onStart(){
		//
	}
	
	/**
	 *	Execute event on continue
	 */
	public function onContinue(){
		//
	}
	
	/**
	 *	Execute event on start
	 */
	public function onFinish(){
		//
	}
	
	/**
	 *	Execute handler
	 */
	protected function handler(string $strHandler, array $arParams){
		if(strlen($strHandler) && method_exists($this, $strHandler)){
			$arResult = &$arParams[0];
			unset($arParams[0]);
			$bResult = call_user_func_array([$this, $strHandler], [$arParams, &$arResult]);
			$arParams[0] = $bResult;
			return $bResult;
		}
		return null;
	}
	
	/**
	 *	Check field is field, property, price
	 */
	protected function isField($strField){
		return IBlock::isField($strField);
	}
	protected function isSectionField($strField){
		return IBlock::isSectionField($strField);
	}
	protected function isProperty($strField){
		return IBlock::isProperty($strField);
	}
	protected function isSectionProperty($strField){
		return IBlock::isSectionProperty($strField);
	}
	protected function isSeoField($strField){
		return IBlock::isSeoField($strField);
	}
	protected function isPrice($strField){
		return IBlock::isPrice($strField);
	}
	protected function isPriceExt($strField, &$strSuffix){
		return IBlock::isPriceExt($strField, $strSuffix);
	}
	protected function isStoreAmount($strField){
		return IBlock::isStoreAmount($strField);
	}
	protected function isCatalogField($strField){
		return IBlock::isCatalogField($strField);
	}
	
	/**
	 *	Get value = multiple or single according to settings
	 */
	protected function cutMultipleValue($arValues, $strMultipleMode=null){
		$arResult = null;
		if(!empty($arValues)){
			switch($strMultipleMode){
				case static::MULTIPLE_MODE_COMMA:
					$obFirstValue = reset($arValues);
					$arTextValue = [];
					$arTextDescr = [];
					foreach($arValues as $obValue){
						$arTextValue[] = $obValue->getValue();
						$arTextDescr[] = $obValue->getDescription();
					}
					$obFirstValue->setValue(implode(', ', $arTextValue));
					$obFirstValue->setDescription(implode(', ', $arTextDescr));
					$arResult = $obFirstValue;
					break;
				case static::MULTIPLE_MODE_FIRST:
					$arResult = reset($arValues);
					break;
				case static::MULTIPLE_MODE_LAST:
					$arResult = end($arValues);
					break;
				case static::MULTIPLE_MODE_MAX:
					$obMaxValue = null;
					foreach($arValues as $obValue){
						if(is_null($obMaxValue) || $obValue->getValue() > $obMaxValue->getValue()){
							$obMaxValue = $obValue;
						}
					}
					$arResult = $obMaxValue;
					break;
				case static::MULTIPLE_MODE_MIN:
					$obMinValue = null;
					foreach($arValues as $obValue){
						if(is_null($obMinValue) || $obValue->getValue() < $obMinValue->getValue()){
							$obMinValue = $obValue;
						}
					}
					$arResult = $obMinValue;
					break;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Transform object (or array of object) to value (array of value)
	 */
	protected function transformObjectToValue($arValues, $bMultiple=false, $strMultipleMode=false, $bWithDescription=false){
		$mResult = $this->mObjectValues = false;
		if($bMultiple){
			$mResult = $arValues;
		}
		else{
			$strMultipleMode = $strMultipleMode ? $strMultipleMode : static::MULTIPLE_MODE_FIRST;
			$mResult = $this->cutMultipleValue($arValues, $strMultipleMode);
		}
		if(is_object($mResult)){
			$this->mObjectValues = $mResult;
			$mResult = $this->transformSingleObjectToValue($mResult, $bWithDescription);
		}
		elseif(is_array($mResult)){
			foreach($mResult as $key => $obValue){
				$this->mObjectValues[$key] = $obValue;
				$mResult[$key] = $this->transformSingleObjectToValue($obValue, $bWithDescription);
			}
		}
		return $mResult;
	}
	
	/**
	 *	
	 */
	protected function transformSingleObjectToValue($obValue, $bWithDescription=false){
		$mResult = $obValue->getValue();
		if($bWithDescription){
			$mResult = [
				'VALUE' => $obValue->getValue(),
				'DESCRIPTION' => $obValue->getDescription(),
			];
		}
		return $mResult;
	}
	
	/**
	 *	Wrapper for SelectBoxFromArray()
	 */
	public function selectBox($strName, $arValues, $strSelected=null, $strDefault=null, $strAttr=null, $bSelect2=true){
		return Helper::selectBox($strName, $arValues, $strSelected, $strDefault, $strAttr, null, $bSelect2);
	}
	
	/**
	 *	Is checkbox 'save current values' checked?
	 */
	protected function isSaveValues(){
		return false;
	}
	
	/**
	 *	Is checkbox 'remove duplicates' checked?
	 */
	protected function isRemoveDuplicates(){
		return false;
	}
	
	/**
	 *	Get error text
	 */
	public function getError(){
		return $this->strError;
	}
	
	/**
	 *	Set error text
	 */
	public function setError($strError){
		$this->strError = $strError;
	}
	
}

?>