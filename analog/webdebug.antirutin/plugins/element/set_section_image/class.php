<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class SetSectionImage extends PluginElement {
	
	const GROUP = 'IMAGES';
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$arSession = &$this->obWorker->getPluginSession();
		if(!is_array($arSession['SECTIONS_ID'])){
			$arSession['SECTIONS_ID'] = [];
		}
		$strPictureElement = $this->get('picture_type_element') == 'preview' ? 'PREVIEW_PICTURE' : 'DETAIL_PICTURE';
		$strPictureSection = $this->get('picture_type_section') == 'preview' ? 'PICTURE' : 'DETAIL_PICTURE';
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['FIELDS' => [$strPictureElement]], true);
		if(is_array($arElement[$strPictureElement])){
			if($arElement['IBLOCK_SECTION_ID'] && !in_array($arElement['IBLOCK_SECTION_ID'], $arSession['SECTIONS_ID'])){
				$arSession['SECTIONS_ID'][] = $arElement['IBLOCK_SECTION_ID'];
				$obSection = new \CIBlockSection();
				$arUpdateFields = [
					$strPictureSection => \CFile::makeFileArray($arElement[$strPictureElement]['ID']),
				];
				if($obSection->update($arElement['IBLOCK_SECTION_ID'], $arUpdateFields)){
					return true;
				}
				$this->setError($obSection->LAST_ERROR);
				return false;
			}
		}
		return true;
	}
	
}

?>