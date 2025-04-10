<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ListEnumDuplicateRemover extends PluginElement {
	
	const DUPLICATE_MODE_VALUE = 1;
	const DUPLICATE_MODE_XML_ID = 2;
	
	protected $bSingleTime = true;
	
	protected $arFieldsFilter = [
		'PROPERTIES' => ['TYPE_FULL' => ['L', 'S:directory']/*, 'IS_WRITEABLE' => 'Y'*/],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$strField = $this->get('field');
		if(!strlen($strField)){
			$this->setError(static::getMessage('ERROR_NO_FIELD'));
			return false;
		}
		if($intPropertyId = $this->isProperty($strField)){
			$arProperty = IBlock::getPropertyById($intPropertyId, $this->intIBlockId);
			if($arProperty['PROPERTY_TYPE'] == 'L'){
				$arUniqueEnums = [];
				$arQuery = [
					'order' => ['SORT' => 'ASC', 'ID' => 'ASC'],
					'filter' => ['PROPERTY_ID' => $intPropertyId],
					'select' => ['ID', 'VALUE', 'XML_ID'],
				];
				$resEnums = \Bitrix\IBlock\PropertyEnumerationTable::getList($arQuery);
				while($arEnum = $resEnums->fetch()){
					$intEnumId = intVal($arEnum['ID']);
					if(in_array($arEnum['VALUE'], $arUniqueEnums)){
						\Bitrix\IBlock\PropertyEnumerationTable::delete(['ID' => $intEnumId, 'PROPERTY_ID' => $intPropertyId]);
					}
					else{
						$arUniqueEnums[$intEnumId] = $arEnum['VALUE'];
					}
				}
				return true;
			}
			elseif($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'directory'){
				$strHlTableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];
				if(strlen($strHlTableName)){
					if(\Bitrix\Main\Loader::includeModule('highloadblock')){
						$strDataClass = IBlock::getHighloadClass($strHlTableName);
						if(strlen($strDataClass)){
							$arUniqueEnums = [];
							$arQuery = [
								'order' => ['ID' => 'ASC'],
								'select' => ['ID', 'UF_NAME'],
							];
							$resSata = $strDataClass::getlist($arQuery);
							while($arItem = $resSata->fetch()) {
								$intItemId = intVal($arItem['ID']);
								if(in_array($arItem['UF_NAME'], $arUniqueEnums)){
									$strDataClass::delete($intItemId);
								}
								else{
									$arUniqueEnums[$intItemId] = $arItem['UF_NAME'];
								}
							}
							return true;
						}
					}
				}
			}
			
		}
		return false;
	}
	
}

?>