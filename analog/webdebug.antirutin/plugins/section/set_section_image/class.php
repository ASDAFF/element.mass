<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\PluginSection;

class SetSectionImage extends PluginSection {
	
	const GROUP = 'IMAGES';
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		$strPictureElement = $this->get('picture_type_element') == 'preview' ? 'PREVIEW_PICTURE' : 'DETAIL_PICTURE';
		$strPictureSection = $this->get('picture_type_section') == 'preview' ? 'PICTURE' : 'DETAIL_PICTURE';
		#
		$arSort = [
			'ID' => 'ASC',
		];
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'SECTION_ID' => $intSectionId,
			'INCLUDE_SUBSECTIONS' => 'Y',
			'ACTIVE' => 'Y',
			'!'.$strPictureElement => false,
		];
		$arNavParams = ['nTopCount' => 1];
		$arSelect = ['ID', $strPictureElement];
		#
		if($arElement = \CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect)->fetch()){
			$bResult = $this->update($intSectionId, [
				$strPictureSection => \CFile::makeFileArray($arElement[$strPictureElement]),
			]);
			if(!$bResult){
				return false;
			}
		}
		return true;
	}
	
}

?>