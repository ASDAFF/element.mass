<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class DeleteOffers extends PluginElement {
	
	const GROUP = 'CATALOG';
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = true;
		$arCatalog = Helper::getCatalogArray($this->intIBlockId);
		if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID'] && $arCatalog['OFFERS_PROPERTY_ID']){
			$arFilter = [
				'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
				'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $intElementId,
			];
			if($this->get('mode') == 'inactive'){
				$arFilter['ACTIVE'] = 'N';
			}
			$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
			while($arOffer = $resOffers->getNext()){
				if(!\CIBlockElement::delete($arOffer['ID'])){
					$this->setError(static::getMessage('ERROR_DELETE_OFFERS', ['#OFFER_ID#' => $arOffer['ID']]));
					$bResult = false;
					break;
				}
			}
		}
		return $bResult;
	}
	
}

?>