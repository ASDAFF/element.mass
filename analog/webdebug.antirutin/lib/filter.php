<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

Helper::loadMessages(__FILE__);

/**
 * Class Filter
 */

class Filter {
	
	const LANG_PREFIX = 'WDA_FILTER';
	
	const VALUE_SEPARATOR = '#|#'; // For filter values

	protected $intIBlockId;
	protected $strEntityType;
	protected $intIBlockOffersID;
	protected $intOffersPropertyID;
	protected $strJson;
	protected $arAvailableFields;
	protected $arAvailableOfferFields;
	protected $strInputName;
	protected $arFilter;
	protected $bIncludeSubsections;
	protected $arSectionsId;

	protected $bDefaultLogicIsAnd = false;
	
	protected $arAllLogicCache = [];
	
	public function __construct($intIBlockId, $strEntityType){
		$this->intIBlockId = $intIBlockId;
		$this->strEntityType = $strEntityType;
		$this->arAvailableFields = IBlock::getAvailableFieldsPlain($intIBlockId, $strEntityType);
		$this->bIncludeSubsections = false;
		if($strEntityType == Helper::TYPE_ELEMENT){
			$arCatalogArray = Helper::getCatalogArray($intIBlockId);
			if(is_array($arCatalogArray) && $arCatalogArray['OFFERS_IBLOCK_ID']) {
				$this->intIBlockOffersID = $arCatalogArray['OFFERS_IBLOCK_ID'];
				$this->intOffersPropertyID = $arCatalogArray['OFFERS_PROPERTY_ID'];
				$this->arAvailableOfferFields = IBlock::getAvailableFieldsPlain($arCatalogArray['OFFERS_IBLOCK_ID'],
					$strEntityType);
			}
		}
		static::addJs();
	}
	
	public static function getMessage($strMessage, $arReplace=null){
		$strPrefix = 'WDA_FILTER';
		return Helper::getMessage($strMessage, $strPrefix, $arReplace);
	}
	
	public static function addJs(){
		global $APPLICATION;
		\Bitrix\Main\Page\Asset::getInstance()->AddJs('/bitrix/js/'.WDA_MODULE.'/filter.js');
		ob_start();
		?>
		<script>
		BX.message({
			'WDA_CONDITIONS_VALUE_SEPARATOR': '<?=static::VALUE_SEPARATOR;?>',
			// Popup
			'WDA_FILTER_POPUP_LOADING': '<?=static::getMessage('POPUP_LOADING');?>',
			'WDA_FILTER_POPUP_SELECT_FIELD': '<?=static::getMessage('POPUP_SELECT_FIELD');?>',
			'WDA_FILTER_POPUP_SELECT_LOGIC': '<?=static::getMessage('POPUP_SELECT_LOGIC');?>',
			'WDA_FILTER_POPUP_SELECT_VALUE': '<?=static::getMessage('POPUP_SELECT_VALUE');?>',
			'WDA_FILTER_POPUP_SAVE': '<?=static::getMessage('POPUP_SAVE');?>',
			'WDA_FILTER_POPUP_CANCEL': '<?=static::getMessage('POPUP_CANCEL');?>',
			// Data
			'WDA_FILTER_ADD_ITEM': '<?=static::getMessage('ADD_ITEM');?>',
			'WDA_FILTER_ADD_GROUP': '<?=static::getMessage('ADD_GROUP');?>',
			'WDA_FILTER_ENTITY_FIELD': '<?=static::getMessage('ENTITY_FIELD');?>',
			'WDA_FILTER_ENTITY_LOGIC': '<?=static::getMessage('ENTITY_LOGIC');?>',
			'WDA_FILTER_ENTITY_VALUE': '<?=static::getMessage('ENTITY_VALUE');?>',
			'WDA_FILTER_AGGREGATOR_ALL': '<?=static::getMessage('AGGREGATOR_ALL');?>',
			'WDA_FILTER_AGGREGATOR_ANY': '<?=static::getMessage('AGGREGATOR_ANY');?>',
			'WDA_FILTER_AGGREGATOR_Y': '<?=static::getMessage('AGGREGATOR_Y');?>',
			'WDA_FILTER_AGGREGATOR_N': '<?=static::getMessage('AGGREGATOR_N');?>',
			'WDA_FILTER_DELETE_ITEM': '<?=static::getMessage('DELETE_ITEM');?>',
			'WDA_FILTER_DELETE_GROUP': '<?=static::getMessage('DELETE_GROUP');?>',
			// Other
			'WDA_FILTER_BUTTON_FILL_LIST_FROM_TEXT': '<?=static::getMessage('BUTTON_FILL_LIST_FROM_TEXT');?>',
			'WDA_FILTER_BUTTON_REMOVE_ALL_VALUES': '<?=static::getMessage('BUTTON_REMOVE_ALL_VALUES');?>',
			'WDA_FILTER_BUTTON_REMOVE_ALL_VALUES_CONFIRM': '<?=static::getMessage('BUTTON_REMOVE_ALL_VALUES_CONFIRM');?>',
			'WDA_FILTER_POPUP_FILL_LIST_FROM_TEXT': '<?=static::getMessage('POPUP_FILL_LIST_FROM_TEXT');?>'
		});
		var wdaFilterLang = {
			addItem: BX.message('WDA_FILTER_ADD_ITEM'),
			addGroup: BX.message('WDA_FILTER_ADD_GROUP'),
			//
			selectField: BX.message('WDA_FILTER_ENTITY_FIELD'),
			selectLogic: BX.message('WDA_FILTER_ENTITY_LOGIC'),
			selectValue: BX.message('WDA_FILTER_ENTITY_VALUE'),
			//
			aggregatorAll: BX.message('WDA_FILTER_AGGREGATOR_ALL'),
			aggregatorAny: BX.message('WDA_FILTER_AGGREGATOR_ANY'),
			aggregatorY: BX.message('WDA_FILTER_AGGREGATOR_Y'),
			aggregatorN: BX.message('WDA_FILTER_AGGREGATOR_N'),
			//
			deleteItemConfirm: BX.message('WDA_FILTER_DELETE_ITEM'),
			deleteGroupConfirm: BX.message('WDA_FILTER_DELETE_GROUP')
		};
		</script>
		<?
		\Bitrix\Main\Page\Asset::GetInstance()->addString(ob_get_clean());
	}
	
