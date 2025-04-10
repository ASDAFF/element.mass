<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class PropertyDeactivator extends PluginElement {
	
	const DUPLICATE_MODE_VALUE = 1;
	const DUPLICATE_MODE_XML_ID = 2;
	
	protected $bSingleTime = true;
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$arProps = $this->getProperties();
		$strMode = $this->get('action_type');
		$bDeactivate = $strMode == 'all' || $strMode == 'deactivate';
		$bActivate = $strMode == 'all' || $strMode == 'activate';
		$arDeactivated = [];
		$arActivated = [];
		foreach($arProps as $intPropId => $arProp){
			if($bDeactivate && $arProp['ACTIVE'] == 'Y' && !$this->isPropertyUsed($intPropId)){
				$this->deactivateProperty($intPropId);
				$arDeactivated[$intPropId] = $arProp['NAME'];
			}
			if($bActivate && $arProp['ACTIVE'] == 'N' && $this->isPropertyUsed($intPropId)){
				$this->activateProperty($intPropId);
				$arActivated[$intPropId] = $arProp['NAME'];
			}
		}
		array_walk($arDeactivated, function(&$item, $key){
			$item = sprintf('<li>%s [%d]</li>', $item, $key);
		});
		array_walk($arActivated, function(&$item, $key){
			$item = sprintf('<li>%s [%d]</li>', $item, $key);
		});
		$strDeactivated = empty($arDeactivated) ? '&ndash;' : sprintf('<ul>%s</ul>', implode('', $arDeactivated)).PHP_EOL;
		$strActivated = empty($arActivated) ? '&ndash;' : sprintf('<ul>%s</ul>', implode('', $arActivated)).PHP_EOL;
		if($bDeactivate){
			$this->debug(static::getMessage('DEBUG_DEACTIVATED').$strDeactivated);
		}
		if($bActivate){
			$this->debug(static::getMessage('DEBUG_ACTIVATED').$strActivated);
		}
		return true;
	}

	/**
	 * Get active properties for current iblock
	 */
	protected function getProperties(){
		$arResult = [];
		$arSort = ['SORT' => 'ASC', 'ID' => 'ASC'];
		$arFilter = ['IBLOCK_ID' => $this->intIBlockId];
		$resProps = \CIBlockProperty::getList($arSort, $arFilter);
		while($arProp = $resProps->fetch()){
			$arResult[$arProp['ID']] = $arProp;
		}
		return $arResult;
	}

	/**
	 * Check property used
	 */
	protected function isPropertyUsed($intPropertyId){
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'!PROPERTY_'.$intPropertyId => false,
		];
		return !!\CIBlockElement::getList([], $arFilter, false, ['nTopCount' => 1], ['ID'])->fetch();
	}

	/**
	 * Activate single property
	 */
	protected function activateProperty($intPropertyId){
		$obProperty = new \CIBlockProperty;
		$result = $obProperty->update($intPropertyId, ['ACTIVE' => 'Y']);
		unset($obProperty);
		return $result;
	}

	/**
	 * Deactivate single property
	 */
	protected function deactivateProperty($intPropertyId){
		$obProperty = new \CIBlockProperty;
		$result = $obProperty->update($intPropertyId, ['ACTIVE' => 'N']);
		unset($obProperty);
		return $result;
	}
	
}

?>