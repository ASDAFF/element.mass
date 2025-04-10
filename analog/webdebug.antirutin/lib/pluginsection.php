<?
namespace WD\Antirutin;

use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock,
	WD\Antirutin\Plugin;

abstract class PluginSection extends Plugin {
	
	/**
	 *	Disable process section!
	 */
	public function processSection($intSectionId){
		return false;
	}
	
	/**
	 *	Execute element!
	 *	must return true if success, false on error
	 */
	final public function processElement($intElementId){
		return false;
	}
	
	/**
	 *	Wrapper for CIBlockSection::update()
	 */
	protected function update($intSectionId, $arFields, $bResort=true, $bUpdateSearch=true, $bResizePictures=false){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(!is_object($this->obSection)){
				$this->obSection = new \CIBlockSection;
			}
			$bResult = $this->obSection->update($intSectionId, $arFields, $bResort, $bUpdateSearch, $bResizePictures);
			if($bResult){
				if(isset($arFields['IPROPERTY_TEMPLATES'])){
					$obValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($this->intIBlockId, $intSectionId);
					$obValues->clearValues();
					unset($obValues);
				}
			}
			else{
				$strError = preg_replace('#^(.*?)<br/?>\s?$#', '$1', $this->obSection->LAST_ERROR);
				$this->setError($strError);
			}
			return $bResult;
		}
		return false;
	}
	
	/**
	 *	Wrapper for getFields
	 */
	protected function getProperty($strPropertyCode, $bJustData=true){
		$arProperty = $this->getFields(['PROPERTIES' => ['CODE' => $strPropertyCode]], $strPropertyCode);
		if($bJustData){
			$arProperty = $arProperty['DATA'];
		}
		return $arProperty;
	}
	
	/**
	 *	Set SEO field for section
	 */
	protected function setSeoField($intSectionId, $strKey, $strValue){
		$obProps = new \Bitrix\IBlock\InheritedProperty\SectionTemplates($this->intIBlockId, $intSectionId);
		$obProps->set([
			$strKey => $strValue,
		]);
		unset($obProps);
	}
	
	/**
	 *	Check property multiple or not
	 */
	protected function isPropertyMultiple($strPropertyCode){
		$bResult = false;
		$arField = IBlock::getSectionUserFields($this->intIBlockId, $strPropertyCode);
		if(is_array($arField)){
			$bResult = $arField['MULTIPLE'] == 'Y';
		}
		return $bResult;
	}
	
}

?>