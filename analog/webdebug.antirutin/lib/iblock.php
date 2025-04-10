<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\Bitrix\Highloadblock\HighloadBlockTable as HighloadBlock;

Helper::loadMessages(__FILE__);

/**
 * Class IBlock
 */

class IBlock {
	
	const LANG_PREFIX = 'WDA_FILTER';
	
	const TYPE_FIELD = 'FIELD';
	const TYPE_PROPERTY = 'PROPERTY';
	const TYPE_SEO = 'SEO';
	const TYPE_IBLOCK = 'IBLOCK';
	const TYPE_SECTION = 'SECTION';
	const TYPE_CATALOG = 'CATALOG';
	const TYPE_PRICE = 'PRICE';
	
	const PREFIX_OFFERS = 'OFFERS.';
	
	protected static $arCache = [];
	
	const CACHE_AVAILABLE_FIELDS = 'AVAILABLE_FIELDS';
	const CACHE_IBLOCK_PARAMS = 'IBLOCK_PARAMS';
	const CACHE_IBLOCK_PICTURES = 'IBLOCK_PICTURES';
	const CACHE_HIGHLOAD_BLOCKS = 'HIGHLOAD_BLOCKS';
	const CACHE_HIGHLOAD_BLOCKS2 = 'HIGHLOAD_BLOCKS2';
	const CACHE_PRODUCT_GROUPS = 'PRODUCT_GROUP';
	
	static $arSeoMapElement = [
		'TITLE' => 'ELEMENT_META_TITLE',
		'KEYWORDS' => 'ELEMENT_META_KEYWORDS',
		'DESCRIPTION' => 'ELEMENT_META_DESCRIPTION',
		'H1' => 'ELEMENT_PAGE_TITLE',
	];
	
	static $arSeoMapSection = [
		'TITLE' => 'SECTION_META_TITLE',
		'KEYWORDS' => 'SECTION_META_KEYWORDS',
		'DESCRIPTION' => 'SECTION_META_DESCRIPTION',
		'H1' => 'SECTION_PAGE_TITLE',
	];
	
	/**
	 *	
	 */
	public static function getMessage($strMessage, $arReplace=null){
		$strPrefix = 'WDA_IBLOCK_FIELD';
		return Helper::getMessage($strMessage, $strPrefix, $arReplace);
	}
		
	/**
	 *	Get all fields for selected iblock
			Some examples:
			1.
			protected $arFieldsFilter = [
				'FIELDS' => ['TYPE_FULL' => ['S', 'N']],
				'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:FileMan', 'S:ElementXmlID', 'S:directory', 'N']],
			];
			2.
			protected $arFieldsFilter = [
				'FIELDS' => ['IS_WRITEABLE' => 'Y', '!CODE' => 'IBLOCK_SECTION_ID'],
				'PROPERTIES' => ['IS_WRITEABLE' => 'Y'],
				'SEO' => ['IS_WRITEABLE' => 'Y'],
				'CATALOG' => ['IS_WRITEABLE' => 'Y'],
				'PRICES' => ['IS_WRITEABLE' => 'Y'],
			];
	 */
	public static function getAvailableFields($intIBlockId, $strEntityType, $arFilter=null){
		$arResult = [];
		if($strEntityType == Helper::TYPE_ELEMENT){
			$arResult = static::getAvailableElementFields($intIBlockId, $arFilter);
		}
		elseif($strEntityType == Helper::TYPE_SECTION){
			$arResult = static::getAvailableSectionFields($intIBlockId, $arFilter);
		}
		return $arResult;
	}
	
	/**
	 *	Get all fields for selected iblock
	 */
	public static function getAvailableFieldsPlain($intIBlockId, $strEntityType, $arFilter=null){
		$arResult = array();
		$arAvailableFields = static::getAvailableFields($intIBlockId, $strEntityType, $arFilter);
		foreach($arAvailableFields as $strGroup => $arGroup){
			if(is_array($arGroup['ITEMS'])){
				foreach($arGroup['ITEMS'] as $strItem => $arItem){
					$arItem['GROUP'] = $arGroup['TYPE'];
					$arItem['NAME_PREFIX'] = $arGroup['NAME_PREFIX'];
					$arItem['CATEGORY'] = $strGroup;
					$strKey = strlen($arGroup['PREFIX']) ? $arGroup['PREFIX'].$strItem : $strItem;
					$arResult[$strKey] = $arItem;
				}
			}
		}
		return $arResult;
	}
	
	// *******************************************************************************************************************
	// Field for TYPE_ELEMENT
	// *******************************************************************************************************************
	
