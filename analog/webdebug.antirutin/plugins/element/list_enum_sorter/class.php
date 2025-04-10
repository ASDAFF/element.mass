<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ListEnumSorter extends PluginElement {
	
	protected $bSingleTime = true;
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE_FULL' => 'L', 'IS_WRITEABLE' => 'Y'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$strField = $this->get('field');
		$intSort = intVal($this->get('sort'));
		if(!strlen($strField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		if($intSort <= 0){
			$this->setError(static::getMessage('ERROR_NO_SORT'));
			return false;
		}
		if($intPropertyId = $this->isProperty($strField)){
			$arEnums = IBlock::getPropertyEnums($intPropertyId, false, false, ['ID', 'SORT']);
			foreach($arEnums as $intEnumId => $arEnum){
				if($arEnum != $intSort){
					\Bitrix\IBlock\PropertyEnumerationTable::update(['ID' => $intEnumId, 'PROPERTY_ID' => $intPropertyId], ['SORT' => $intSort]);
				}
			}
			return true;
		}
		return false;
	}
	
}

?>