	/**
	 *	Get conditions json
	 */
	public function getConditionsJson($intIBlockId, $arItems, $strEntityType){
		if(is_array($arItems)) {
			$arAvailableFields = IBlock::getAvailableFieldsPlain($intIBlockId, $strEntityType);
			$arXmlItems = array();
			if(is_array($arItems) && isset($arItems['FIELD'])){
				$arItems = array($arItems);
			}
			foreach($arItems as $arItem){
				$strField = $arItem['FIELD'];
				$strLogic = $arItem['LOGIC'];
				$strValue = $arItem['VALUE'];
				$strTitle = $arItem['TITLE'];
				#
				$strValue = !is_null($strValue) ? $strValue : '';
				if(strlen($strValue) && !strlen($strTitle)){
					$strTitle = $strValue;
				}
				#
				$arField = $arAvailableFields[$strField];
				if(is_array($arField)){
					$arLogic = $this->getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
					if(is_array($arLogic)){
						$arXmlItems[] = array(
							'type' => 'item',
							'iblockType' => 'main',
							'field' => array(
								'name' => $arField['NAME'],
								'value' => $strField,
							),
							'logic' => array(
								'name' => $arLogic['NAME'],
								'value' => $strLogic,
								'hide' => $arLogic['HIDE_VALUE'] ? 'Y' : 'N',
							),
							'value' => array(
								'name' => $strTitle,
								'value' => $strValue,
							),
						);
					}
				}
			}
		}
		#
		$arFilterJson = array(
			array(
				'type' => 'group',
				'aggregatorType' => 'ALL',
				'items' => $arXmlItems,
			),
		);
		return Json::encode($arFilterJson);
	}

	/**
	 * Set flag for default logic in filter
	 */
	public function setDefaultLogicAnd($bFlag=true){
		$this->bDefaultLogicIsAnd = !!$bFlag;
	}
	
	/**
	 *	Display HTML
	 */
	public function show(){
		$strFilterUniqID = 'filter_'.randString(8).time();
		?>
		<div class="wda-filter" id="<?=$strFilterUniqID;?>" data-role="filter" data-iblock-id="<?=$this->intIBlockId;?>"></div>
		<input type="hidden" name="<?=$this->strInputName;?>" value="<?=htmlspecialcharsbx($this->strJson);?>" id="<?=$strFilterUniqID;?>_input" />
		<script>
		// Main filter
		$('#<?=$strFilterUniqID;?>').wdaFilter({
			lang: wdaFilterLang,
			field: $('#<?=$strFilterUniqID;?>_input'),
			logic: <?=($this->strEntityType == Helper::TYPE_ELEMENT ? 'true' : 'false');?>,
			group: <?=($this->strEntityType == Helper::TYPE_ELEMENT ? 'true' : 'false');?>,
			callbackClickEntity: wdaConditionsPopupCallbackClickEntity,
			defaultLogicIsAnd: <?=($this->bDefaultLogicIsAnd ? 'true' : 'false');?>
		});
		</script>
		<?
		return $strFilterUniqID;
	}
	
