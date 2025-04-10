<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class LinkSection extends PluginElement {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = false;
		$strAction = $this->get('action');
		$mSelectedSectionId = $this->get('section');
		if(!strlen($strAction)){
			$this->setError(static::getMessage('ERROR_NO_ACTION'));
			return false;
		}
		if(is_string($mSelectedSectionId) && !strlen($mSelectedSectionId) && $strAction != 'set' && $strAction != 'correct'){
			$this->setError(static::getMessage('ERROR_NO_SECTION'));
			return false;
		}
		elseif(is_array($mSelectedSectionId) && empty($mSelectedSectionId)){
			$this->setError(static::getMessage('ERROR_NO_SECTION'));
			return false;
		}
		# Get element main section
		$intMainSectionId = null;
		$arFilter = ['ID' => $intElementId];
		$arSelect = ['IBLOCK_SECTION_ID'];
		if($arElement = \CIBlockElement::getList([], $arFilter, false, false, $arSelect)->fetch()){
			$intMainSectionId = intVal($arElement['IBLOCK_SECTION_ID']);
		}
		# Get element groups
		$arElementSectionsId = [];
		if(in_array($strAction, ['change', 'add', 'delete', 'correct'])){
			$resSections = \CIBlockElement::getElementGroups($intElementId, true, ['ID', 'NAME']);
			while ($arSection = $resSections->getNext(false, false)) {
				$arElementSectionsId[] = $arSection['ID'];
			}
		}
		$arElementSectionsIdOriginal = $arElementSectionsId;
		# Process
		if($strAction == 'correct'){
			if(!in_array($intMainSectionId, $arElementSectionsId)){
				$intMainSectionId = !empty($arElementSectionsId) ? reset($arElementSectionsId) : false;
				$this->update($intElementId, ['IBLOCK_SECTION_ID' => $intMainSectionId]);
			}
			$bResult = true;
		}
		elseif($mSelectedSectionId) {
			switch($strAction) {
				case 'set':
					$bResult = $this->update($intElementId, [
						'IBLOCK_SECTION_ID' => $mSelectedSectionId,
					]);
					break;
				case 'change':
					$bResult = $this->update($intElementId, [
						'IBLOCK_SECTION_ID' => $mSelectedSectionId,
						'IBLOCK_SECTION' => $arElementSectionsId,
					]);
					$this->updateElementIndex($intElementId);
					break;
				case 'add':
					$mSelectedSectionId = is_array($mSelectedSectionId) ? $mSelectedSectionId : [$mSelectedSectionId];
					foreach($mSelectedSectionId as $intSelectedSectionId){
						if(!in_array($mSelectedSectionId, $arElementSectionsId)){
							$arElementSectionsId[] = $intSelectedSectionId;
						}
					}
					$this->setElementSection($intElementId, $arElementSectionsId);
					$bResult = true;
					break;
				case 'delete':
					$mSelectedSectionId = is_array($mSelectedSectionId) ? $mSelectedSectionId : [$mSelectedSectionId];
					foreach($mSelectedSectionId as $intSelectedSectionId){
						$mDeleteKey = array_search($intSelectedSectionId, $arElementSectionsId);
						if($mDeleteKey !== false){
							unset($arElementSectionsId[$mDeleteKey]);
						}
					}
					if($arElementSectionsId != $arElementSectionsIdOriginal){
						$this->setElementSection($intElementId, $arElementSectionsId);
						# If new sections array not contains main section, change main section
						if(!is_null($intMainSectionId) && !in_array($intMainSectionId, $arElementSectionsId)){
							$intMainSectionId = !empty($arElementSectionsId) ? reset($arElementSectionsId) : false;
							$this->update($intElementId, ['IBLOCK_SECTION_ID' => $intMainSectionId]);
						}
					}
					$bResult = true;
					break;
			}
		}
		elseif($strAction == 'set' && is_string($mSelectedSectionId) && !strlen($mSelectedSectionId)){
			$this->setElementSection($intElementId, []);
			$bResult = true;
		}
		# Trigger for update
		if($bResult){
			$this->update($intElementId, [
				'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
			]);
		}
		return $bResult;
	}
	
}

?>