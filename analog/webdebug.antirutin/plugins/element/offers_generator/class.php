<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class OfferGenerator extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	protected $arFields_ModeDefault_Offers = [
		'OFFERS.PROPERTIES' => ['TYPE_FULL' => ['L', 'S:directory']],
	];

	private $_arCatalog = null;
	private $_obElement = null;
	private $_intOfferSort = 0;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = false;
		switch($this->get('mode')){
			case 'simple':
				$bResult = $this->processElement_Simple($intElementId);
				break;
			default:
				$bResult = $this->processElement_Default($intElementId);
				break;
		}
		return $bResult;
	}

	protected function getModes(){
		return [
			'default' => static::getMessage('MODE_DEFAULT'),
			'simple' => static::getMessage('MODE_SIMPLE'),
		];
	}
	
	// DEFAULT
	protected function processElement_Default($intElementId){
		$this->_arCatalog = Helper::getCatalogArray($this->intIBlockId);
		if(!$this->_arCatalog['OFFERS_IBLOCK_ID'] || !$this->_arCatalog['OFFERS_PROPERTY_ID']){
			$this->setError(static::getMessage('ERROR_DEFAULT_NO_OFFERS'));
			return false;
		}
		// Check props
		$arPropsId = [];
		if(is_array($arProps = $this->get('default_props'))){
			foreach($arProps as $strProp){
				if($strOfferField = $this->isOfferField($strProp)){
					if($intPropId = $this->isProperty($strOfferField)){
						$intPropId = intVal($intPropId);
						if($intPropId > 0){
							$arPropsId[$intPropId] = $intPropId;
						}
					}
				}
			}
		}
		$arProps = [];
		if(!empty($arPropsId)){
			$arQuery = ['filter' => ['ID' => $arPropsId]];
			$resProps = \Bitrix\IBlock\PropertyTable::getList($arQuery);
			while($arProp = $resProps->fetch()){
				$strType = $arProp['PROPERTY_TYPE'].(strlen($arProp['USER_TYPE']) ? ':'.$arProp['USER_TYPE'] : '');
				if(in_array($strType, ['L', 'S:directory'])){
					$arProp['TYPE_FULL'] = $strType;
					$arProp['ITEMS'] = $this->getPropertyValues($arProp);
					if(!empty($arProp['ITEMS'])){
						$arProps[$arProp['ID']] = $arProp;
					}
				}
			}
		}
		if(empty($arProps)){
			$this->setError(static::getMessage('ERROR_DEFAULT_NO_PROPS'));
			return false;
		}
		// Get current element data
		$arFeatures = [
			'FIELDS' => true,
			'CATALOG' => true,
			'PRICES' => true,
		];
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		// Generate offers
		$this->_obElement = new \CIBlockElement;
		$this->_intOfferSort = 0;
		$bResult = $this->generateOffers($arElement, $arProps);
		if($bResult){
			\CCatalogProduct::update($intElementId, ['TYPE' => \Bitrix\Catalog\ProductTable::TYPE_SKU]);
		}
		return $bResult;
	}

	protected function getPropertyValues(array $arProp):?array{
		$arResult = [];
		switch($arProp['TYPE_FULL']){
			case 'L':
				$arQuery = [
					'order' => ['SORT' => 'ASC', 'VALUE' => 'ASC'],
					'filter' => ['PROPERTY_ID' => $arProp['ID']],
					'select' => ['ID', 'VALUE'],
				];
				$resEnums = \Bitrix\IBlock\PropertyEnumerationTable::getList($arQuery);
				while($arEnum = $resEnums->fetch()){
					$arResult[$arEnum['ID']] = $arEnum['VALUE'];
				}
				break;
			case 'S:directory':
				if(!isset($arProp['USER_TYPE_SETTINGS_LIST']) || !is_array($arProp['USER_TYPE_SETTINGS_LIST'])){
					return [];
				}
				if(!isset($arProp['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])){
					return [];
				}
				if(!strlen($strTable = $arProp['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])){
					return [];
				}
				if(!\Bitrix\Main\Loader::includeModule('highloadblock')){
					return [];
				}
				$arQuery = ['filter' => ['TABLE_NAME' => $strTable]];
				if(!($arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList($arQuery)->fetch())){
					return [];
				}
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				$strEntityDataClass = $obEntity->getDataClass();
				$arQuery = [
					'order' => ['UF_NAME' => 'ASC'],
					'select' => ['UF_NAME', 'UF_XML_ID'],
				];
				$resEnums = $strEntityDataClass::getlist($arQuery);
				while($arItem = $resEnums->fetch()) {
					$arResult[$arItem['UF_XML_ID']] = $arItem['UF_NAME'];
				}
				break;
		}
		return $arResult;
	}

	protected function generateOffers(array $arElement, array $arProps, array $arChain=[]){
		// Base case
		if(empty($arProps)){
			if(!$this->saveOffer($arElement, $arChain)){
				return false;
			}
		}
		// Recurrent case
		else{
			$intPropId = key($arProps);
			$arProp = reset($arProps);
			unset($arProps[$intPropId]);
			foreach($arProp['ITEMS'] as $key => $value){
				$arChain[$intPropId] = [
					'TYPE_FULL' => $arProp['TYPE_FULL'],
					'VALUE' => $key,
					'TITLE' => $value,
				];
				if(!$this->generateOffers($arElement, $arProps, $arChain)){
					return false;
				}
			}
		}
		return true;
	}

	protected function saveOffer(array $arElement, array $arChain=[]){
		$bResult = true;
		// Build offer filter
		$arOfferFilter = [
			'IBLOCK_ID' => $this->_arCatalog['OFFERS_IBLOCK_ID'],
			'PROPERTY_'.$this->_arCatalog['OFFERS_PROPERTY_ID'] => $arElement['ID'],
		];
		foreach($arChain as $intPropId => $arItem){
			$arOfferFilter['PROPERTY_'.$intPropId] = $arItem['VALUE'];
		}
		// Check exists
		$arExistOffer = \CIBlockElement::getList([], $arOfferFilter, false, ['nTopCount' => 1], ['ID'])->fetch();
		if($arExistOffer){
			$intOfferId = $arExistOffer['ID'];
		}
		else{
			$arCode = [$arElement['CODE']];
			foreach($arChain as $intPropId => $arItem){
				$arCode[] = $arItem['TITLE'];
			}
			$arOfferFields = [
				'IBLOCK_ID' => $this->_arCatalog['OFFERS_IBLOCK_ID'],
				'NAME' => $arElement['NAME'],
				'CODE' => \CUtil::translit(implode('_', $arCode), 'ru', [
					'max_len' => 255,
					'change_case' => 'L',
					'replace_space' => '_',
					'replace_other' => '_',
					'delete_repeat_replace' => true,
					'use_google' => false,
				]),
				'ACTIVE' => 'Y',
				'SORT' => ++$this->_intOfferSort,
				'PROPERTY_VALUES' => [
					$this->_arCatalog['OFFERS_PROPERTY_ID'] => $arElement['ID'],
	
				],
			];
			foreach($arChain as $intPropId => $arItem){
				$arOfferFields['PROPERTY_VALUES'][$intPropId] = $arItem['VALUE'];
			}
			$intOfferId = $this->_obElement->add($arOfferFields);
			if(!$intOfferId){
				$bResult = false;
				$this->setError($this->_obElement->LAST_ERROR);
			}
		}
		// Set price
		if($intOfferId && isset($arElement['PRICES']) && is_array($arElement['PRICES'])){
			foreach($arElement['PRICES'] as $arPrice){
				Helper::setProductPrice($intOfferId, $arPrice['CATALOG_GROUP_ID'], $arPrice['PRICE'], $arPrice['CURRENCY']);
				if($arPrice['EXTRA_ID']){
					Helper::setProductPriceExtra($intOfferId, $arPrice['CATALOG_GROUP_ID'], $arPrice['EXTRA_ID']);
				}
			}
		}
		//
		return $bResult;
	}

	// SIMPLE
	protected function processElement_Simple($intElementId){
		$intCount = $this->get('count');
		$strActive = $this->get('active') == 'Y' ? 'Y' : 'N';
		$bCopyPrices = $this->get('copy_prices') == 'Y';
		
		# Check input values
		if(!is_numeric($intCount) || $intCount <= 0){
			$this->setError(static::getMessage('ERROR_SIMPLE_NO_COUNT'));
			return false;
		}
		if(!($arCatalog = Helper::getCatalogArray($this->intIBlockId))){
			$this->setError(static::getMessage('ERROR_SIMPLE_IBLOCK_HAS_NO_OFFERS'));
			return false;
		}
		
		# Set features for get element
		$arFeatues = ['FIELDS' => ['NAME', 'CODE']];
		if($bCopyPrices){
			$arFeatues['PRICES'] = true;
		}
		
		# Get current element
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatues);
		if(!strlen($arElement['CODE'])){
			$arElement['CODE'] = \CUtil::translit($arElement['~NAME'], LANGUAGE_ID, [
				'max_len' => 255,
				'change_case' => 'L',
				'replace_space' => '_',
				'replace_other' => '_',
				'delete_repeat_replace' => true,
			]);
		}
		
		# Create offers
		$obElement = new \CIBlockElement;
		for($i = 1; $i <= $intCount; $i++){
			$strRand = randString(8);
			$arOfferFields = [
				'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
				'ACTIVE' => 'N',
				'NAME' => $arElement['~NAME'],
				'CODE' => sprintf('%s_%s', $arElement['CODE'], toLower($strRand)), // before add we do not know offer ID
				'PROPERTY_VALUES' => [
					$arCatalog['OFFERS_PROPERTY_ID'] => $intElementId,
				],
			];
			if($intOfferId = $obElement->add($arOfferFields, false, false, false)){
				$obElement->update($intOfferId, [
					'ACTIVE' => $strActive,
					'NAME' => $arElement['~NAME'],
					'CODE' => sprintf('%s_%s', $arElement['CODE'], $intOfferId), // Now we know offer ID
				]);
				if($bCopyPrices && $arElement['PRICES']){
					foreach($arElement['PRICES'] as $intPriceId => $arPrice){
						Helper::setProductPrice($intOfferId, $intPriceId, $arPrice['PRICE'], $arPrice['CURRENCY']);
						if($arPrice['EXTRA_ID']){
							Helper::setProductPriceExtra($intOfferId, $intPriceId, $arPrice['EXTRA_ID']);
						}
					}
				}
			}
		}
		# Change product type
		if($intOfferId){
			\CCatalogProduct::update($intElementId, ['TYPE' => \Bitrix\Catalog\ProductTable::TYPE_SKU]);
		}

		# Return
		return $intOfferId ? true : false;
	}
	
}

?>