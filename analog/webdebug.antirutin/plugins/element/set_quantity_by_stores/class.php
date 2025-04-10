<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class SetQuantityByStores extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	protected $arFieldsFilter = [
		'CATALOG' => ['CODE' => ['STORE_AMOUNT_%']],
	];
	
	protected $arFieldsFilterSaveProduct = [
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S', 'N']],
		'CATALOG' => ['CODE' => ['STORE_AMOUNT_%']],
	];
	
	protected $arFieldsFilterSaveOffer = [
		'OFFERS.PROPERTIES' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S', 'N']],
		'OFFERS.CATALOG' => ['CODE' => ['STORE_AMOUNT_%']],
	];

	private $fOffersSumm = 0;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('stores') || !is_array($this->get('stores'))){
			$this->setError(static::getMessage('ERROR_NO_STORES'));
			return false;
		}
		$bResultProduct = false;
		$bResultOffers = false;
		$bProduct = in_array($this->get('mode'), ['product', 'all']);
		$bOffers = in_array($this->get('mode'), ['offers', 'all']);
		if($bProduct){
			$bResultProduct = $this->processSingleElement($intElementId);
		}
		if($bOffers){
			$bResultOffers = $this->processElementOffers($intElementId);
		}
		return $bResultProduct || $bResultOffers;
	}
	
	/**
	 *	Process product or offer
	 */
	protected function processSingleElement($intElementId, $intIBlockId=null, $bSaveOffersSumm=false){
		$arFeatures = ['STORES' => true];
		$intIBlockId = is_numeric($intIBlockId) ? $intIBlockId : $this->intIBlockId;
		$arElement = IBlock::getElementArray($intElementId, $intIBlockId, $arFeatures);
		$fAmount = 0;
		foreach($this->get('stores') as $strField){
			if($intStoreId = $this->isStoreAmount($strField)){
				if(is_array($arElement['STORES'][$intStoreId])){
					$fAmount += floatVal($arElement['STORES'][$intStoreId]['AMOUNT']);
				}
			}
		}
		$bOffer = $intIBlockId != $this->intIBlockId;
		$bSaveToProperty = $bOffer && $this->get('offer_save_to_property') == 'Y'
			|| !$bOffer && $this->get('product_save_to_property') == 'Y';
		if($bSaveOffersSumm){
			$this->fOffersSumm = $fAmount;
			return true;
		}
		if($bSaveToProperty){
			$intPropertyId = null;
			$strField = $this->get(($bOffer ? 'offer' : 'product').'_save_property');
			if($intProductPropId = $this->isProperty($strField)){
				$intPropertyId = $intProductPropId;
			}
			elseif($strOfferField = $this->isOfferField($strField)){
				if($intOfferPropId = $this->isProperty($strOfferField)){
					$intPropertyId = $intOfferPropId;
				}
				elseif($intStoreId = $this->isStoreAmount($strOfferField)){
					Helper::setProductStoreAmount($intElementId, $intStoreId, $fAmount);
					return true;
				}
			}
			elseif($intStoreId = $this->isStoreAmount($strField)){
				Helper::setProductStoreAmount($intElementId, $intStoreId, $fAmount);
				return true;
			}
			if($intPropertyId){
				$this->setPropertyValue($intElementId, $intPropertyId, $fAmount, null, $intIBlockId);
				return true;
			}
		}
		else{
			return Helper::setProductField($intElementId, 'QUANTITY', $fAmount);
		}
	}
	
	/**
	 *	
	 */
	protected function processElementOffers($intElementId){
		$bResult = false;
		$arCatalog = Helper::getCatalogArray($this->intIBlockId);
		if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] && $arCatalog['OFFERS_PROPERTY_ID']){
			$fSumm = 0;
			$bSaveOffersSumm = $this->get('save_offers_summ_to_property') == 'Y';
			$arFilter = [
				'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
				'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $intElementId,
			];
			$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
			while($arOffer = $resOffers->getNext(false, false)){
				$bOfferResult = $this->processSingleElement($arOffer['ID'], $arCatalog['OFFERS_IBLOCK_ID'], $bSaveOffersSumm);
				if($bSaveOffersSumm){
					$fSumm += $this->fOffersSumm;
				}
				if($bOfferResult){
					$bResult = true;
				}
			}
			if($bResult && $bSaveOffersSumm){
				$strField = $this->get('save_offers_summ_property');
				if($intPropertyId = $this->isProperty($strField)){
					$this->setPropertyValue($intElementId, $intPropertyId, $fSumm, null, $this->intIBlockId);
				}
				elseif($intStoreId = $this->isStoreAmount($strField)){
					Helper::setProductStoreAmount($intElementId, $intStoreId, $fSumm);
				}
			}
		}
		return $bResult;
	}
	
}

?>