	/**
	 *	Set input name
	 */
	public function setInputName($strInputName){
		$this->strInputName = $strInputName;
	}
	
	/**
	 *	Set saved JSON
	 */
	public function setJson($strJson){
		$this->strJson = $strJson;
	}
	
	/**
	 *	Parse json
	 */
	public function getJsonArray(){
		$strJson = $this->strJson;
		if(!Helper::isUtf()){
			$strJson = Helper::convertEncoding($strJson, 'CP1251', 'UTF-8');
		}
		$arJsonResult = json_decode($strJson, true);
		if(!Helper::isUtf()){
			$arJsonResult = Helper::convertEncoding($arJsonResult, 'UTF-8', 'CP1251');
		}
		return $arJsonResult;
	}
	
	/**
	 *	Set include_subsections mode for filtering
	 */
	public function setIncludeSubsections($bIncludeSubsections){
		$this->bIncludeSubsections = $bIncludeSubsections;
	}
	
	/**
	 *	Set sections ID [array]
	 */
	public function setSectionsId($arSectionsId){
		$this->arSectionsId = $arSectionsId;
	}
	
	/**
	 *	
	 */
	public static function getDatetimeFilterValues($bWithTime=true){
		$arResult = [
			'days' => static::getMessage('DATETIME_DAYS'),
			'months' => static::getMessage('DATETIME_MONTHS'),
			'years' => static::getMessage('DATETIME_YEARS'),
			'hours' => static::getMessage('DATETIME_HOURS'),
			'minutes' => static::getMessage('DATETIME_MINUTES'),
			'seconds' => static::getMessage('DATETIME_SECONDS'),
		];
		if(!$bWithTime){
			unset($arResult['hours'], $arResult['minutes'], $arResult['seconds']);
		}
		return $arResult;
	}

	/**
	 * Convert datetime value
	 * MySQL format used just for element properites, in all other cases used site format:
	 * for elements: date_create, timestamp_x, active_from, active_to
	 * for sections: date_create, timestamp_x, properties
	 */
	public static function convertDatetime(&$strField, &$strValue){
		# 1. Change fields
		$arChange = [
			'ACTIVE_FROM' => 'DATE_ACTIVE_FROM',
			'ACTIVE_TO' => 'DATE_ACTIVE_TO',
		];
		if(isset($arChange[$strField])){
			$strField = $arChange[$strField];
		}
		# 2. Replace 1day, 5month, ...
		$strValue = static::parseDatetimeValue($strValue, $strField);
		# 3. Format datetime (just for props)
		if(IBlock::isProperty($strField)){
			$obDate = new \Bitrix\Main\Type\DateTime($strValue);
			$strFormat = 'Y-m-d H:i:s';
			$strValue = $obDate->format($strFormat);
			unset($obDate);
		}
	}
	
	/**
	 *	Parse datetime value (for example, 1day)
	 *	Returns value is site format
	 */
	public static function parseDatetimeValue($strValue, $strField, $bReturnArray=false){
		if(preg_match('#^(\d+)([a-z]+)$#', $strValue, $arMatch)){
			$strDatetime = false;
			$strValue = $arMatch[1];
			$strType = $arMatch[2];
			if(strlen($strType)){
				$arTypes = static::getDatetimeFilterValues();
				if(isset($arTypes[$strType])){
					$obDate = new \Bitrix\Main\Type\DateTime();
					$strDiff = sprintf('- %d %s', $strValue, $strType);
					$obDate->add($strDiff);
					$strFormat = \Bitrix\Main\Type\DateTime::convertFormatToPhp(FORMAT_DATETIME);
					$strDatetime = $obDate->format($strFormat);
				}
			}
			if($bReturnArray){
				return [
					$strDatetime,
					$strValue,
					$strType,
				];
			}
			else{
				return $strDatetime;
			}
		}
		if($bReturnArray){
			return [];
		}
		return $strValue;
	}
	
