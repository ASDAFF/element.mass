<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class FredDiscount extends PluginElement {
	
	const GROUP = 'MODULES';

	const FRED_DISCOUNT = 'fred.discount';
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if(!$this->isFredDiscountModuleInstalled()){
			$this->setError(static::getMessage('ERROR_NO_MODULE'));
			return false;
		}
		if($this->getFredDiscountOption('IBLOCK_ID') != $this->intIBlockId){
			$this->setError(static::getMessage('ERROR_WRONG_IBLOCK_ID', ['#IBLOCK_ID#' => $this->getFredDiscountOption('IBLOCK_ID')]));
			return false;
		}
		$strPropertyCode = $this->getFredDiscountOption('DISCOUNT_PROPERTY');
		if(strlen($strPropertyCode) && ($intPropertyId = $this->getPropertyIdByCode($strPropertyCode))){
			$arFeatures = [
				'FIELDS' => ['NAME'],
				'PROPERTY_ID' => [$intPropertyId],
			];
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
			if(is_array($arElement['PROPERTIES'][$intPropertyId]['VALUE'])){
				if(count($arElement['PROPERTIES'][$intPropertyId]['VALUE'])){
					$arElement['PROPERTIES'][$intPropertyId]['VALUE'] = reset($arElement['PROPERTIES'][$intPropertyId]['VALUE']);
				}
				else{
					$arElement['PROPERTIES'][$intPropertyId]['VALUE'] = 0;
				}
			}
			$arSaveFields = [
				'ID' => $arElement['ID'],
				'NAME' => $arElement['NAME'],
				'IBLOCK_ID' => $this->intIBlockId,
				'PROPERTY_VALUES' => [
					$intPropertyId => [
						'n0' => ['VALUE' => $arElement['PROPERTIES'][$intPropertyId]['VALUE']],
					],
				],
			];
			\FRED\Iblock::setDiscount($arSaveFields);
			return true;
		}
		else{
			$this->setError(static::getMessage('ERROR_EMPTY_PROPERTY'));
		}
		return false;
	}
	
	/**
	 *	Is module 'fred.discount' installed?
	 */
	protected function isFredDiscountModuleInstalled(){
		return \Bitrix\Main\Loader::includeModule(static::FRED_DISCOUNT);
	}

	/**
	 *	Get option for 'fred.discount'
	 */
	protected function getFredDiscountOption($strOption){
		return \Bitrix\Main\Config\Option::get(static::FRED_DISCOUNT, $strOption);
	}

	/**
	 *	Get property ID by CODE
	 */
	protected function getPropertyIdByCode($strPropertyCode){
		$arProperty = \Bitrix\IBlock\PropertyTable::getList([
			'filter' => ['IBLOCK_ID' => $this->intIBlockId, 'CODE' => $strPropertyCode],
		])->fetch();
		return is_array($arProperty) ? intVal($arProperty['ID']) : false;
	}
	
}

?>