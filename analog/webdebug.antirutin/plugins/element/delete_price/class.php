<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\PluginElement;


class DeletePrice extends PluginElement {

	const GROUP = 'CATALOG';
	protected $arFieldsFilter = [
		'PRICES' => ['IS_WRITEABLE' => 'Y'],
	];

	public function processElement($intElementId): bool {
		if($this->isEmpty('price_type')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_PRICE'));
			return false;
		}
		$bResult = false;
		if(\Bitrix\Main\Loader::includeModule('catalog')) {
			$arPriceTypes = $this->getPriceTypesId($this->get('price_type'));
			$arPricesId = $this->getProductPriceId($intElementId, $arPriceTypes);
			foreach ($arPricesId as $intDelPriceId) {
				$bResult = \Bitrix\Catalog\PriceTable::Delete($intDelPriceId);
				if (!$bResult) {
					break;
				}
			}
		}
		return !!$bResult;
	}

	protected function getPriceTypesId(array $arPriceTypes) : array {
		$arResult = [];
		foreach ($arPriceTypes as $strPriceType){
			$arResult[] = $this->isPrice($strPriceType);
		}
		return $arResult;
	}

	protected function getProductPriceId($intElementId, array $arPriceTypes) : array{
		$arResult = [];
		$arProductPricesId = \Bitrix\Catalog\PriceTable::getList([
			'select' => ['ID'],
			'filter' => [
				'PRODUCT_ID' => $intElementId,
				'=CATALOG_GROUP_ID' => $arPriceTypes,
			],
			'order' => ['CATALOG_GROUP_ID' => 'ASC']
		])->fetchAll();
		foreach ($arProductPricesId as $item){
			$arResult[] = $item['ID'];
		}
		return $arResult;
	}
}