	/**
	 *	Get logic for values
	 */
	public function getLogicAll($strType, $strUserType=false, $strCurrentField=false){
		if(is_array($this->arAllLogicCache[$strType][$strUserType]) && !empty($this->arAllLogicCache[$strType][$strUserType])){
			return $this->arAllLogicCache[$strType][$strUserType];
		}
		
		$arResult = array();
		
		$arResult = array(
			'EQUAL' => array(
				'NAME' => static::getMessage('LOGIC_EQUAL'),
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return $this->buildFilterItem(array($strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'NOT_EQUAL' => array(
				'NAME' => static::getMessage('LOGIC_NOT_EQUAL'),
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return $this->buildFilterItem(array('!'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'M_EQUAL' => array(
				'NAME' => static::getMessage('LOGIC_M_EQUAL'),
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					$arValue = explode(static::VALUE_SEPARATOR, $strValue);
					Helper::arrayRemoveEmptyValues($arValue);
					return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'M_NOT_EQUAL' => array(
				'NAME' => static::getMessage('LOGIC_M_NOT_EQUAL'),
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					$arValue = explode(static::VALUE_SEPARATOR, $strValue);
					Helper::arrayRemoveEmptyValues($arValue);
					return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'ISSET' => array(
				'NAME' => static::getMessage('LOGIC_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return $this->buildFilterItem(array('!'.$strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
			'NOT_ISSET' => array(
				'NAME' => static::getMessage('LOGIC_NOT_ISSET'),
				'HIDE_VALUE' => true,
				'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
					return $this->buildFilterItem(array($strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
				},
			),
		);
		
		switch($strType){
			case 'S':
				if($strUserType=='_Checkbox' || $strUserType=='_CheckboxD' || $strUserType=='SASDCheckbox') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => static::getMessage('LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array($strField => 'Y'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => static::getMessage('LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!'.$strField => 'Y'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						)
					);
				}
				elseif($strUserType=='directory') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				elseif($strUserType=='Date' || $strUserType=='DateTime') {
					$arResult = array(
						'EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array($strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_NOT_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('!'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'ISSET' => array(
							'NAME' => static::getMessage('LOGIC_ISSET'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('!'.$strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_ISSET' => array(
							'NAME' => static::getMessage('LOGIC_NOT_ISSET'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array($strField => false), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS' => array(
							'NAME' => static::getMessage('LOGIC_LESS'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => static::getMessage('LOGIC_MORE'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return $this->buildFilterItem(array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'FOR_THE_LAST' => array(
							'NAME' => static::getMessage('LOGIC_FOR_THE_LAST'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return static::buildFilterItem(array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_FOR_THE_LAST' => array(
							'NAME' => static::getMessage('LOGIC_NOT_FOR_THE_LAST'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockID, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								static::convertDatetime($strField, $strValue);
								return static::buildFilterItem(array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					);
				}
				elseif($strUserType=='_OffersFlag'){
					$arResult = array(
						'WITH_OFFERS' => array(
							'NAME' => static::getMessage('LOGIC_WITH_OFFERS'),
							'MULTIPLE' => true,
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
								if(!$bIsOffers && $intIBlockOffersID){
									return $this->buildFilterItem(array(), true, $intIBlockOffersID, $intOffersPropertyID, false);
								}
							},
						),
						'WITHOUT_OFFERS' => array(
							'NAME' => static::getMessage('LOGIC_WITHOUT_OFFERS'),
							'MULTIPLE' => true,
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false){
								if(!$bIsOffers && $intIBlockOffersID){
									return $this->buildFilterItem(array(), true, $intIBlockOffersID, $intOffersPropertyID, true);
								}
							},
						),
					);
				}
				else {
					$arNewResult = array(
						'EXACT' => array(
							'NAME' => static::getMessage('LOGIC_EXACT'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_EXACT' => array(
							'NAME' => static::getMessage('LOGIC_NOT_EXACT'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_EXACT' => array(
							'NAME' => static::getMessage('LOGIC_M_EXACT'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('='.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_NOT_EXACT' => array(
							'NAME' => static::getMessage('LOGIC_M_NOT_EXACT'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!='.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'SUBSTRING' => array(
							'NAME' => static::getMessage('LOGIC_SUBSTRING'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('%'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_SUBSTRING' => array(
							'NAME' => static::getMessage('LOGIC_NOT_SUBSTRING'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!%'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_SUBSTRING' => array(
							'NAME' => static::getMessage('LOGIC_M_SUBSTRING'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('%'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_NOT_SUBSTRING' => array(
							'NAME' => static::getMessage('LOGIC_M_NOT_SUBSTRING'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!%'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'BEGINS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_BEGINS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array($strField => $strValue.'%'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_BEGINS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_NOT_BEGINS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!'.$strField => $strValue.'%'), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_BEGINS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_M_BEGINS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								$arValue = array_map(function($strItem){
									return $strItem.'%';
								}, $arValue);
								return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_NOT_BEGINS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_M_NOT_BEGINS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								$arValue = array_map(function($strItem){
									return $strItem.'%';
								}, $arValue);
								return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'ENDS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_ENDS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array($strField => '%'.$strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_ENDS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_NOT_ENDS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!'.$strField => '%'.$strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_ENDS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_M_ENDS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								$arValue = array_map(function($strItem){
									return '%'.$strItem;
								}, $arValue);
								return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_NOT_ENDS_WITH' => array(
							'NAME' => static::getMessage('LOGIC_M_NOT_ENDS_WITH'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								$arValue = array_map(function($strItem){
									return '%'.$strItem;
								}, $arValue);
								return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LOGIC' => array(
							'NAME' => static::getMessage('LOGIC_LOGIC'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('?'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_LOGIC' => array(
							'NAME' => static::getMessage('LOGIC_NOT_LOGIC'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('!?'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_LOGIC' => array(
							'NAME' => static::getMessage('LOGIC_M_LOGIC'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('?'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'M_NOT_LOGIC' => array(
							'NAME' => static::getMessage('LOGIC_M_NOT_LOGIC'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!?'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS' => array(
							'NAME' => static::getMessage('LOGIC_LESS'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => static::getMessage('LOGIC_MORE'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					);
					if($strUserType == '_List'){
						foreach($arNewResult as $key => $value){
							if(!in_array($key, ['IN_LIST', 'NOT_IN_LIST'])){
								unset($arNewResult[$key]);
							}
						}
					}
					if($this->strEntityType == Helper::TYPE_SECTION){
						unset($arNewResult['M_LOGIC'], $arNewResult['M_NOT_LOGIC']);
					}
					if($this->strEntityType == Helper::TYPE_SECTION && !IBlock::isSectionD7Available()){
						foreach($arNewResult as $key => $value){
							if(!in_array($key, ['IN_LIST', 'NOT_IN_LIST'])){
								unset($arNewResult[$key]);
							}
						}
					}
					$arResult = array_merge($arResult, $arNewResult);
				}
				break;
			case 'N':
				if($strUserType=='SASDCheckboxNum') {
					$arResult = array(
						'CHECKED' => array(
							'NAME' => static::getMessage('LOGIC_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array($strField => 1), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_CHECKED' => array(
							'NAME' => static::getMessage('LOGIC_NOT_CHECKED'),
							'HIDE_VALUE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array($strField => 2), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						)
					);
				}
				else {
					$arResult = array_merge($arResult, array(
						'LESS' => array(
							'NAME' => static::getMessage('LOGIC_LESS'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'LESS_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_LESS_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE' => array(
							'NAME' => static::getMessage('LOGIC_MORE'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'MORE_OR_EQUAL' => array(
							'NAME' => static::getMessage('LOGIC_MORE_OR_EQUAL'),
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								return $this->buildFilterItem(array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				if($strUserType=='_ID_LIST') {
					$arResult = array_merge($arResult, array(
						'IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
						'NOT_IN_LIST' => array(
							'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
							'MULTIPLE' => true,
							'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
								$arValue = explode(static::VALUE_SEPARATOR, $strValue);
								Helper::arrayRemoveEmptyValues($arValue);
								return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
							},
						),
					));
				}
				break;
			case 'L':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'E':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'G':
				$arResult = array_merge($arResult, array(
					'IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array($strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'NOT_IN_LIST' => array(
						'NAME' => static::getMessage('LOGIC_NOT_IN_LIST'),
						'MULTIPLE' => true,
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							$arValue = explode(static::VALUE_SEPARATOR, $strValue);
							Helper::arrayRemoveEmptyValues($arValue);
							return $this->buildFilterItem(array('!'.$strField => $arValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
			case 'F':
				$arExclude = array('ISSET','NOT_ISSET');
				foreach($arResult as $key => $arItem){
					if(!in_array($key, $arExclude)){
						unset($arResult[$key]);
					}
				}
				$arResult = array_merge($arResult, array(
					'LESS' => array(
						'NAME' => static::getMessage('LOGIC_LESS'),
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							return $this->buildFilterItem(array('<'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'LESS_OR_EQUAL' => array(
						'NAME' => static::getMessage('LOGIC_LESS_OR_EQUAL'),
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							return $this->buildFilterItem(array('<='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'MORE' => array(
						'NAME' => static::getMessage('LOGIC_MORE'),
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							return $this->buildFilterItem(array('>'.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
					'MORE_OR_EQUAL' => array(
						'NAME' => static::getMessage('LOGIC_MORE_OR_EQUAL'),
						'CALLBACK' => function($strField, $strLogic, $strValue, $intIBlockId, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID){
							return $this->buildFilterItem(array('>='.$strField => $strValue), $bIsOffers, $intIBlockOffersID, $intOffersPropertyID);
						},
					),
				));
				break;
		}
		$this->arAllLogicCache[$strType][$strUserType] = $arResult;
		$this->correctLogicListForField($arResult, $strType, $strUserType, $strCurrentField);
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected function correctLogicListForField(&$arLogicList, $strType, $strUserType, $strField){
		$arUnset = [];
		if($strField == 'ID'){
			$arUnset = array_merge($arUnset, ['ISSET', 'NOT_ISSET']);
		}
		elseif(in_array($strField, ['CATALOG_MEASURE_ID', 'CATALOG_VAT_ID', 'CATALOG_TYPE'])){
			$arUnset = array_merge($arUnset, ['IN_LIST', 'NOT_IN_LIST', 'LESS', 'LESS_OR_EQUAL', 'MORE', 'MORE_OR_EQUAL']);
		}
		foreach($arUnset as $key){
			if(isset($arLogicList[$key])){
				unset($arLogicList[$key]);
			}
		}
	}
	
	/**
	 *	Get logic item
	 */
	public function getLogicItem($strType, $strUserType, $strLogic){
		$arLogicAll = $this->getLogicAll($strType, $strUserType);
		return $arLogicAll[$strLogic];
	}
	
	/**
	 *	Build PHP filter
	 */
	public function buildFilter($bPrettyPrint=false){
		$this->arFilter = [];
		if($this->intIBlockId) {
			$this->arFilter['IBLOCK_ID'] = intVal($this->intIBlockId);
		}
		if(is_array($this->arSectionsId) && !empty($this->arSectionsId)){
			if($this->strEntityType == Helper::TYPE_ELEMENT){
				$this->arFilter['SECTION_ID'] = $this->arSectionsId;
				if($this->bIncludeSubsections){
					$this->arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
				}
			}
			elseif($this->strEntityType == Helper::TYPE_SECTION){
				if(IBlock::isSectionD7Available()){
					$this->arFilter['IBLOCK_SECTION_ID'] = $this->arSectionsId;
					if($this->bIncludeSubsections && !empty($this->arSectionsId)){
						$arMargins = [];
						$arSectionsFilterTmp = [
							'IBLOCK_ID' => intVal($this->intIBlockId),
							'ID' => $this->arSectionsId,
						];
						$arSelect = ['ID', 'LEFT_MARGIN', 'RIGHT_MARGIN'];
						$resSectionsTmp = \CIBlockSection::getList([], $arSectionsFilterTmp, false, $arSelect);
						while($arSectionTmp = $resSectionsTmp->fetch()){
							$arMargins[] = [
								'>LEFT_MARGIN' => $arSectionTmp['LEFT_MARGIN'],
								'<RIGHT_MARGIN' => $arSectionTmp['RIGHT_MARGIN'],
							];
						}
						if(!empty($arMargins)){
							$this->arFilter[] = array_merge(['LOGIC' => 'OR'], $arMargins);
							unset($this->arFilter['IBLOCK_SECTION_ID']);
						}
					}
				}
				else{
					$this->arFilter['ID'] = $this->arSectionsId;
				}
			}
		}
		elseif($this->bIncludeSubsections && $this->strEntityType == Helper::TYPE_ELEMENT){
			$this->arFilter['IBLOCK_SECTION_ID'] = false;
			$this->arFilter['INCLUDE_SUBSECTIONS'] = 'N';
		}
		$arJson = $this->getJsonArray();
		$this->appendFilter($arJson, $this->arFilter);
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers(WDA_MODULE, 'OnBuildFilter') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$this->arFilter, $this));
		}
		if($bPrettyPrint){
			$arFilterPrint = $this->arFilter;
			$this->formatFilterArray($arFilterPrint);
			return $arFilterPrint;
		}
		return $this->arFilter;
	}
	
	protected function formatFilterArray(&$mFilter, $intDepth=1){
		if(is_array($mFilter)){
			foreach($mFilter as &$mFilterItem){
				$this->formatFilterArray($mFilterItem, $intDepth+1);
			}
		}
		elseif(is_object($mFilter) && get_class($mFilter) == 'CIBlockElement'){
			$strSubQuery = print_r($mFilter->arFilter, true);
			$strOffset = str_repeat(' ', ($intDepth + 2) * 4);
			$strSubQuery = preg_replace('#^(.*?)$#m', $strOffset.'$1', $strSubQuery);
			$strSubQuery = 'SubQuery: '.ltrim($strSubQuery);
			$mFilter = $strSubQuery;
		}
	}
	
	/**
	 *	Check count of elements by filtering
	 */
	public function checkCount(){
		$intResult = 0;
		$arFilter = $this->buildFilter();
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			switch($this->strEntityType){
				case Helper::TYPE_ELEMENT:
					$intResult = IBlock::getElementCount($arFilter);
					break;
				case Helper::TYPE_SECTION:
					$intResult = IBlock::getSectionCount($arFilter);
					break;
			}
		}
		return $intResult;
	}
	
	/**
	 *	Append filter (this function will work recursively)
	 */
	protected function appendFilter($arJsonItems, &$arFilter){
		$bTypeElement = $this->strEntityType == Helper::TYPE_ELEMENT;
		$bTypeSection = $this->strEntityType == Helper::TYPE_SECTION;
		if(is_array($arJsonItems)) {
			foreach($arJsonItems as $key => $arJsonItem){
				if($arJsonItem['type']=='group') {
					if(is_array($arJsonItem['items']) && !empty($arJsonItem['items'])) {
						$arSubFilter = [];
						if($bTypeElement){
							$arSubFilter['LOGIC'] = $arJsonItem['aggregatorType']=='ANY' ? 'OR' : 'AND';
							$this->appendFilter($arJsonItem['items'], $arSubFilter);
							if(!(count($arSubFilter)==1 && isset($arSubFilter['LOGIC']))){
								$arFilter[] = $arSubFilter;
							}
						}
						elseif($bTypeSection){
							$this->appendFilter($arJsonItem['items'], $arSubFilter);
							if(!(count($arSubFilter)==1 && isset($arSubFilter['LOGIC']))){
								$arFilter = array_merge_recursive($arFilter, $arSubFilter);
							}
						}
					}
				}
				elseif($arJsonItem['type']=='item'){
					$arFilterItem = array();
					$bIsOffers = $arJsonItem['iblockType']=='offers' ? true : false;
					$strField = $arJsonItem['field']['value'];
					$strFilterField = $this->applyCustomFilterKey($strField);
					if($bIsOffers) {
						$arField = $this->arAvailableOfferFields[$strField];
					}
					else {
						$arField = $this->arAvailableFields[$strField];
					}
					if(is_array($arField)) {
						$strLogic = $arJsonItem['logic']['value'];
						$arLogic = $this->getLogicItem($arField['TYPE'], $arField['USER_TYPE'], $strLogic);
						$strValue = $arJsonItem['value']['value'];
						if($arLogic['CALLBACK']) {
							$arFilterItem = call_user_func_array($arLogic['CALLBACK'], array(
								$strFilterField, $strLogic, $strValue, $this->intIBlockId, $bIsOffers,
									$this->intIBlockOffersID, $this->intOffersPropertyID
							));
						}
						if(is_array($arFilterItem) && !empty($arFilterItem)) {
							if($bTypeElement){
								$arFilter[] = $arFilterItem;
							}
							elseif($bTypeSection){
								$arFilter = array_merge_recursive($arFilter, $arFilterItem);
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Use custom key for filter
	 */
	protected function applyCustomFilterKey($strField){
		$arField = $this->arAvailableFields[$strField];
		if(is_array($arField) && strlen($arField['CUSTOM_FILTER_KEY'])){
			return $arField['CUSTOM_FILTER_KEY'];
		}
		return $strField;
	}
	
	/**
	 *	Build filter item (this use in each logic item)
	 */
	protected function buildFilterItem($arItem, $bIsOffers=false, $intIBlockOffersID=false, $intOffersPropertyID=false, $bNegation=false){
		$arResult = array();
		if($bIsOffers && $this->strEntityType == Helper::TYPE_ELEMENT){
			$strKey = $bNegation ? '!ID' : 'ID';
			$arResult = array(
				$strKey => \CIBlockElement::subQuery('PROPERTY_'.$intOffersPropertyID, array_merge($arItem, array(
					'IBLOCK_ID' => $intIBlockOffersID,
				))),
			);
		}
		else {
			// Prepare was added 2022-04-22 (bug with filtering by multiple GENDER (property type=L): male and not female)
			static::prepareMultipleItemForBuildFilterItem($arItem);
			$arResult = $arItem;
		}
		// Event handler
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers(WDA_MODULE, 'OnBuildFilterItem') as $arHandler) {
			ExecuteModuleEventEx($arHandler, array(&$arResult, $arItem, $bIsOffers, $intIBlockOffersID, $intOffersPropertyID, $bNegation));
		}
		return $arResult;
	}

	protected function prepareMultipleItemForBuildFilterItem(&$arItem){
		if(is_array($arItem) && count($arItem)){
			$strFieldWithOperation = key($arItem);
			$strFieldCleared = $strFieldWithOperation;
			$strOperation = '';
			if(preg_match('#^([<>\!\?=%]+)(.*?)$#', $strFieldWithOperation, $arMatch)){
				$strOperation = $arMatch[1];
				$strFieldCleared = $arMatch[2];
			}
			$bNegative = strpos($strOperation, '!') !== false;
			if($intPropId = IBlock::isProperty($strFieldCleared)){
				if($bNegative){
					$arItem = [
						str_replace('!', '', $strOperation.$strFieldCleared) => $arItem[$strFieldWithOperation],
					];
				}
				static $arProperties = [];
				$arProp = &$arProperties[$intPropId];
				if(!is_array($arProp)){
					$arProp = \CIBlockProperty::getList([], ['ID' => $intPropId])->fetch();
				}
				if(is_array($arProp) /* && $arProp['MULTIPLE'] == 'Y' */){
					$arFilter = array_merge(['IBLOCK_ID' => $arProp['IBLOCK_ID']], $arItem);
					$arResult = [($bNegative ? '!' : '').'ID' => \CIBlockElement::subQuery('ID', $arFilter)];
					$arItem = $arResult;
				}
			}
		}
	}
	
	/**
	 *	Just for conditions_value.php
	 */
	public function getPropertyItems_L($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$arFilter = array(
			'IBLOCK_ID' => $intFieldIBlockID,
			'PROPERTY_ID' => $arProperty['DATA']['ID'],
		);
		$resEnums = \CIBlockPropertyEnum::GetList(array('SORT'=>'ASC'), $arFilter);
		while($arEnum = $resEnums->getNext()){
			if(in_array($arEnum['ID'], $arValues)) {
				$arResult[$arEnum['ID']] = $arEnum['VALUE'].' ['.$arEnum['ID'].']';
			}
		}
		unset($arFilter, $resEnums, $arEnum);
		return $arResult;
	}
	public function getPropertyItems_E($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
			);
			$resItems = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array('ID', 'NAME'));
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public function getPropertyItems_G($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = array(
				'ID' => $arValues,
				'CHECK_PERMISSIONS' => 'N',
			);
			$resItems = \CIBlockSection::GetList(array('ID' => 'ASC'), $arFilter, false, array('ID', 'NAME'), false);
			while($arItem = $resItems->GetNext()){
				$arResult[IntVal($arItem['ID'])] = $arItem['~NAME'].' ['.$arItem['ID'].']';
			}
		}
		unset($resItems, $arItem, $arFilter);
		return $arResult;
	}
	public function getPropertyItems_S_directory($arValues, $arProperty=array(), $arParams=array()){
		$arResult = array();
		$strHlTableName = $arProperty['DATA']['USER_TYPE_SETTINGS']['TABLE_NAME'];
		if(!empty($arValues) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
			if(strlen($strHlTableName)){
				$arFilter = array(
					'UF_XML_ID' => $arValues,
				);
				$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME'=>$strHlTableName)))->fetch();
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				$strEntityDataClass = $obEntity->getDataClass();
				$resData = $strEntityDataClass::GetList(array(
					'filter' => $arFilter,
					'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
					'order' => array('ID' => 'ASC'),
				));
				while ($arItem = $resData->Fetch()) {
					$arResult[$arItem['UF_XML_ID']] = $arParams['ARRAY'] === true ? $arItem : $arItem['UF_NAME'];
				}
			}
		}
		unset($strHlTableName, $arFilter, $arHLBlock, $obEntity, $strEntityDataClass, $resData, $arItem);
		return $arResult;
	}
	
	/**
	 *	Search sections
	 *	(because CIBlockSection does not support LOGIC OR)
	 *	ToDo: make it with D7
	 */
	public function searchSectionsByText($intIBlockId, $strSearch){
		$intIBlockId = IntVal($intIBlockId);
		$arWhere = array();
		if(strlen($strSearch)){
			$arWhere[] = "(BS.CODE IS NULL OR (BS.CODE LIKE '%{$strSearch}%'))";
			$arWhere[] = "(BS.NAME LIKE '%{$strSearch}%')";
			if(is_numeric($strSearch) && $strSearch > 0){
				$intID = IntVal($intID);
				$arWhere[] = "BS.ID = {$intID}";
			}
		}
		$strWhere = "(BS.IBLOCK_ID = '{$intIBlockId}')";
		if(!empty($arWhere)){
			$strWhere .= ' AND '.implode(' OR ', $arWhere);
		}
		$strSql = "
			SELECT
				DISTINCT BS.ID AS ID, BS.NAME AS NAME
			FROM
				b_iblock_section BS
			INNER JOIN
				b_iblock B ON BS.IBLOCK_ID = B.ID
			WHERE
				{$strWhere};
		";
		return $GLOBALS['DB']->query($strSql);
	}
	
}