	/**
	 *	
	 */
	protected static function getAvailableElementFields($intIBlockId, $arFilter=null){
		$strKey = md5(is_array($arFilter) && !empty($arFilter) ? serialize($arFilter) : '');
		$arResult = &static::$arCache[static::CACHE_AVAILABLE_FIELDS][$intIBlockId][Helper::TYPE_ELEMENT][$strKey];
		if(is_array($arResult) && !empty($arResult)){
			return $arResult;
		}
		$arResult = array(
			'FIELDS' => array(
				'NAME' => static::getMessage('GROUP_FIELDS'),
				'ITEMS' => static::_getAvailableElementFields($intIBlockId),
				'PREFIX' => '', // used for system purposes
				'TYPE' => static::TYPE_FIELD,
			),
			'PROPERTIES' => array(
				'NAME' => static::getMessage('GROUP_PROPERTIES'),
				'ITEMS' => static::_getAvailableElementProperties($intIBlockId),
				'PREFIX' => 'PROPERTY_',
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PROPERTY,
			),
			'SEO' => array(
				'NAME' => static::getMessage('GROUP_SEO'),
				'ITEMS' => static::_getAvailableElementSeoFields($intIBlockId),
				'PREFIX' => 'SEO_',
				'TYPE' => static::TYPE_SEO,
			),
			'SECTION' => array(
				'NAME' => static::getMessage('GROUP_SECTION'),
				'ITEMS' => static::_getAvailableElementSectionFields($intIBlockId),
				'PREFIX' => 'SECTION__',
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_SECTION,
			),
			'IBLOCK' => array(
				'NAME' => static::getMessage('GROUP_IBLOCK'),
				'ITEMS' => static::_getAvailableElementIBlockFields($intIBlockId),
				'PREFIX' => 'IBLOCK__',
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_IBLOCK,
			),
			'CATALOG' => array(
				'NAME' => static::getMessage('GROUP_CATALOG'),
				'ITEMS' => static::_getAvailableElementCatalogFields($intIBlockId),
				'PREFIX' => 'CATALOG_',
				'TYPE' => static::TYPE_CATALOG,
			),
			'PRICES' => array(
				'NAME' => static::getMessage('GROUP_PRICES'),
				'ITEMS' => static::_getAvailableElementPrices($intIBlockId),
				'PREFIX' => 'CATALOG_',
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PRICE,
			),
		);
		static::_getAvailableOfferFields($intIBlockId, $arResult, $arFilter);
		foreach($arResult as $strType => $arType){
			foreach($arType['ITEMS'] as $key => $arItem){
				$strTypeFull = $arItem['TYPE'].(strlen($arItem['USER_TYPE']) ? ':'.$arItem['USER_TYPE'] : '');
				$arResult[$strType]['ITEMS'][$key]['TYPE_FULL'] = $strTypeFull;
				if(is_array($arItem['DATA'])){
					$arResult[$strType]['ITEMS'][$key]['DATA']['TYPE_FULL'] = $strTypeFull;
				}
			}
		}
		if(is_array($arFilter) && !empty($arFilter)){
			$arResult = static::filterFields($arResult, $arFilter);
		}
		foreach($arResult as $strType => $arType){
			if(empty($arResult[$strType]['ITEMS'])){
				unset($arResult[$strType]);
			}
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected static function _getAvailableOfferFields($intIBlockId, &$arResult, &$arFilter){
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		if($arCatalog['OFFERS_IBLOCK_ID']){
			$intOffersIBlockId = $arCatalog['OFFERS_IBLOCK_ID'];
			$strPrefix = static::getMessage('FIELD__PREFIX_OFFER');
			#
			$arResultTmp = [];
			$arResultTmp['OFFERS.FIELDS'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_FIELDS'),
				'ITEMS' => static::_getAvailableElementFields($intOffersIBlockId),
				'PREFIX' => 'OFFERS.',
				'OFFERS' => true,
				'TYPE' => static::TYPE_FIELD,
			];
			$arResultTmp['OFFERS.PROPERTIES'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_PROPERTIES'),
				'ITEMS' => static::_getAvailableElementProperties($intOffersIBlockId),
				'PREFIX' => 'OFFERS.PROPERTY_',
				'OFFERS' => true,
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PROPERTY,
			];
			$arResultTmp['OFFERS.SEO'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_SEO'),
				'ITEMS' => static::_getAvailableElementSeoFields($intOffersIBlockId),
				'PREFIX' => 'OFFERS.SEO_',
				'OFFERS' => true,
				'TYPE' => static::TYPE_SEO,
			];
			$arResultTmp['OFFERS.SECTION'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_SECTION'),
				'ITEMS' => static::_getAvailableElementSectionFields($intOffersIBlockId),
				'PREFIX' => 'OFFERS.SECTION__',
				'OFFERS' => true,
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_SECTION,
			];
			$arResultTmp['OFFERS.IBLOCK'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_IBLOCK'),
				'ITEMS' => static::_getAvailableElementIBlockFields($intOffersIBlockId),
				'PREFIX' => 'OFFERS.IBLOCK__',
				'OFFERS' => true,
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_IBLOCK,
			];
			$arResultTmp['OFFERS.CATALOG'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_CATALOG'),
				'ITEMS' => static::_getAvailableElementCatalogFields($intOffersIBlockId),
				'PREFIX' => 'OFFERS.CATALOG_',
				'OFFERS' => true,
				'TYPE' => static::TYPE_CATALOG,
			];
			$arResultTmp['OFFERS.PRICES'] = [
				'NAME' => $strPrefix.static::getMessage('GROUP_PRICES'),
				'ITEMS' => static::_getAvailableElementPrices($intOffersIBlockId),
				'PREFIX' => 'OFFERS.CATALOG_',
				'OFFERS' => true,
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PRICE,
			];
			// Temporary disable filtering for offers
			foreach($arResultTmp as $key1 => $arGroup){
				foreach($arGroup['ITEMS'] as $key2 => $arKey){
					$arResultTmp[$key1]['ITEMS'][$key2]['IS_FILTRABLE'] = 'N';
				}
			}
			$arResult = array_merge($arResult, $arResultTmp);
		}
	}
	
	/**
	 *	Fields
	 */
	protected static function _getAvailableElementFields($intIBlockId){
		$arResult = [];
		if(is_numeric($intIBlockId) || $intIBlockId === true){
			$arResult = array(
				'ID' => array(
					'NAME' => static::getMessage('FIELD__ID'),
					'TYPE' => 'N',
					'READONLY' => true,
					'USER_TYPE' => '_ID_LIST',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'NAME' => array(
					'NAME' => static::getMessage('FIELD__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DATE_CREATE' => array(
					'NAME' => static::getMessage('FIELD__DATE_CREATE'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'TIMESTAMP_X' => array(
					'NAME' => static::getMessage('FIELD__TIMESTAMP_X'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK_ID'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_SECTION_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK_SECTION_ID'),
					'TYPE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'ACTIVE' => array(
					'NAME' => static::getMessage('FIELD__ACTIVE'),
					'TYPE' => 'S',
					'USER_TYPE' => '_Checkbox',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'ACTIVE_FROM' => array(
					'NAME' => static::getMessage('FIELD__ACTIVE_FROM'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'ACTIVE_TO' => array(
					'NAME' => static::getMessage('FIELD__ACTIVE_TO'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'SORT' => array(
					'NAME' => static::getMessage('FIELD__SORT'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'PREVIEW_PICTURE' => array(
					'NAME' => static::getMessage('FIELD__PREVIEW_PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'PREVIEW_TEXT' => array(
					'NAME' => static::getMessage('FIELD__PREVIEW_TEXT'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'PREVIEW_TEXT_TYPE' => array(
					'NAME' => static::getMessage('FIELD__PREVIEW_TEXT_TYPE'),
					'TYPE' => 'S',
					'USER_TYPE' => '_TextType',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DETAIL_PICTURE' => array(
					'NAME' => static::getMessage('FIELD__DETAIL_PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DETAIL_TEXT' => array(
					'NAME' => static::getMessage('FIELD__DETAIL_TEXT'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DETAIL_TEXT_TYPE' => array(
					'NAME' => static::getMessage('FIELD__DETAIL_TEXT_TYPE'),
					'TYPE' => 'S',
					'USER_TYPE' => '_TextType',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DETAIL_PAGE_URL' => array(
					'NAME' => static::getMessage('FIELD__DETAIL_PAGE_URL'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'CODE' => array(
					'NAME' => static::getMessage('FIELD__CODE'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'XML_ID' => array(
					'NAME' => static::getMessage('FIELD__XML_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'TAGS' => array(
					'NAME' => static::getMessage('FIELD__TAGS'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'SHOW_COUNTER' => array(
					'NAME' => static::getMessage('FIELD__SHOW_COUNTER'),
					'TYPE' => 'N',
					'READONLY' => true,
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'SHOW_COUNTER_START' => array(
					'NAME' => static::getMessage('FIELD__SHOW_COUNTER_START'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'READONLY' => true,
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'CREATED_BY' => array(
					'NAME' => static::getMessage('FIELD__CREATED_BY'),
					'TYPE' => 'N',
					'USER_TYPE' => '_UserId',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'CREATED_BY__NAME' => array(
					'NAME' => static::getMessage('FIELD__CREATED_BY__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'MODIFIED_BY' => array(
					'NAME' => static::getMessage('FIELD__MODIFIED_BY'),
					'TYPE' => 'N',
					'USER_TYPE' => '_UserId',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'MODIFIED_BY__NAME' => array(
					'NAME' => static::getMessage('FIELD__MODIFIED_BY__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				# Workflow
				'WF_COMMENTS' => array(
					'NAME' => static::getMessage('FIELD__WF_COMMENTS'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'WF_STATUS_ID' => array(
					'NAME' => static::getMessage('FIELD__WF_STATUS_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'SHOW_HISTORY' => array(
					'NAME' => static::getMessage('FIELD__SHOW_HISTORY'),
					'TYPE' => 'S',
					'USER_TYPE' => '_Checkbox',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'SHOW_NEW' => array(
					'NAME' => static::getMessage('FIELD__SHOW_NEW'),
					'TYPE' => 'S',
					'USER_TYPE' => '_Checkbox',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'WF_PARENT_ELEMENT_ID' => array(
					'NAME' => static::getMessage('FIELD__WF_PARENT_ELEMENT_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'WF_NEW' => array(
					'NAME' => static::getMessage('FIELD__WF_NEW'),
					'TYPE' => 'S',
					'USER_TYPE' => '_Checkbox',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'WF_LOCK_STATUS' => array(
					'NAME' => static::getMessage('FIELD__WF_LOCK_STATUS'),
					'TYPE' => 'S',
					'USER_TYPE' => '_WfLockStatus',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
					'LIST_VALUES' => [
						'red' => static::getMessage('FIELD__WF_LOCK_STATUS__RED'),
						'green' => static::getMessage('FIELD__WF_LOCK_STATUS__GREEN'),
						'yellow' => static::getMessage('FIELD__WF_LOCK_STATUS__YELLOW'),
					],
				),
			);
		}
		return $arResult;
	}
	
	/**
	 *	Properties
	 */
	protected static function _getAvailableElementProperties($intIBlockId, $bUsePropCode=false){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock')){
			$arSort = array(
				'SORT' => 'ASC',
				'NAME' => 'ASC',
			);
			$arFilter = array(
				'IBLOCK_ID' => $intIBlockId,
				'ACTIVE' => 'Y',
			);
			$resProps = \CIBlockProperty::GetList($arSort, $arFilter);
			while($arProp = $resProps->GetNext(false,false)){
				$strType = $arProp['PROPERTY_TYPE'].(strlen($arProp['USER_TYPE']) ? ':'.$arProp['USER_TYPE'] : '');
				$arPropItem = array(
					'NAME' => $arProp['NAME'],
					'CODE' => $arProp['CODE'],
					'TYPE' => $arProp['PROPERTY_TYPE'],
					'USER_TYPE' => $arProp['USER_TYPE'],
					'ID' => $arProp['ID'],
					'IS_PROPERTY' => true,
					'IS_MULTIPLE' => $arProp['MULTIPLE'] == 'Y' ? 'Y' : 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
					'DATA' => $arProp,
				);
				if(strlen($arProp['USER_TYPE'])){
					$arPropItem['USER_TYPE'] = $arProp['USER_TYPE'];
				}
				$strKey = $bUsePropCode && strlen($arProp['CODE']) ? $arProp['CODE'] : $arProp['ID'];
				$arResult[$strKey] = $arPropItem;
			}
		}
		return $arResult;
	}
	
	/**
	 *	IBlock fields
	 */
	protected static function _getAvailableElementIBlockFields($intIBlockId){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock')){
			$arResult = array(
				'ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__ID'),
					'TYPE' => 'N',
					'READONLY' => true,
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'TIMESTAMP_X' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__TIMESTAMP_X'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_TYPE_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__TYPE_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'LID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__LID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'CODE' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__CODE'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'NAME' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'SORT' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__SORT'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'PICTURE' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'DESCRIPTION' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__DESCRIPTION'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'ELEMENT_NAME' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__ELEMENT_NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'XML_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__XML_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
			);
		}
		return $arResult;
	}
	
	/**
	 *	SEO fields
	 */
	protected static function _getAvailableElementSeoFields($intIBlockId){
		$arResult = [
			'TITLE' => array(
				'NAME' => static::getMessage('FIELD__SEO_TITLE'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'KEYWORDS' => array(
				'NAME' => static::getMessage('FIELD__SEO_KEYWORDS'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'DESCRIPTION' => array(
				'NAME' => static::getMessage('FIELD__SEO_DESCRIPTION'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'H1' => array(
				'NAME' => static::getMessage('FIELD__SEO_H1'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
		];
		return $arResult;
	}
	
	/**
	 *	Section fields
	 */
	protected static function _getAvailableElementSectionFields($intIBlockId){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock')){
			$resIBlock = \CIBlock::getList(array(), array('ID' => $intIBlockId, 'CHECK_PERMISSIONS' => 'N'));
			if($arIBlock = $resIBlock->getNext(false,false)){
				$resIBlockType = \CIBlockType::GetByID($arIBlock['IBLOCK_TYPE_ID']);
				if($arIBlockType = $resIBlockType->getNext(false,false)){
					if($arIBlockType['SECTIONS']=='Y') {
						$arResult = array(
							'ID' => array(
								'NAME' => static::getMessage('FIELD__SECTION__ID'),
								'TYPE' => 'N',
								'READONLY' => true,
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'DATE_CREATE' => array(
								'NAME' => static::getMessage('FIELD__SECTION__DATE_CREATE'),
								'TYPE' => 'S',
								'USER_TYPE' => 'DateTime',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'TIMESTAMP_X' => array(
								'NAME' => static::getMessage('FIELD__SECTION__TIMESTAMP_X'),
								'TYPE' => 'S',
								'USER_TYPE' => 'DateTime',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'SORT' => array(
								'NAME' => static::getMessage('FIELD__SECTION__SORT'),
								'TYPE' => 'N',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'NAME' => array(
								'NAME' => static::getMessage('FIELD__SECTION__NAME'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'PICTURE' => array(
								'NAME' => static::getMessage('FIELD__SECTION__PICTURE'),
								'TYPE' => 'F',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'DETAIL_PICTURE' => array(
								'NAME' => static::getMessage('FIELD__SECTION__DETAIL_PICTURE'),
								'TYPE' => 'F',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'DESCRIPTION' => array(
								'NAME' => static::getMessage('FIELD__SECTION__DESCRIPTION'),
								'TYPE' => 'S',
								'USER_TYPE' => 'HTML',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'CODE' => array(
								'NAME' => static::getMessage('FIELD__SECTION__CODE'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'XML_ID' => array(
								'NAME' => static::getMessage('FIELD__SECTION__XML_ID'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'SEO_TITLE' => array(
								'NAME' => static::getMessage('FIELD__SECTION__SEO_TITLE'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'SEO_KEYWORDS' => array(
								'NAME' => static::getMessage('FIELD__SECTION__SEO_KEYWORDS'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'SEO_DESCRIPTION' => array(
								'NAME' => static::getMessage('FIELD__SECTION__SEO_DESCRIPTION'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
							'SEO_H1' => array(
								'NAME' => static::getMessage('FIELD__SECTION__SEO_H1'),
								'TYPE' => 'S',
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							),
						);
						$arCatalog = Helper::getCatalogArray($intIBlockId);
						if($arCatalog['PRODUCT_IBLOCK_ID']){
							$intIBlockId = $arCatalog['PRODUCT_IBLOCK_ID'];
						}
						$arUserFields = static::getSectionUserFields($intIBlockId);
						foreach($arUserFields as $strFieldCode => $arUserField){
							$strFieldName = '';
							if(is_array($arUserField['EDIT_FORM_LABEL'])) {
								$strFieldName = $arUserField['EDIT_FORM_LABEL'][LANGUAGE_ID];
								if(!strlen($strFieldName)){
									$strFieldName = reset($arUserField['EDIT_FORM_LABEL']);
								}
							}
							if(!strlen($strFieldName)){
								$strFieldName = $strFieldCode;
							}
							/*
							$arTypes = array(
								'integer' => 'N',
								'double' => 'N',
								'date' => 'S', // S:Date
								'datetime' => 'S', // S:DateTime
								'boolean' => 'S',
								'file' => 'F',
								'iblock_section' => 'G',
								'iblock_element' => 'E',
								'hlblock' => 'S', // S:directory
								'enumeration' => 'L',
							);
							$strType = isset($arTypes[$arUserField['USER_TYPE_ID']]) ? $arTypes[$arUserField['USER_TYPE_ID']] : 'S';
							*/
							$strType = static::getSectionFieldType($arUserField['USER_TYPE_ID'], true);
							$arType = explode(':', $strType);
							$strType = $arType[0];
							$strUserType = $arType[1];
							$arResult[$strFieldCode] = array(
								'NAME' => $strFieldName,
								'CODE' => $strFieldCode,
								#'TYPE' => $arUserField['USER_TYPE_ID'],
								'TYPE' => $strType,
								'USER_TYPE' => $strUserType,
								'ID' => $arUserField['ID'],
								'DATA' => $arUserField,
								'IS_FILTRABLE' => 'N',
								'IS_WRITEABLE' => 'N',
							);
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Catalog fields
	 */
	protected static function _getAvailableElementCatalogFields($intIBlockId){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog')){
			$arCatalog = Helper::getCatalogArray($intIBlockId);
			$intIBlockOffersID = IntVal($arCatalog['OFFERS_IBLOCK_ID']);
			if(is_array($arCatalog) && $arCatalog['IBLOCK_ID']==$intIBlockId){
				$arResult = array(
					'QUANTITY' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_QUANTITY'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'QUANTITY' : 'CATALOG_QUANTITY',
					),
					'QUANTITY_RESERVED' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_QUANTITY_RESERVED'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'QUANTITY_RESERVED' : 'CATALOG_QUANTITY_RESERVED',
					),
					'AVAILABLE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_AVAILABLE'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Checkbox',
						'READONLY' => true,
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'N',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'AVAILABLE' : 'CATALOG_AVAILABLE',
					),
					'WEIGHT' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_WEIGHT'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'WEIGHT' : 'CATALOG_WEIGHT',
					),
					'LENGTH' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_LENGTH'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'LENGTH' : 'CATALOG_LENGTH',
					),
					'WIDTH' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_WIDTH'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'WIDTH' : 'CATALOG_WIDTH',
					),
					'HEIGHT' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_HEIGHT'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'HEIGHT' : 'CATALOG_HEIGHT',
					),
					'VAT_ID' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_VAT_ID'),
						'TYPE' => 'N',
						'USER_TYPE' => '_Vat',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'LIST_VALUES' => Helper::getVatList(true),
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'VAT_ID' : 'CATALOG_VAT_ID',
					),
					'VAT_VALUE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_VAT_VALUE'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
					),
					'VAT_INCLUDED' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_VAT_INCLUDED'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Checkbox',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'VAT_INCLUDED' : 'CATALOG_VAT_INCLUDED',
					),
					'PURCHASING_PRICE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_PURCHASING_PRICE'),
						'TYPE' => 'N',
						'USER_TYPE' => '_Price',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'PURCHASING_PRICE' : 'CATALOG_PURCHASING_PRICE',
					),
					'PURCHASING_CURRENCY' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_PURCHASING_CURRENCY'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Currency',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'PURCHASING_CURRENCY' : 'CATALOG_PURCHASING_CURRENCY',
					),
					'MEASURE_ID' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_MEASURE_ID'),
						'TYPE' => 'N',
						'USER_TYPE' => '_Measure',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'MEASURE' : 'CATALOG_MEASURE',
						'LIST_VALUES' => Helper::getMeasuresList(true),
					),
					'MEASURE_UNIT' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_MEASURE_UNIT'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
					),
					'MEASURE_NAME' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_MEASURE_NAME'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
					),
					'MEASURE_RATIO' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_MEASURE_RATIO'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'Y',
					),
					'QUANTITY_TRACE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_QUANTITY_TRACE'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'USER_TYPE' => '_CheckboxD',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'QUANTITY_TRACE_RAW' : 'CATALOG_QUANTITY_TRACE',
					),
					'NEGATIVE_AMOUNT_TRACE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_NEGATIVE_AMOUNT_TRACE'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'USER_TYPE' => '_CheckboxD',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'CAN_BUY_ZERO_RAW' : 'CATALOG_CAN_BUY_ZERO',
					),
					'SUBSCRIBE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_SUBSCRIBE'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'USER_TYPE' => '_CheckboxD',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'SUBSCRIBE_RAW' : 'CATALOG_SUBSCRIBE',
					),
					'TYPE' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_TYPE'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'N',
						'USER_TYPE' => '_CatalogType',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'TYPE' : 'CATALOG_TYPE',
						'LIST_VALUES' => [
							'1' => static::getMessage('FIELD__CATALOG_TYPE_1'),
							'2' => static::getMessage('FIELD__CATALOG_TYPE_2'),
							'3' => static::getMessage('FIELD__CATALOG_TYPE_3'),
						],
					),
					'PRODUCT_GROUP' => array(
						'NAME' => static::getMessage('FIELD__CATALOG_PRODUCT_GROUP'),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'Y',
						'USER_TYPE' => '_CatalogProductGroup',
						'CUSTOM_FILTER_KEY' => 'UF_PRODUCT_GROUP',
						'LIST_VALUES' => static::getProductGroups(),
					),
				);
				if(Helper::isCatalogBarcodeAvailable()){
					$arResult['BARCODE'] = array(
						'NAME' => static::getMessage('FIELD__CATALOG_BARCODE'),
						'TYPE' => 'S',
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'Y',
						'IS_MULTIPLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'BARCODE' : 'CATALOG_BARCODE',
					);
					$arResult['BARCODE_MULTI'] = array(
						'NAME' => static::getMessage('FIELD__CATALOG_BARCODE_MULTI'),
						'TYPE' => 'S',
						'USER_TYPE' => '_Checkbox',
						'IS_FILTRABLE' => Helper::isCatalogNewFilter() ? 'Y' : 'N',
						'IS_WRITEABLE' => 'Y',
						'CUSTOM_FILTER_KEY' => Helper::isCatalogNewFilter() ? 'BARCODE_MULTI' : 'CATALOG_BARCODE_MULTI',
					);
				}
				if($intIBlockOffersID) {
					$arResult['OFFERS'] = array(
						'NAME' => static::getMessage('FIELD__CATALOG_OFFERS'),
						'TYPE' => 'S',
						'USER_TYPE' => '_OffersFlag',
						'READONLY' => true,
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'N',
					);
				}
				$arStores = Helper::getStoresList();
				foreach($arStores as $arStore){
					$arResult['STORE_AMOUNT_'.$arStore['ID']] = array(
						'NAME' => static::getMessage('FIELD__CATALOG_STORE_AMOUNT', array(
							'#NAME#'=>$arStore['TITLE'],
							'#ADDRESS#'=>$arStore['ADDRESS'],
						)),
						'TYPE' => 'N',
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
					);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Prices
	 */
	protected static function _getAvailableElementPrices($intIBlockId){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog')){
			$arCatalog = Helper::getCatalogArray($intIBlockId);
			if(is_array($arCatalog) && $arCatalog['IBLOCK_ID']==$intIBlockId){
				$arPrices = Helper::getPriceList(array('SORT' => 'ASC', 'ID' => 'ASC'));
				foreach($arPrices as $arPrice) {
					$arPriceOriginal = $arPrice;
					$arPrice['NAME_LANG'] = strlen(trim($arPrice['NAME_LANG'])) ? $arPrice['NAME_LANG'] : $arPrice['NAME'];
					$arResult['PRICE_'.$arPrice['ID']] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_NO_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'USER_TYPE' => '_Price',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'Y',
						'DATA' => $arPriceOriginal,
					);
					$arResult['CURRENCY_'.$arPrice['ID']] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_CURRENCY'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'USER_TYPE' => '_Currency',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'Y',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__WITH_DISCOUNT'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_WITH_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'USER_TYPE' => '_Price',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__DISCOUNT'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_DISCOUNT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					#
					$arResult['PRICE_'.$arPrice['ID'].'__WITH_DISCOUNT__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_WITH_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_NO_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'USER_TYPE' => '_Currency',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__DISCOUNT__CURR'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_DISCOUNT_CURR'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__PERCENT'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_PERCENT'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'N',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
					$arResult['PRICE_'.$arPrice['ID'].'__PERCENT__SIGN'] = array(
						'NAME' => $arPrice['NAME_LANG'].static::getMessage('FIELD__CATALOG_PRICE_PERCENT_SIGN'),
						'CODE' => $arPrice['NAME'],
						'TYPE' => 'S',
						'ID' => $arPrice['ID'],
						'IS_FILTRABLE' => 'N',
						'IS_WRITEABLE' => 'N',
						'DATA' => $arPriceOriginal,
					);
				}
			}
		}
		return $arResult;
	}
	
	// *******************************************************************************************************************
	// Field for TYPE_SECTION
	// *******************************************************************************************************************
	
	/**
	 *	
	 */
	protected static function getAvailableSectionFields($intIBlockId, $arFilter=null){
		$strKey = md5(is_array($arFilter) && !empty($arFilter) ? serialize($arFilter) : '');
		$arResult = &static::$arCache[static::CACHE_AVAILABLE_FIELDS][$intIBlockId][Helper::TYPE_SECTION][$strKey];
		if(is_array($arResult) && !empty($arResult)){
			return $arResult;
		}
		$arResult = array(
			'FIELDS' => array(
				'NAME' => static::getMessage('GROUP_SECTION'),
				'ITEMS' => static::_getAvailableSectionFields($intIBlockId),
				'PREFIX' => '', // used for system purposes
				'TYPE' => static::TYPE_FIELD,
			),
			'PROPERTIES' => array(
				'NAME' => static::getMessage('GROUP_USER_FIELDS'),
				'ITEMS' => static::_getAvailableSectionProperties($intIBlockId),
				'PREFIX' => '',
				'SHOW_MORE' => true,
				'TYPE' => static::TYPE_PROPERTY,
			),
			'SEO' => array(
				'NAME' => static::getMessage('GROUP_SEO'),
				'ITEMS' => static::_getAvailableSectionSeoFields($intIBlockId),
				'PREFIX' => 'SEO_',
				'TYPE' => static::TYPE_SEO,
			),
			'IBLOCK' => array(
				'NAME' => static::getMessage('GROUP_IBLOCK'),
				'ITEMS' => static::_getAvailableSectionIBlockFields($intIBlockId),
				'PREFIX' => 'IBLOCK__',
				'NAME_PREFIX' => '',
				'TYPE' => static::TYPE_IBLOCK,
			),
		);
		foreach($arResult as $strType => $arType){
			foreach($arType['ITEMS'] as $key => $arItem){
				$strTypeFull = $arItem['TYPE'].(strlen($arItem['USER_TYPE']) ? ':'.$arItem['USER_TYPE'] : '');
				$arResult[$strType]['ITEMS'][$key]['TYPE_FULL'] = $strTypeFull;
				if(is_array($arItem['DATA'])){
					$arResult[$strType]['ITEMS'][$key]['DATA']['TYPE_FULL'] = $strTypeFull;
					$arResult[$strType]['ITEMS'][$key]['DATA']['PROPERTY_TYPE'] = $arItem['TYPE'];
					$arResult[$strType]['ITEMS'][$key]['DATA']['USER_TYPE'] = $arItem['USER_TYPE'];
				}
			}
		}
		if(is_array($arFilter) && !empty($arFilter)){
			$arResult = static::filterFields($arResult, $arFilter);
		}
		foreach($arResult as $strType => $arType){
			if(empty($arResult[$strType]['ITEMS'])){
				unset($arResult[$strType]);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Fields
	 */
	protected static function _getAvailableSectionFields($intIBlockId){
		$arResult = [];
		if(is_numeric($intIBlockId) || $intIBlockId === true){
			$arResult = array(
				'ID' => array(
					'NAME' => static::getMessage('FIELD__ID'),
					'TYPE' => 'N',
					'READONLY' => true,
					'USER_TYPE' => '_ID_LIST',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'NAME' => array(
					'NAME' => static::getMessage('FIELD__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DATE_CREATE' => array(
					'NAME' => static::getMessage('FIELD__DATE_CREATE'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'TIMESTAMP_X' => array(
					'NAME' => static::getMessage('FIELD__TIMESTAMP_X'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK_ID'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_SECTION_ID' => array(
					'NAME' => static::getMessage('FIELD__SECTION_IBLOCK_SECTION_ID'),
					'TYPE' => 'N',
					'IS_WRITEABLE' => 'Y',
				),
				'ACTIVE' => array(
					'NAME' => static::getMessage('FIELD__ACTIVE'),
					'TYPE' => 'S',
					'USER_TYPE' => '_Checkbox',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'SORT' => array(
					'NAME' => static::getMessage('FIELD__SORT'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'PICTURE' => array(
					'NAME' => static::getMessage('FIELD__SECTION_PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DESCRIPTION' => array(
					'NAME' => static::getMessage('FIELD__SECTION_DESCRIPTION'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'DESCRIPTION_TYPE' => array(
					'NAME' => static::getMessage('FIELD__SECTION_DESCRIPTION_TYPE'),
					'TYPE' => 'S',
					'USER_TYPE' => '_TextType',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'Y',
				),
				'DETAIL_PICTURE' => array(
					'NAME' => static::getMessage('FIELD__SECTION_DETAIL_PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'SECTION_PAGE_URL' => array(
					'NAME' => static::getMessage('FIELD__SECTION_PAGE_URL'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'CODE' => array(
					'NAME' => static::getMessage('FIELD__CODE'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'XML_ID' => array(
					'NAME' => static::getMessage('FIELD__XML_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'CREATED_BY' => array(
					'NAME' => static::getMessage('FIELD__CREATED_BY'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'Y',
				),
				'CREATED_BY__NAME' => array(
					'NAME' => static::getMessage('FIELD__CREATED_BY__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'MODIFIED_BY' => array(
					'NAME' => static::getMessage('FIELD__MODIFIED_BY'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
				'MODIFIED_BY__NAME' => array(
					'NAME' => static::getMessage('FIELD__MODIFIED_BY__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'DEPTH_LEVEL' => array(
					'NAME' => static::getMessage('FIELD__DEPTH_LEVEL'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'Y',
					'IS_WRITEABLE' => 'N',
				),
			);
		}
		return $arResult;
	}
	
	/**
	 *	Properties
	 */
	protected static function _getAvailableSectionProperties($intIBlockId, $bUsePropCode=false){
		$arResult = array();
		$arUserFields = static::getSectionUserFields($intIBlockId);
		foreach($arUserFields as $strFieldCode => $arUserField){
			$strFieldName = '';
			if(is_array($arUserField['EDIT_FORM_LABEL'])) {
				$strFieldName = $arUserField['EDIT_FORM_LABEL'][LANGUAGE_ID];
				if(!strlen($strFieldName)){
					$strFieldName = reset($arUserField['EDIT_FORM_LABEL']);
				}
			}
			if(!strlen($strFieldName)){
				$strFieldName = $strFieldCode;
			}
			/*
			$arTypes = array(
				'integer' => 'N',
				'double' => 'N',
				'date' => 'S', // S:Date
				'datetime' => 'S', // S:DateTime
				'boolean' => 'S',
				'file' => 'F',
				'iblock_section' => 'G',
				'iblock_element' => 'E',
				'hlblock' => 'S', // S:directory
				'enumeration' => 'L',
			);
			$strType = isset($arTypes[$arUserField['USER_TYPE_ID']]) ? $arTypes[$arUserField['USER_TYPE_ID']] : 'S';
			*/
			$strType = static::getSectionFieldType($arUserField['USER_TYPE_ID'], true);
			$arType = explode(':', $strType);
			$strType = $arType[0];
			$strUserType = $arType[1];
			if(strpos($arUserField['USER_TYPE_ID'], 'webdebug_seo_') === 0){
				$strUserType = $arUserField['USER_TYPE_ID'];
			}
			$arResult[$strFieldCode] = array(
				'NAME' => $strFieldName,
				'CODE' => $strFieldCode,
				#'TYPE' => $arUserField['USER_TYPE_ID'],
				'TYPE' => $strType,
				'USER_TYPE' => $strUserType,
				'USER_TYPE_ID' => $arUserField['USER_TYPE_ID'],
				'ID' => $arUserField['ID'],
				'DATA' => $arUserField,
				'IS_FILTRABLE' => 'Y',
				'IS_WRITEABLE' => 'Y',
				'IS_MULTIPLE' => $arUserField['MULTIPLE'] == 'Y',
			);
		}
		return $arResult;
	}
	
	/**
	 *	IBlock fields
	 */
	protected static function _getAvailableSectionIBlockFields($intIBlockId){
		$arResult = array();
		if($intIBlockId && \Bitrix\Main\Loader::includeModule('iblock')){
			$arResult = array(
				'ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__ID'),
					'TYPE' => 'N',
					'READONLY' => true,
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'TIMESTAMP_X' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__TIMESTAMP_X'),
					'TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'IBLOCK_TYPE_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__TYPE_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'LID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__LID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'CODE' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__CODE'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'NAME' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'SORT' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__SORT'),
					'TYPE' => 'N',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'PICTURE' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__PICTURE'),
					'TYPE' => 'F',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'DESCRIPTION' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__DESCRIPTION'),
					'TYPE' => 'S',
					'USER_TYPE' => 'HTML',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'ELEMENT_NAME' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__ELEMENT_NAME'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
				'XML_ID' => array(
					'NAME' => static::getMessage('FIELD__IBLOCK__XML_ID'),
					'TYPE' => 'S',
					'IS_FILTRABLE' => 'N',
					'IS_WRITEABLE' => 'N',
				),
			);
		}
		return $arResult;
	}
	
	/**
	 *	SEO fields
	 */
	protected static function _getAvailableSectionSeoFields($intIBlockId){
		$arResult = [
			'TITLE' => array(
				'NAME' => static::getMessage('FIELD__SEO_TITLE'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'KEYWORDS' => array(
				'NAME' => static::getMessage('FIELD__SEO_KEYWORDS'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'DESCRIPTION' => array(
				'NAME' => static::getMessage('FIELD__SEO_DESCRIPTION'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
			'H1' => array(
				'NAME' => static::getMessage('FIELD__SEO_H1'),
				'TYPE' => 'S',
				'IS_FILTRABLE' => 'N',
				'IS_WRITEABLE' => 'Y',
			),
		];
		return $arResult;
	}
	
	// *******************************************************************************************************************
	// Other
	// *******************************************************************************************************************
	
	/**
	 *	Display one available item
	 *	As minimum, it used in popup 'Select field' and on display field's default value
	 */
	public static function displayAvailableItemName($arItem, $bParent=false, $bOffer=false){
		$strResult = '';
		$arInfo = array();
		if(strlen($arItem['NAME_PREFIX'])){
			$strResult .= $arItem['NAME_PREFIX'].' ';
		}
		if(strlen($arItem['ID'])){
			$arInfo[] = $arItem['ID'];
		}
		if(strlen($arItem['CODE'])){
			$arInfo[] = $arItem['CODE'];
		}
		if(is_array($arItem['DATA']) && strlen($arItem['DATA']['PROPERTY_TYPE'])){
			if(substr($arItem['DATA']['USER_TYPE'], 0, 1) == '_'){ // Do not display system user types
				unset($arItem['DATA']['USER_TYPE']);
			}
			$strItem = $arItem['DATA']['PROPERTY_TYPE'];
			$strItem .= strlen($arItem['DATA']['USER_TYPE']) ? ':'.$arItem['DATA']['USER_TYPE'] : '';
			$strItem .= $arItem['DATA']['MULTIPLE'] == 'Y' ? '+' : '';
			$arInfo[] = $strItem;
		}
		elseif(is_array($arItem['DATA']) && strlen($arItem['DATA']['USER_TYPE_ID'])){
			$strItem = $arItem['DATA']['USER_TYPE_ID'];
			$strItem .= $arItem['DATA']['MULTIPLE'] == 'Y' ? '+' : '';
			$arInfo[] = $strItem;
		}
		else{
			if($arItem['IS_MULTIPLE'] == 'Y'){
				$arInfo[] = '+';
			}
		}
		$strResult .= $arItem['NAME'];
		if(!empty($arInfo)) {
			$strResult .= ' ['.implode(', ', $arInfo).']';
		}
		if(!empty($arItem['MORE'])) {
			$strResult .= ' '.$arItem['MORE'];
		}
		if($bOffer){
			$strResult = static::getMessage('FIELD__PREFIX_OFFER').$strResult;
		}
		elseif($bParent){
			$strResult = static::getMessage('FIELD__PREFIX_PRODUCT').$strResult;
		}
		return $strResult;
	}
	
	/**
	 *	Get array of ID of used sections
	 *	$strUsedSectionsID - это все разделы, в т.ч. по доп. привязкам, и тут могут быть и те разделы, которые
	 *	не выбраны в списке - их не должно быть в результирующем массиве
	 *	$strMode = all || selected || selected_with_subsections
	 */
	public static function getInvolvedSectionsID($intIBlockId, $strSelectedCategoriesID, $strMode){
		$arResult = array();
		$arSort = array(
			'LEFT_MARGIN' => 'ASC',
		);
		$arFilter = array(
			'IBLOCK_ID' => $intIBlockId,
			'CHECK_PERMISSIONS' => 'N',
		);
		$arSectionsAll = array();
		$resSections = \CIBlockSection::getList($arSort, $arFilter, false, array('ID','DEPTH_LEVEL'));
		while($arSection = $resSections->getNext(false,false)){
			$arSectionsAll[$arSection['ID']] = array(
				'DEPTH_LEVEL' => IntVal($arSection['DEPTH_LEVEL']),
			);
		}
		unset($resSections, $arSection);
		#
		$arSelectedSectionsID = explode(',', $strSelectedCategoriesID);
		Helper::arrayRemoveEmptyValues($arSelectedSectionsID);
		#
		switch($strMode){
			case 'all':
				$arResult = array_keys($arSectionsAll);
				break;
			case 'selected':
				foreach($arSelectedSectionsID as $intSelectedSectionID){
					if(isset($arSectionsAll[$intSelectedSectionID])){
						$arResult[] = $intSelectedSectionID;
					}
				}
				break;
			case 'selected_with_subsections':
				foreach($arSelectedSectionsID as $intSelectedSectionID){
					# для каждого раздела $intSelectedSectionID ищем его в $arSectionsAll и отбираем все подразделы (там где DEPT_LEVEL больше чем у него)
					$intSelectedDepthLevel = false;
					foreach($arSectionsAll as $intSectionID => $arSection){
						if($intSelectedDepthLevel){
							if($arSection['DEPTH_LEVEL']>$intSelectedDepthLevel) {
								$arResult[] = $intSectionID;
							}
							else {
								break;
							}
						}
						if($intSectionID == $intSelectedSectionID){
							$arResult[] = $intSectionID;
							$intSelectedDepthLevel = $arSection['DEPTH_LEVEL'];
						}
					}
					$arResult = array_unique($arResult); // т.к. может быть выбран и родитель, и дети - в таком случае будут дубли
				}
				break;
		}
		unset($arSectionsAll, $arSelectedSectionsID);
		return $arResult;
	}
	
	/**
	 *	
	 */
	protected static function isIBlockSupportSections($intIBlockTypeId){
		static $arIBlockTypes;
		if(!is_array($arIBlockTypes)){
			$arIBlockTypes = [];
			$resIBlockType = \CIBlockType::getList();
			while($arIBlockType = $resIBlockType->getNext(false, false)){
				$arIBlockTypes[$arIBlockType['ID']] = $arIBlockType['SECTIONS'] == 'Y' ? true : false;
			}
		}
		return !!$arIBlockTypes[$intIBlockTypeId];
	}
	
	/**
	 *	Get section user fields
	 */
	public static function getSectionUserFields($intIBlockId, $strField=false) {
		$arResult = [];
		if (\Bitrix\Main\Loader::includeModule('iblock')) {
			$arFilter = array(
				'ENTITY_ID' => 'IBLOCK_'.$intIBlockId.'_SECTION',
			);
			if(!empty($strField)) {
				$arFilter['FIELD_NAME'] = $strField;
			}
			$resProps = \CUserTypeEntity::GetList(array('SORT'=>'ASC'), $arFilter);
			while ($arProp = $resProps->GetNext(false, false)) {
				$arProp = \CUserTypeEntity::GetByID($arProp['ID']);
				$arResult[$arProp['FIELD_NAME']] = $arProp;
			}
			if(!empty($strField)) {
				$arResult = $arResult[$strField];
				if(!is_array($arResult)) {
					$arResult = [];
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Transform section field type to iblock property type
	 */
	public static function getSectionFieldType($strUserTypeId, $bFull=false){
		$arTypes = array(
			'integer' => 'N',
			'int' => 'N',
			'double' => 'N',
			'date' => 'S:Date', // S:Date
			'datetime' => 'S:DateTime', // S:DateTime
			'boolean' => 'S',
			'file' => 'F',
			'iblock_section' => 'G',
			'iblock_element' => 'E',
			'hlblock' => 'S:directory', // S:directory
			'enumeration' => 'L',
			# webdebug.seo
			'webdebug_seo_price' => 'N',
			'webdebug_seo_store' => 'N',
			'webdebug_seo_location' => 'N',
			# Avito
			'avito_category' => 'S:avito_category',
		);
		$strType = $arTypes[$strUserTypeId];
		if(is_null($strType)){
			$strType = 'S';
		}
		if(!$bFull && strpos($strType, ':') !== false){
			$strType = explode(':', $strType);
			$strType = reset($strType);
		}
		return $strType;
	}
	
	/**
	 *	Field is field (both for elements and sections)
	 */
	public static function isField($strCode) {
		$intIBlockSimulate = 1;
		$arFieldsElement = array_keys(static::_getAvailableElementFields($intIBlockSimulate));
		#$arFieldsSection = array_keys(static::_getAvailableSectionFields($intIBlockSimulate));
		return in_array($strCode, $arFieldsElement)/* || in_array($strCode, $arFieldsSection)*/;
	}
	
	/**
	 *	Field is section field
	 */
	public static function isSectionField($strCode) {
		$intIBlockSimulate = 1;
		$arFieldsSection = array_keys(static::_getAvailableSectionFields($intIBlockSimulate));
		return in_array($strCode, $arFieldsSection);
	}
	
	/**
	 *	Field is property
	 */
	public static function isProperty($strCode) {
		if(preg_match('#^PROPERTY_(.*?)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Field is section property
	 */
	public static function isSectionProperty($strCode) {
		if(preg_match('#^UF_.*?$#', $strCode, $arMatch)) {
			return $strCode;
		}
		return false;
	}
	
	/**
	 *	Field is seo-field
	 */
	public static function isSeoField($strCode) {
		if(preg_match('#^SEO_(.*?)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Field is price
	 */
	public static function isPrice($strCode) {
		if(preg_match('#^CATALOG_PRICE_(\d+)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Field is price (extended version)
	 */
	public static function isPriceExt($strCode, &$strSuffix) {
		if(preg_match('#^CATALOG_(PRICE|CURRENCY)_(\d+)(__|)(.*?)$#', $strCode, $arMatch)) {
			$strSuffix = ($arMatch[1] == 'CURRENCY') ? 'CURRENCY' : $arMatch[4];
			return $arMatch[2];
		}
		return false;
	}
	
	/**
	 *	Field is store
	 */
	public static function isStoreAmount($strCode) {
		if(preg_match('#^CATALOG_STORE_AMOUNT_(\d+)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Field is catalog-field
	 */
	public static function isCatalogField($strCode) {
		if(preg_match('#^CATALOG_(.*?)$#', $strCode, $arMatch)) {
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	Get all sections for selected iblock
	 */
	public static function getIBlockSections($intIBlockId, $intMaxDepth=0) {
    $arResult = [];
		if(is_numeric($intIBlockId) && $intIBlockId > 0 && \Bitrix\Main\Loader::includeModule('iblock')) {
			$arFilter = [
				'IBLOCK_ID' => $intIBlockId,
				'CHECK_PERMISSIONS' => 'N',
			];
			if($intMaxDepth > 0){
				$arFilter['<=DEPTH_LEVEL'] = $intMaxDepth;
			}
			$resSections = \CIBlockSection::GetList(['LEFT_MARGIN'=>'ASC'], $arFilter, false, 
				['ID', 'NAME', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID']);
			while($arSection = $resSections->GetNext()){
				$arResult[$arSection['ID']] = $arSection;
			}
			# Name => Full name
			if(Helper::getOption('section_name_chain') == 'Y'){
				$arLevelName = [];
				foreach($arResult as &$arSection){
					$arLevelName[$arSection['DEPTH_LEVEL']] = $arSection['NAME'];
					$arName = array_slice($arLevelName, 0, $arSection['DEPTH_LEVEL']);
					$arSection['NAME'] = implode(' - ', $arName);
				}
				unset($arSection);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get IBlockID for element
	 */
	public static function getElementIBlockId($intElementId){
		if(is_numeric($intElementId) && $intElementId > 0 && \Bitrix\Main\Loader::includeModule('iblock')) {
			$resElement = \CIBlockElement::GetList([], ['ID' => $intElementId], false, false, ['IBLOCK_ID']);
			if($arElement = $resElement->fetch()){
				return $arElement['IBLOCK_ID'];
			}
		}
		return false;
	}
	
	/**
	 *	Get IBlockID for section
	 */
	public static function getSectionIBlockId($intSectionId){
		if(is_numeric($intSectionId) && $intSectionId > 0 && \Bitrix\Main\Loader::includeModule('iblock')) {
			$resSection = \CIBlockSection::getList([], ['ID' => $intSectionId], false, ['IBLOCK_ID']);
			if($arSection = $resSection->fetch()){
				return $arSection['IBLOCK_ID'];
			}
		}
		return false;
	}
	
	/**
	 *	Transform array of sections to tree of sections
	 */
	public static function sectionsArrayToTree($arSections){
		$arResult = [];
		$DepthLevel = 0;
		$arFirstSection = reset($arSections);
		$DepthLevelFirst = $arFirstSection['DEPTH_LEVEL'];
		$LastIndex = 0;
		$arParents = [];
		foreach ($arSections as $arSection) {
			$DepthLevel = $arSection['DEPTH_LEVEL'];
			if ($DepthLevel == $DepthLevelFirst) {
				$arResult[] = $arSection;
				$LastIndex = count($arResult)-1;
				$arParents[$DepthLevel] = &$arResult[$LastIndex];
			} else {
				$arParents[$DepthLevel-1]['SECTIONS'][] = $arSection;
				$LastIndex = count($arParents[$DepthLevel-1]['SECTIONS'])-1;
				$arParents[$DepthLevel] = &$arParents[$DepthLevel-1]['SECTIONS'][$LastIndex];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get element additional sections
	 */
	public static function getElementAdditionalSections($intElementId, $intMainSection=false){
		$arResult = [];
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(!$intMainSection){
				$resItem = \CIBlockElement::getList([], array('ID' => $intElementId), false, false, array('ID', 'IBLOCK_SECTION_ID'));
				#if($arItem = $resItem->getNext(false, false)){
				if($arItem = $resItem->fetch()){
					$intMainSection = $arItem['IBLOCK_SECTION_ID'];
				}
			}
			$resGroups = \CIBlockElement::GetElementGroups($intElementId, false, array('ID'));
			#while($arGroup = $resGroups->getNext(false, false)){
			while($arGroup = $resGroups->fetch()){
				if($arGroup['ID'] !== $intMainSection) {
					$arResult[] = $arGroup['ID'];
				}
			}
		}
		unset($resItem, $arItem, $resGroups, $arGroup);
		return $arResult;
	}
	
	/**
	 *	Get property IDs by code (CIBlockProperty::getList cannot to do this)
	 */
	public static function getIBlockPropsIdByCode($intIBlockId, $arPropCodes){
		$arResult = [];
		$intIBlockId = IntVal($intIBlockId);
		$arPropCodes = array_map(function($strProp){
			return Helper::forSql($strProp);
		}, $arPropCodes);
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$resProps = \Bitrix\Iblock\PropertyTable::getList(array(
				'filter' => array(
					'IBLOCK_ID' => $intIBlockId,
					'CODE' => $arPropCodes,
				),
				'order' => array(
					'ID' => 'ASC',
				),
				'select' => array(
					'ID',
					'CODE',
				),
			));
			while($arProp = $resProps->fetch()){
				$arResult[$arProp['ID']] = $arProp['CODE'];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get element array
	 *	Example:
			$arFeatures = [
				'FIELDS' => ['DETAIL_TEXT'],
				'PROPERTY_ID' => [334, 336],
				'SECTION' => ['PICTURE', 'DETAIL_PICTURE', 'DESCRIPTION'], {or true if all}
				'IBLOCK' => true,
				'SEO' => true,
				'ADDITIONAL_SECTIONS' => true,
				'CATALOG' => true,
				'BARCODE' => true,
				'PRICES' => [1, 2, 3], {or true if all}
				'STORES' => [1, 2], {or true if all}
			];
			$arElement = \WD\Antirutin\IBlock::getElementArray(5461, 20, $arFeatures);
	 */
	public static function getElementArray($intElementId, $intIBlockId, $arFeatures=null, $bPropertiesById=true, $arOfferFilter=null){
		if(!\Bitrix\Main\Loader::includeModule('iblock')){
			return [];
		}
		if(!$intIBlockId){
			$intIBlockId = static::getElementIBlockID($intElementId);
		}
		#
		if($arFeatures === true){
			$bGetFields = true;
			$bSkipFieldPictures = false;
			$bGetProperties = true;
			$bGetEmptyProperties = true;
			$bGetAdditionalSections = true;
			$bGetSection = true;
			$bGetSectionPictures = true;
			$bGetIBlock = true;
			$bGetIBlockPictures = true;
			$bGetParent = false;
			$bGetOffers = false;
			$bGetFirstOffer = false;
			$bGetSeo = true;
			$bGetSeoTemplates = true;
			$bGetCatalog = true;
			$bGetQuantity = true;
			$bGetBarcode = true;
			$bGetStores = true;
			$bGetPrices = true;
		}
		elseif(is_array($arFeatures) && !empty($arFeatures)){
			$bGetFields = is_array($arFeatures['FIELDS']) && !empty($arFeatures['FIELDS']) || $arFeatures['FIELDS'] === true;
			$bSkipFieldPictures = $arFeatures['SKIP_FIELD_PICTURES'] === true;
			$bGetProperties = is_array($arFeatures['PROPERTY_ID']) && !empty($arFeatures['PROPERTY_ID']) || $arFeatures['PROPERTY_ID'] === true;
			$bGetEmptyProperties = $arFeatures['EMPTY_PROPERTIES'] === true;
			$bGetAdditionalSections = $arFeatures['ADDITIONAL_SECTIONS'] === true;
			$bGetSection = $arFeatures['SECTION'] === true || is_array($arFeatures['SECTION']) && !empty($arFeatures['SECTION']);
			$bGetSectionPictures = $arFeatures['SECTION_PICTURES'] === true;
			$bGetIBlock = $arFeatures['IBLOCK'] === true;
			$bGetIBlockPictures = $arFeatures['IBLOCK_PICTURES'] === true;
			$bGetParent = $arFeatures['PARENT'] === true;
			$bGetOffers = $arFeatures['OFFERS'] === true;
			$bGetFirstOffer = $arFeatures['OFFER'] === true;
			$bGetSeo = $arFeatures['SEO'] === true;
			$bGetSeoTemplates = $arFeatures['SEO_TEMPLATES'] === true;
			$bGetCatalog = $arFeatures['CATALOG'] === true;
			$bGetQuantity = $arFeatures['QUANTITY'] === true;
			$bGetBarcode = $arFeatures['BARCODE'] === true;
			$bGetPrices = $arFeatures['PRICES'] === true || is_array($arFeatures['PRICES']) && !empty($arFeatures['PRICES']);
			$bGetStores = $arFeatures['STORES'] === true || is_array($arFeatures['STORES']) && !empty($arFeatures['STORES']);
		}
		else{
			$bGetFields = true;
			$bSkipFieldPictures = true;
			$bGetProperties = false;
			$bGetEmptyProperties = false;
			$bGetAdditionalSections = false;
			$bGetSection = false;
			$bGetSectionPictures = false;
			$bGetIBlock = false;
			$bGetIBlockPictures = false;
			$bGetParent = false;
			$bGetOffers = false;
			$bGetFirstOffer = false;
			$bGetSeo = false;
			$bGetSeoTemplates = false;
			$bGetCatalog = false;
			$bGetQuantity = false;
			$bGetBarcode = false;
			$bGetPrices = false;
			$bGetStores = false;
		}
		#
		if($bGetParent || $bGetOffers){
			$arCatalog = Helper::getCatalogArray($intIBlockId);
			$bParent = is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] || !is_array($arCatalog);
			$bOffer = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'];
		}
		#
		$arSort = array(
			'ID' => 'ASC',
		);
		$arFilter = array(
			'ID' => $intElementId,
			'IBLOCK_ID' => $intIBlockId,
		);
		$arSelect = array(
			'ID',
			'IBLOCK_ID', // this is need for ->getProperties()
			'IBLOCK_SECTION_ID', // this is need for get sections
		);
		if($bGetFields){
			$arSelect[] = '*';
		}
		if(is_array($arFeatures['FIELDS'])){
			foreach($arFeatures['FIELDS'] as $strField){
				$arSelect[] = $strField;
			}
		}
		if($bGetCatalog){
			$arSelect[] = 'CATALOG_QUANTITY'; // it selects all catalog_* fields
		}
		$arSelect = array_unique($arSelect);
		$resElement = \CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		$obElement = $resElement->getNextElement();
		if(is_object($obElement)){
			$arResult = $obElement->getFields();
			#
			if($arResult['PREVIEW_PICTURE'] > 0 && !$bSkipFieldPictures){
				$arResult['PREVIEW_PICTURE'] = \CFile::getFileArray($arResult['PREVIEW_PICTURE']);
			}
			if($arResult['DETAIL_PICTURE'] > 0 && !$bSkipFieldPictures){
				$arResult['DETAIL_PICTURE'] = \CFile::getFileArray($arResult['DETAIL_PICTURE']);
			}
			# Get element more sections
			if($bGetAdditionalSections) {
				$arResult['ADDITIONAL_SECTIONS'] = array();
				$resSections = \CIBlockElement::GetElementGroups($intElementId, false, array('ID'));
				while($arSection = $resSections->fetch()){
					if($arSection['ID'] != $arResult['IBLOCK_SECTION_ID']) {
						$arResult['ADDITIONAL_SECTIONS'][] = $arSection['ID'];
					}
				}
			}
			# Get parent section
			if($bGetSection && $arResult['IBLOCK_SECTION_ID']){
				$arResult['SECTION'] = [];
				$arFilter = array(
					'ID' => $arResult['IBLOCK_SECTION_ID'],
					'IBLOCK_ID' => $arResult['IBLOCK_ID'],
					'CHECK_PERMISSIONS' => 'N',
				);
				if(is_array($arFeatures['SECTION'])) {
					$arSelect = array(
						'ID',
						'IBLOCK_ID',
						'IBLOCK_SECTION_ID',
					);
					foreach($arFeatures['SECTION'] as $strField){
						if($strField === '*'){
							$arSelect = [];
							break;
						}
						$arSelect[] = $strField;
					}
				}
				else{
					$arSelect = [];
				}
				$arSelect = array_unique($arSelect);
				$resSection = \CIBlockSection::getList(array('ID'=>'ASC'), $arFilter, false, $arSelect);
				if($arSection = $resSection->getNext()){
					if($bGetSectionPictures){
						if($arSection['PICTURE'] > 0){
							$arSection['PICTURE'] = \CFile::getFileArray($arSection['PICTURE']);
						}
						if($arSection['DETAIL_PICTURE'] > 0){
							$arSection['DETAIL_PICTURE'] = \CFile::getFileArray($arSection['DETAIL_PICTURE']);
						}
					}
					# Save result
					$arResult['SECTION'] = $arSection;
				}
				$resSection = $arSection = $resSection2 = $arSection2 = $arFilter = $arSelect = $intParentSectionID = null;
			}
			# Get parent iblock
			if($bGetIBlock) {
				$arResult['IBLOCK'] = [];
				$arIBlockFilter = array(
					'ID' => $arResult['IBLOCK_ID'],
					'CHECK_PERMISSIONS' => 'N',
				);
				$resIBlock = \CIBlock::getList(array(), $arIBlockFilter, false);
				if($arIBlock = $resIBlock->getNext()){
					#
					if($bGetIBlockPictures){
						if($arIBlock['PICTURE'] > 0){
							$arIBlockPicture = &static::$arCache[static::CACHE_IBLOCK_PICTURES][$arIBlock['ID']];
							if(!isset($arIBlockPicture)){
								$arIBlockPicture = \CFile::getPath($arIBlock['PICTURE']);
							}
						}
					}
					$arResult['IBLOCK'] = $arIBlock;
				}
				$resIBlock = $arIBlock = $arIBlockFilter = null;
			}
			# Get element properties
			if($bGetProperties){
				$arPropertyFilter = [];
				if(is_array($arFeatures['PROPERTY_ID'])){
					$arPropertyFilter['ID'] = $arFeatures['PROPERTY_ID'];
				}
				if(!$bGetEmptyProperties){
					$arPropertyFilter['EMPTY'] = 'N';
				}
				$arResult['PROPERTIES'] = array();
				foreach($obElement->getProperties(false, $arPropertyFilter) as $arProp){
					$strPropKey = strlen($arProp['CODE']) && !$bPropertiesById ? $arProp['CODE'] : $arProp['ID'];
					$arResult['PROPERTIES'][$strPropKey] = $arProp;
				}
			}
			if($bGetQuantity && !$bGetCatalog && \Bitrix\Main\Loader::includeModule('catalog')){
				$arResult['QUANTITY'] = false;
				$resProduct = \CCatalogProduct::getList([], ['ID' => $intElementId], false, false, ['QUANTITY']);
				if($arProduct = $resProduct->fetch()){
					$arResult['QUANTITY'] = $arProduct['QUANTITY'];
				}
			}
			# Product group
			if($bGetCatalog && \Bitrix\Main\Loader::includeModule('catalog')){
				$arResult = array_merge($arResult, static::getElementProductGroup($intElementId));
			}
			# Barcode
			if($bGetBarcode && Helper::isCatalogBarcodeAvailable()) {
				$arResult['CATALOG_BARCODE'] = [];
				$arResult['CATALOG_BARCODE_FULL'] = [];
				$resBarcode = \CCatalogStoreBarCode::GetList(array(), array('PRODUCT_ID' => $intElementId, 'STORE_ID' => 0));
				while($arBarcode = $resBarcode->fetch()) {
					$arResult['CATALOG_BARCODE'][] = $arBarcode['BARCODE'];
					$arResult['CATALOG_BARCODE_FULL'][$arBarcode['ID']] = $arBarcode;
				}
				$resProduct = \CCatalogProduct::getList([], ['ID' => $intElementId], false, false, ['ID', 'BARCODE_MULTI']);
				if($arProduct = $resProduct->getNext()){
					$arResult['CATALOG_BARCODE_MULTI'] = $arProduct['BARCODE_MULTI'] == 'Y' ? 'Y' : 'N';
					$arResult['~CATALOG_BARCODE_MULTI'] = $arProduct['BARCODE_MULTI'];
				}
				$resBarcode = $arBarcode = null;
			}
			# Prices
			if($bGetPrices && \Bitrix\Main\Loader::includeModule('catalog')){
				$arResult['PRICES'] = [];
				$arFilter = array(
					'PRODUCT_ID' => $arResult['ID'],
				);
				if(is_array($arFeatures['PRICES'])){
					$arFilter['CATALOG_GROUP_ID'] = $arFeatures['PRICES'];
				}
				$resPrices = \CPrice::getList([], $arFilter);
				while($arPrice = $resPrices->getNext(false, false)){
					$arResult['PRICES'][$arPrice['CATALOG_GROUP_ID']] = $arPrice;
				}
				$arFilter = $resPrices = $arPrice = null;
			}
			# Stores
			if($bGetStores && Helper::isCatalogStoresAvailable()){
				$arResult['STORES'] = [];
				$arFilter = array(
					'PRODUCT_ID' => $arResult['ID'],
				);
				if(is_array($arFeatures['STORES'])){
					$arFilter['STORE_ID'] = $arFeatures['STORES'];
				}
				$resStoreAmount = \CCatalogStoreProduct::getList([], $arFilter);
				while($arStoreAmount = $resStoreAmount->getNext(false, false)){
					$arResult['STORES'][$arStoreAmount['STORE_ID']] = $arStoreAmount;
				}
				$arFilter = $resStoreAmount = $arStoreAmount = null;
			}
			# Additional product data
			if(is_array($arCatalog)){
				$arResult['IS_PARENT'] = $bParent;
				$arResult['IS_OFFER'] = $bOffer;
				if($arResult['IS_PARENT']){
					$arResult['OFFERS_IBLOCK_ID'] = $arCatalog['OFFERS_IBLOCK_ID'];
				}
				if($arResult['IS_OFFER']){
					$arResult['PRODUCT_IBLOCK_ID'] = $arCatalog['PRODUCT_IBLOCK_ID'];
				}
			}
			# Get offers
			if($bGetOffers) {
				static::getElementOffers($arResult, $bGetFirstOffer, $arOfferFilter);
			}
			# Get SEO for element
			if($bGetSeo){
				$arResult['SEO'] = [];
				if(class_exists('\Bitrix\IBlock\InheritedProperty\ElementValues')){
					$obIPropValues = new \Bitrix\IBlock\InheritedProperty\ElementValues($arResult['IBLOCK_ID'], $arResult['ID']);
					$arIPropValues = $obIPropValues->getValues();
					if(is_array($arIPropValues)){
						foreach(static::$arSeoMapElement as $strTo => $strFrom){
							$arResult['SEO'][$strTo] = htmlspecialchars_decode($arIPropValues[$strFrom]);
							$arResult['SEO']['~~'.$strTo] = strVal($arIPropValues[$strFrom]);
						}
					}
					unset($obIPropValues, $arIPropValues, $strTo, $strFrom);
				}
			}
			if($bGetSeoTemplates){
				$arResult['SEO_TEMPLATES'] = [];
				if(class_exists('\Bitrix\IBlock\InheritedProperty\ElementTemplates')){
					$obIPropTemplates = new \Bitrix\IBlock\InheritedProperty\ElementTemplates($arResult['IBLOCK_ID'], $arResult['ID']);
					$arIPropTemplates = $obIPropTemplates->findTemplates();
					if(is_array($arIPropTemplates)){
						foreach(static::$arSeoMapElement as $strTo => $strFrom){
							$arResult['SEO_TEMPLATES'][$strTo] = $arIPropTemplates[$strFrom];
						}
					}
					unset($obIPropTemplates, $arIPropValues, $strTo, $strFrom);
				}
			}
			# Get parent element
			if($bGetParent && is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']){
				foreach($arResult['PROPERTIES'] as $arProperty){
					if($arProperty['ID'] == $arCatalog['SKU_PROPERTY_ID']){
						$intParentElementID = $arProperty['VALUE'];
						$arResult['PARENT'] = static::getElementArray($intParentElementID, $arCatalog['PRODUCT_IBLOCK_ID'], 
							$bGetOffers_=false, $bGetParent_=false, $bGetSection_=true, $bGetIBlock_=true);
						if(empty($arResult['IBLOCK_SECTION_ID'])){
							$arResult['IBLOCK_SECTION_ID'] = $arResult['PARENT']['IBLOCK_SECTION_ID'];
						}
						if(empty($arResult['ADDITIONAL_SECTIONS'])){
							$arResult['ADDITIONAL_SECTIONS'] = $arResult['PARENT']['ADDITIONAL_SECTIONS'];
						}
						break;
					}
				}
			}
			#
			foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers(WDA_MODULE, 'onGetElementArray') as $arHandler) {
				ExecuteModuleEventEx($arHandler, [&$arResult, $intElementId, $intIBlockId, $bGetParent, $obElement]);
			}
			#
			$arCatalog = null;
			return $arResult;
		}
		$obElement = null;
		return false;
	}
	
	/**
	 *	
	 */
	public static function getProductUserFields(){
		$arResult = [];
		if(\Bitrix\Main\Loader::includeModule('catalog')){
			$arFilter = [
				'ENTITY_ID' => \Bitrix\Catalog\ProductTable::getUfId(),
			];
			$resUserField = \CUserTypeEntity::getList([], $arFilter);
			while($arUserField = $resUserField->fetch()){
				$arResult[$arUserField['FIELD_NAME']] = $arUserField;
			}
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	public static function getProductGroups(){
		$arResult = &static::$arCache[static::CACHE_PRODUCT_GROUPS];
		if(!is_array($arResult)){
			$arResult = [];
			$arUserFields = static::getProductUserFields();
			$arField = &$arUserFields['UF_PRODUCT_GROUP'];
			if($arField){
				foreach(static::getHighloadItems($arField['SETTINGS']['HLBLOCK_ID']) as $arHlItem){
					$arResult[$arHlItem['ID']] = sprintf('%s [%d]', $arHlItem['UF_NAME'], $arHlItem['ID']);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	
	 */
	public static function getElementProductGroup($intElementId){
		$arResult = [];
		\Bitrix\Main\Loader::includeModule('catalog');
		$arUserFields = $GLOBALS['USER_FIELD_MANAGER']->getUserFields(\Bitrix\Catalog\ProductTable::getUfId(), 
			$intElementId, LANGUAGE_ID);
		$arField = $arUserFields['UF_PRODUCT_GROUP'];
		if($arField['SETTINGS']['HLBLOCK_ID'] && $arField['VALUE']){
			$arHlItems = static::getHighloadItems($arField['SETTINGS']['HLBLOCK_ID'], [], ['ID' => $arField['VALUE']]);
			if(is_array($arHlItems)){
				foreach($arHlItems as $arHlItem){
					$arResult['PRODUCT_GROUP_ID'] = $arHlItem['ID'];
					$arResult['PRODUCT_GROUP_CODE'] = $arHlItem['UF_XML_ID'];
					$arResult['PRODUCT_GROUP_NAME'] = $arHlItem['UF_NAME'];
					break;
				}
			}
		}
		unset($arUserFields, $arField, $arHlItems, $arHlItem);
		return $arResult;
	}
	
	/**
	 *	Get section array
	 */
	public static function getSectionArray($intSectionId, $intIBlockId, $arSelect=null){
		if(!\Bitrix\Main\Loader::includeModule('iblock')){
			return [];
		}
		if(!$intIBlockId){
			$intIBlockId = static::getSectionIBlockId($intSectionId);
		}
		$bGetSeo = false;
		$bGetSeoTemplates = false;
		$arSort = [
			'ID' => 'ASC',
		];
		$arFilter = [
			'ID' => $intSectionId,
			'IBLOCK_ID' => $intIBlockId,
		];
		if(is_array($arSelect)){
			$intSeoKey = array_search('SEO', $arSelect);
			if($intSeoKey !== false){
				$bGetSeo = true;
				unset($arSelect[$intSeoKey]);
			}
			$intSeoKey = array_search('SEO_TEMPLATES', $arSelect);
			if($intSeoKey !== false){
				$bGetSeoTemplates = true;
				unset($arSelect[$intSeoKey]);
			}
			$arSelect = array_merge([
				'ID',
				'IBLOCK_ID',
			], $arSelect);
		}
		else{
			$arSelect = [
				'*',
				'UF_*',
			];
		}
		$resSection = \CIBlockSection::getList($arSort, $arFilter, false, $arSelect);
		if($arResult = $resSection->getNext()){
			# Get raw values (not decoded!)
			$arRawItems = ['NAME', 'DESCRIPTION'];
			foreach($arRawItems as $strRawItem){
				if(isset($arResult[$strRawItem])){
					$arResult[$strRawItem] = $arResult['~'.$strRawItem];
				}
			}
			if($arResult['PICTURE'] > 0){
				$arResult['PICTURE'] = \CFile::getFileArray($arResult['PICTURE']);
			}
			if($arResult['DETAIL_PICTURE'] > 0){
				$arResult['DETAIL_PICTURE'] = \CFile::getFileArray($arResult['DETAIL_PICTURE']);
			}
			# Properties
			foreach($arResult as $key => $mValue){
				if(stripos($key, 'UF_') === 0){
					if(!is_array($arResult['PROPERTIES'])){
						$arResult['PROPERTIES'] = [];
					}
					$arResult['PROPERTIES'][$key] = $mValue;
					$arResult['PROPERTIES']['~'.$key] = $arResult['~'.$key];
					unset($arResult[$key], $arResult['~'.$key]);
				}
			}
			#
			if($bGetSeo){
				$arResult['SEO'] = [];
				if(class_exists('\Bitrix\IBlock\InheritedProperty\SectionValues')){
					$obIPropValues = new \Bitrix\IBlock\InheritedProperty\SectionValues($intIBlockId, $intSectionId);
					$arIPropValues = $obIPropValues->getValues();
					if(is_array($arIPropValues)){
						foreach(static::$arSeoMapSection as $strTo => $strFrom){
							$arResult['SEO'][$strTo] = htmlspecialchars_decode($arIPropValues[$strFrom]);
							$arResult['SEO']['~~'.$strTo] = strVal($arIPropValues[$strFrom]);
						}
					}
					$obIPropValues = $arIPropValues = $strTo = $strFrom = null;
				}
			}
			#
			if($bGetSeoTemplates){
				$arResult['SEO_TEMPLATES'] = [];
				if(class_exists('\Bitrix\IBlock\InheritedProperty\SectionTemplates')){
					$obIPropTemplates = new \Bitrix\IBlock\InheritedProperty\SectionTemplates($intIBlockId, $intSectionId);
					$arIPropTemplates = $obIPropTemplates->findTemplates();
					if(is_array($arIPropTemplates)){
						foreach(static::$arSeoMapSection as $strTo => $strFrom){
							$arResult['SEO_TEMPLATES'][$strTo] = $arIPropTemplates[$strFrom];
						}
					}
					unset($obIPropTemplates, $arIPropValues, $strTo, $strFrom);
				}
			}
			#
			return $arResult;
		}
		return false;
	}
	
	/**
	 *	Get element first offer
	 */
	protected static function getElementOffers(&$arElement, $bGetFirstOffer, $arCustomFilter=null){
		$arElement['OFFERS'] = [];
		$intIBlockId = $arElement['IBLOCK_ID'];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] && \Bitrix\Main\Loader::includeModule('iblock')) {
			$arSort = ['SORT' => 'ASC', 'ID' => 'ASC'];
			foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers(WDA_MODULE, 'onGetSortForGetOffers') as $arHandler) {
				ExecuteModuleEventEx($arHandler, [&$arSort, $arElement, $bGetFirstOffer]);
			}
			$arFilter = [
				'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $arElement['ID'],
			];
			if(is_array($arCustomFilter)){
				$arFilter = array_merge($arFilter, $arCustomFilter);
			}
			$resOffers = \CIBlockElement::GetList($arSort, $arFilter, false, false, array('ID'));
			while($arOffer = $resOffers->fetch()){
				$arElement['OFFERS'][] = IntVal($arOffer['ID']);
			}
			if(!empty($arElement['OFFERS'])){
				$intFirstOfferId = reset($arElement['OFFERS']);
				if($bGetFirstOffer){
					$arElement['OFFER'] = static::getElementArray($intFirstOfferId, $arCatalog['OFFERS_IBLOCK_ID']);
				}
			}
		}
		$intIBlockId = $arCatalog = $arSort = $arFilter = $intFirstOfferId = $resOffers = $arOffer = null;
	}
	
	/**
	 *	Show <select> with available fields
	 */
	public static function showAvailableFields($arFields, $strEntityType, $strInputName, $mValue=null, $strAttr=null, $strDefault=null, $bPlaceholder=false, $strId=null, $bSelect2=true){
		return Helper::includeFile('available_fields', [
			'ENTITY_TYPE' => $strEntityType,
			'FIELDS' => $arFields,
			'INPUT_NAME' => $strInputName,
			'VALUE' => $mValue,
			'ATTR' => $strAttr,
			'DEFAULT' => $strDefault,
			'PLACEHOLDER' => $bPlaceholder,
			'ID' => $strId,
			'WITHOUT_SELECT2' => !$bSelect2,
		]);
	}
	
	
	/**
	 *	Filter fields
	 *	$arFilter = [
				'FIELDS' => ['TYPE' => ['N', 'S'], '!CODE' => 'IBLOCK_SECTION_ID'],
				'PROPERTIES' => ['TYPE' => ['S', 'N'], 'CODE' => ['TEST1', 'PROP_%'], 'MULTIPLE' => 'Y'],
				'CATALOG' => '*',
				'PRICES' => '*',
	 *	];
	 */
	protected static function filterFields($arFields, $arFilter){
		# 1. Clear result
		$arResult = $arFields;
		foreach($arResult as $key => $arItems){
			$arResult[$key]['ITEMS'] = [];
		}
		# 2. Do filter
		foreach($arFields as $strType => $arTypeFields){
			if(isset($arFilter[$strType]) && is_array($arTypeFields['ITEMS'])) {
				foreach($arTypeFields['ITEMS'] as $strField => $arField){
					if(!isset($arField['CODE']) || !strlen($arField['CODE'])){
						$arField['CODE'] = $strField;
					}
					if(static::filterField($arField, $arFilter[$strType])){
						$arResult[$strType]['ITEMS'][$strField] = $arField;
					}
				}
			}
		}
		# Return
		return $arResult;
	}
	
	/**
	 *	Filter one field
	 */
	public static function filterField($arField, $arFilter){
		if($arFilter == '*' || $arFilter == ['*'] || $arFilter === true){
			return true;
		}
		$arFilter = is_array($arFilter) ? $arFilter : [];
		$arResult = [];
		foreach($arFilter as $strKey => $mFilterValue){
			$bNegative = substr($strKey, 0, 1) == '!';
			if($bNegative){
				$strKey = substr($strKey, 1);
			}
			$mTestValue = is_array($arField['DATA']) && isset($arField['DATA'][$strKey]) 
				? $arField['DATA'][$strKey] : $arField[$strKey];
			$bFiltered = static::filterFieldItem($mTestValue, $mFilterValue); // arField[$strKey]
			if($bNegative){
				if($bFiltered){
					$arResult[] = 'N';
				}
			}
			else{
				if($bFiltered){
					$arResult[] = 'Y';
				}
				else{
					$arResult[] = 'N';
				}
			}
		}
		$arResult = array_unique($arResult);
		$bResult = empty($arResult) || count($arResult) == 1 && reset($arResult) == 'Y';
		return $bResult;
	}
	
	/**
	 *	Filter one item of one field
	 */
	public static function filterFieldItem($strItem, $mValue){
		if(is_array($mValue) && in_array($strItem, $mValue)){
			return true;
		}
		elseif(is_string($mValue) && strpos($mValue, '%') !== false){
			$strPattern = '#'.str_replace('%', '.*?', $mValue).'#';
			return !!preg_match($strPattern, $strItem);
		}
		elseif(is_string($mValue) && substr($mValue, 0, 1) == '#'){
			return !!preg_match($mValue, $strItem);
		}
		elseif($strItem == $mValue){
			return true;
		}
		elseif($mValue === false && !strlen($strItem)){
			return true;
		}
		elseif(is_array($mValue)){
			foreach($mValue as $mSubValue){
				if(static::filterFieldItem($strItem, $mSubValue)){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Get admin URL for section list
	 *	$arSection must contain ID, IBLOCK_ID, IBLOCK_TYPE_ID, IBLOCK_SECTION_ID
	 */
	public static function getSectionListAdminUrl($arSection){
		$arIBlock = &static::$arCache[static::CACHE_IBLOCK_PARAMS][$arSection['IBLOCK_ID']];
		if(!is_array($arIBlock) && \Bitrix\Main\Loader::includeModule('iblock')){
			$resIBlock = \CIBlock::getList([], ['ID' => $arSection['IBLOCK_ID']]);
			$arIBlock = $resIBlock->getNext(false, false);
		}
		if(is_array($arIBlock)){
			$arAvailableListModes = ['S', 'C'];
			$strListMode = $arIBlock['LIST_MODE'];
			if(!in_array($strListMode, $arAvailableListModes)){
				$strListMode = \Bitrix\Main\Config\Option::get('iblock', 'combined_list_mode') == 'Y' ? 'C' : 'S';
			}
			if(!in_array($strListMode, $arAvailableListModes)){
				$strListMode = reset($arAvailableListModes);
			}
			if($strListMode=='S'){
				return '/bitrix/admin/iblock_section_admin.php?'.http_build_query([
					'IBLOCK_ID' => $arIBlock['ID'],
					'type' => $arIBlock['IBLOCK_TYPE_ID'],
					'find_section_section' => '0',
					'lang' => LANGUAGE_ID,
				]);
			}
			else {
				/*<a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=<?=$arIBlock['ID'];?>&type=<?=$arIBlock['IBLOCK_TYPE_ID'];?>&lang=<?=LANGUAGE_ID;?>&find_section_section=0" target="_blank"><?=$arIBlock['NAME'];?></a>*/
			}
		}
		return false;
	}
	
	/**
	 *	Get all enums for property
	 *	For sections $arSort is a IBLOCK_ID
	 */
	public static function getPropertyEnums($intPropertyId, $arSort=null, $arFilter=null, $arSelect=null, $intLimit=null){
		$arResult = [];
		if(is_numeric($intPropertyId)){
			if(\Bitrix\Main\Loader::includeModule('iblock')){
				$arFilter = is_array($arFilter) ? $arFilter : [];
				$arFilter = array_merge($arFilter, ['PROPERTY_ID' => $intPropertyId]);
				$arSelect = is_array($arSelect) ? $arSelect : ['ID', 'VALUE', 'DEF', 'XML_ID'];
				$arQuery = [
					'order' => is_array($arSort) ? $arSort : ['SORT' => 'ASC', 'VALUE' => 'ASC'],
					'filter' => $arFilter,
					'select' => $arSelect,
				];
				if(is_numeric($intLimit) && $intLimit > 0){
					$arQuery['limit'] = $intLimit;
				}
				$resEnums = \Bitrix\IBlock\PropertyEnumerationTable::getList($arQuery);
				while($arEnum = $resEnums->fetch()){
					$arResult[intVal($arEnum['ID'])] = $arEnum;
				}
				unset($resEnums, $arQuery);
			}
		}
		elseif(static::isSectionProperty($intPropertyId) && is_numeric($arSort)){
			$arUserFieldFilter = ['FIELD_NAME' => $intPropertyId, 'ENTITY_ID' => 'IBLOCK_'.$arSort.'_SECTION'];
			$resUserField = \CUserTypeEntity::getList([], $arUserFieldFilter);
			if($arUserField = $resUserField->fetch()){
				$resEnum = \CUserFieldEnum::getList([], ['USER_FIELD_ID' => $arUserField['ID']]);
				while($arEnum = $resEnum->getNext()){
					$arResult[$arEnum['ID']] = $arEnum;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get all hl-enums for selected property id
	 */
	public static function getPropertyHighloadItems($intPropertyId, $arSort=null, $arFilter=null){
		$arResult = [];
		if(\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')){
			$resProp = \CIBlockProperty::getList([], ['ID' => $intPropertyId]);
			if($arProp = $resProp->getNext(false, false)){
				$arResult = static::getHighloadItems($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'], $arSort, $arFilter);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get all hl-enums
	 */
	public static function getHighloadItems($strHighloadTable, $arSort=null, $arFilter=null, $bId=false){
		$arResult = [];
		$strDataClass = static::getHighloadClass($strHighloadTable);
		if(strlen($strDataClass)){
			$arQuery = [
				'select' => ['ID', 'UF_NAME', 'UF_XML_ID'],
				'order' => ['ID' => 'ASC'],
			];
			if(is_array($arFilter)){
				$arQuery['filter'] = $arFilter;
			}
			$resSata = $strDataClass::getlist($arQuery);
			while($arItem = $resSata->fetch()) {
				$arResult[$bId ? $arItem['ID'] : $arItem['UF_XML_ID']] = $arItem;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get Highload data class name
	 */
	public static function getHighloadClass($strHighloadTable=false){
		if(empty($strHighloadTable)){
			return false;
		}
		$strResult = &static::$arCache[static::CACHE_HIGHLOAD_BLOCKS][$strHighloadTable];
		if(strlen($strResult)){
			return $strResult;
		}
		if(\Bitrix\Main\Loader::includeModule('highloadblock')){
			$arFilter = [];
			if(is_numeric($strHighloadTable)){
				$arHLBlock = HighloadBlock::getById($strHighloadTable)->fetch();
			}
			else{
				$arHLBlock = HighloadBlock::getList(['filter' => ['TABLE_NAME' => $strHighloadTable]])->fetch();
			}
			if($arHLBlock){
				$obEntity = HighloadBlock::compileEntity($arHLBlock);
				$strResult = $obEntity->getDataClass();
			}
		}
		return $strResult;
	}
	
	/**
	 *	Get Highload table fields
	 */
	public static function getHighloadTableFields($strHighloadTable=false){
		if(empty($strHighloadTable)){
			return false;
		}
		$arResult = &static::$arCache[static::CACHE_HIGHLOAD_BLOCKS2][$strHighloadTable];
		if(is_array($arResult)){
			return $arResult;
		}
		if(\Bitrix\Main\Loader::includeModule('highloadblock')){
			$arFilter = [];
			if(is_numeric($strHighloadTable)){
				$arHLBlock = HighloadBlock::getById($strHighloadTable)->fetch();
			}
			else{
				$arHLBlock = HighloadBlock::getList(['filter' => ['TABLE_NAME' => $strHighloadTable]])->fetch();
			}
			$obEntity = HighloadBlock::compileEntity($arHLBlock);
			$arResult = $obEntity->getFields();
		}
		if(!is_array($arResult)){
			$arResult = [];
		}
		return $arResult;
	}
	
	/**
	 *	Transform value for S:directory (it always as UF_XML_ID, we transform to UF_NAME)
	 */
	public static function getSDirectoryValuesByXmlId($strHighloadTable, $mXmlId){
		$mResult = $mXmlId;
		if(is_array($mResult) && !empty($mResult) || strlen($mResult)){
			$arFilter = ['UF_XML_ID' => $mResult];
			$arItems = static::getHighloadItems($strHighloadTable, false, $arFilter, true);
			if(is_array($arItems)){
				if(is_array($mResult)){
					foreach($mResult as $key => $value){
						$mResult[$key] = $arItems[$value]['UF_NAME'];
					}
				}
				else{
					$mResult = $arItems[$mResult]['UF_NAME'];
				}
			}
		}
		return $mResult;
	}
	
	/**
	 *	
	 */
	public static function getPropertyById($intPropertyId, $intIBlockId=null){
		if($intPropertyId){
			$arFilter = [
				'ID' => $intPropertyId,
			];
			if($intIBlockId){
				$arFilter['IBLOCK_ID'] = $intIBlockId;
			}
			$resProp = \CIBlockProperty::getList([], $arFilter);
			if($arProp = $resProp->getNext()){
				return $arProp;
			}
		}
		return false;
	}
	
	/**
	 *	
	 */
	public static function getEnumsL($intPropertyId, $intIBlockId){
		$arResult = [];
		$arSort = [
			'SORT' => 'ASC',
			'VALUE' => 'ASC',
		];
		$arFilter = [
			'IBLOCK_ID' => $intIBlockId,
			'PROPERTY_ID' => $intPropertyId,
		];
		$resEnums = \CIBlockPropertyEnum::getList($arSort, $arFilter);
		while($arEnum = $resEnums->getNext()){
			$arResult[$arEnum['ID']] = $arEnum;
		}
		return $arResult;
	}
	
	/**
	 *	Get elements
	 */
	public static function getElementList($arSort, $arFilter, $arSelect=null, $intLimit=null){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$arSelect = is_array($arSelect) ? $arSelect : [];
			$mNavParams = is_numeric($intLimit) && $intLimit > 0 ? ['nTopCount' => $intLimit] : false;
			return \CIBlockElement::getList($arSort, $arFilter, false, $mNavParams, $arSelect);
		}
		return false;
	}
	
	/**
	 *	Get count of elements
	 */
	public static function getElementCount($arFilter){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			return intVal(\CIBlockElement::getList([], $arFilter, []));
		}
		return false;
	}
	
	/**
	 *	Is available filtering by user fields (sections)
	 */
	public static function isSectionD7Available(){
		return class_exists('\Bitrix\IBlock\Model\Section');
	}
	
	/**
	 *	Get sections
	 *	$arFilter must contain IBLOCK_ID
	 */
	public static function getSectionList($arSort, $arFilter, $arSelect=null, $intLimit=null){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(static::isSectionD7Available()){
				# D7
				$mEntity = \Bitrix\IBlock\Model\Section::compileEntityByIBlock(intVal($arFilter['IBLOCK_ID']));
				if($mEntity){
					$obQuery = new \Bitrix\Main\Entity\Query($mEntity); 
					$obQuery->setOrder($arSort)->setFilter($arFilter);
					if($arSelect){
						$obQuery->setSelect($arSelect);
					}
					if($intLimit){
						$obQuery->setLimit($intLimit);
					}
					if(in_array('IBLOCK_TYPE_ID', $arSelect)){
						\Bitrix\Main\Loader::includeModule('iblock');
						$subQuery = new \Bitrix\Main\Entity\Query(\Bitrix\IBlock\IBlockTable::getEntity());
						$subQuery->setSelect(['IBLOCK_TYPE_ID']);
						$subQuery->setFilter(['ID' => new \Bitrix\Main\DB\SqlExpression('%s')]);
						$obQuery->registerRuntimeField('IBLOCK_TYPE_ID', [
							'expression' => ['('.$subQuery->getQuery().')', 'IBLOCK_ID']
						]);
					}
					//получаем SQL подзапроса
					return $obQuery->exec();
				}
			}
			else{
				# Not D7
				$mNavParams = false;
				if($intLimit > 0){
					$mNavParams = ['iNumPage' => 1, 'nPageSize' => $intLimit];
				}
				return \CIBlockSection::getList($arSort, $arFilter, false, $arSelect, $mNavParams);
			}
		}
		return false;
	}
	
	/**
	 *	Get count of sections
	 *	$arFilter must contain IBLOCK_ID
	 */
	public static function getSectionCount($arFilter){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(static::isSectionD7Available()){
				# D7
				$mEntity = \Bitrix\IBlock\Model\Section::compileEntityByIBlock(intVal($arFilter['IBLOCK_ID']));
				if($mEntity){
					$arRunTime = [
						'data_type' => 'integer',
						'expression' => ['COUNT(%s)', 'ID'],
					];
					$obQuery = new \Bitrix\Main\Entity\Query($mEntity); 
					$obRef = new \ReflectionMethod($obQuery, 'exec');
					$obQuery
						->registerRuntimeField('CNT', $arRunTime)
						->setSelect(['CNT'])
						->setFilter($arFilter);
					$resQueryResult = $obQuery->exec();
					$arQueryResult = $resQueryResult->fetch();
					return intVal($arQueryResult['CNT']);
				}
			}
			else{
				# Not D7
				$intResult = 0;
				$resSections = \CIBlockSection::getList(['ID' => 'ASC'], $arFilter, false, ['ID']);
				while($arSection = $resSections->fetch()){
					$intResult++;
				}
				return $intResult;
			}
		}
		return false;
	}

	/**
	 * Get max sections depth in selected iblock
	 */
	public static function getMaxSectionDepth($intIBlockId){
		$intDepth = 0;
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$arSection = \CIBlockSection::getList(['DEPTH_LEVEL' => 'DESC'], ['IBLOCK_ID' => $intIBlockId], false,
				['ID', 'DEPTH_LEVEL'])->fetch();
			$intDepth = intVal($arSection['DEPTH_LEVEL']);
		}
		return $intDepth;
	}
	
}
