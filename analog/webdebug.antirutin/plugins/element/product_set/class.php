<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ProductSet extends PluginElement {

	const GROUP = 'SETS';
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = false;
		if(\Bitrix\Main\Loader::IncludeModule('catalog') && class_exists('CCatalogProductSet')) {

			# Prepare
			$arProducts = $this->getSavedProducts();
			$intType = intVal($this->get('type'));
			$intProductType = $intType == 1 ? \CCatalogProductSet::TYPE_SET : \CCatalogProductSet::TYPE_GROUP;
			$obProductSet = new \CCatalogProductSet;

			# Prepare set items
			$arSetItems = [];
			foreach($arProducts as $arProduct){
				$arSetItem = [
					'ACTIVE' => 'Y',
					'ITEM_ID' => $arProduct['id'],
					'QUANTITY' => $arProduct['amount'],
					'SORT' => $arProduct['sort'],
				];
				if($intProductType === \CCatalogProductSet::TYPE_SET) {
					$arSetItem['DISCOUNT_PERCENT'] = $arProduct['percent'];
				}
				$arSetItems[] = $arSetItem;
			}

			# Get current set
			$arCurrentProductSets = \CCatalogProductSet::getAllSetsByProduct($intElementId, $intProductType);

			# Update exist set
			if(is_array($arCurrentProductSets) && !empty($arCurrentProductSets)) {
				$arCurrentProductSet = array_shift($arCurrentProductSets); # because result is [123 => [data]]
				$arSetFields = [
					'ITEMS' => $arSetItems,
				];
				if($obProductSet->update($arCurrentProductSet['SET_ID'], $arSetFields)) {
					$obProductSet->recalculateSetsByProduct($intElementId);
					$bResult = true;
				}
				else {
					$this->setError(static::getMessage('ERROR_UPD_SET_'.$intType, [
						'#ERROR#' => $this->errorsToString($obProductSet->getErrors()),
					]));
				}
			}
			# Add new set
			else {
				$arSetFields = [
					'ACTIVE' => 'Y',
					'TYPE' => $intProductType,
					'ITEM_ID' => $intElementId,
					'ITEMS' => $arSetItems,
				];
				if($obProductSet->add($arSetFields)) {
					$obProductSet->recalculateSetsByProduct($intElementId);
					$bResult = true;
				}
				else {
					$this->setError(static::getMessage('ERROR_ADD_SET_'.$intType, [
						'#ERROR#' => $this->errorsToString($obProductSet->getErrors()),
					]));
				}
			}
			unset($obProductSet);

		}
		return $bResult;
	}

	public function getSavedProducts(){
		$arResult = [];
		$arData = $this->get('products');
		if(!is_array($arData)){
			$arData = [
				'id' => [],
				'quantity' => [],
				'sort' => [],
				'percent' => [],
			];
		}
		foreach ($arData as $key => $subarr) {
			foreach ($subarr as $subkey => $subvalue) {
				$arResult[$subkey][$key] = $subvalue;
			}
		}
		foreach($arResult as $key => $arItem){
			if(!strlen($arItem['id'])){
				unset($arResult[$key]);
			}
		}
		foreach($arResult as $key => $arItem){
			$arItem['id'] = intVal($arItem['id']);
			$arItem['amount'] = floatVal($arItem['amount']);
			$arItem['sort'] = intVal($arItem['sort']);
			$arItem['percent'] = floatVal($arItem['percent']);
			$arResult[$key] = $arItem;
		}
		uasort($arResult, function($a, $b){
			return $a['sort'] - $b['sort'];
		});
		return $arResult;
	}

	public function errorsToString($arErrors){
		if(!is_array($arErrors) || empty($arErrors)){
			return '-';
		}
		return implode(', ', array_column($arErrors, 'text'));
	}

}
