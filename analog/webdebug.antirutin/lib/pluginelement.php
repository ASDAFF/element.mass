<?
namespace WD\Antirutin;

use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock,
	WD\Antirutin\Plugin;

abstract class PluginElement extends Plugin {
	
	/**
	 *	Execute element!
	 *	must return true if success, false on error
	 */
	public function processElement($intElementId){
		return false;
	}
	
	/**
	 *	Disable process section!
	 */
	final public function processSection($intSectionId){
		return false;
	}
	
	/**
	 *	Check field is offer field, and return this offer field (without OFFERS.) or false
	 */
	protected function isOfferField($strField){
		if(strpos($strField, IBlock::PREFIX_OFFERS) === 0){
			return substr($strField, strlen(IBlock::PREFIX_OFFERS));
		}
		return false;
	}
	
	/**
	 *	Get offers (just ID)
	 */
	protected function getElementOffers($intElementId, $arOfferFilter=null){
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['OFFERS' => true], true, $arOfferFilter);
		return is_array($arElement['OFFERS']) ? $arElement['OFFERS'] : [];
	}
	
	/**
	 *	Wrapper for CIBlockElement::update()
	 */
	protected function update($intElementId, $arFields, $bUpdateSearch=true, $bResizePictures=false, $bCheckDiskQuota=true){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(!is_object($this->obElement)){
				$this->obElement = new \CIBlockElement;
			}
			$bResult = $this->obElement->update($intElementId, $arFields, $bWorkFlow=false, $bUpdateSearch, $bResizePictures, $bCheckDiskQuota);
			if($bResult){
				if(isset($arFields['IPROPERTY_TEMPLATES'])){
					$obValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($this->intIBlockId, $intElementId);
					$obValues->clearValues();
					unset($obValues);
				}
			}
			else{
				$strError = preg_replace('#^(.*?)<br/?>\s?$#', '$1', $this->obElement->LAST_ERROR);
				$this->setError($strError);
			}
			return $bResult;
		}
		return false;
	}
	
	/**
	 *	Wrapper for CIBlockElement::setPropertyValuesEx()
	 */
	protected function setPropertyValue($intElementId, $intPropertyId=null, $mValue=null, $arValue=null, $intIBlockId=null){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			if(!is_array($arValue)){
				$arValue = [$intPropertyId => $mValue];
			}
			$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
			$arProperty = $this->getProperty($intPropertyId, true, $intIBlockId);
			if($arProperty['MULTIPLE'] == 'Y'){
				if($this->isSaveValues()){
					$arCurrentPropertyValue = $this->getPropertyValue($intElementId, $intPropertyId, $intIBlockId);
					if(!is_array($arCurrentPropertyValue)){
						$arCurrentPropertyValue = [$arCurrentPropertyValue];
					}
					if(!empty($arCurrentPropertyValue) && is_array($arValue[$intPropertyId])){
						$arValuesTmp = $arCurrentPropertyValue;
						if(is_array($arValue[$intPropertyId])){
							$intIndex = 0;
							foreach($arValue[$intPropertyId] as $value){
								$arValuesTmp['n'.$intIndex++] = $value;
							}
						}
						$arValue[$intPropertyId] = $arValuesTmp;
					}
				}
				if($this->isRemoveDuplicates()){
					foreach($arValue as $intPropertyId => $mPropertyValue){
						if(is_array($mPropertyValue)){
							$arExistValues = [];
							foreach($mPropertyValue as $key => $value){
								$strValue = is_array($value) && isset($value['VALUE']) ? $value['VALUE'] : $value;
								if(in_array($strValue, $arExistValues)){
									unset($arValue[$intPropertyId][$key]);
								}
								else{
									$arExistValues[] = $strValue;
								}
							}
						}
					}
				}
			}
			\CIBlockElement::setPropertyValuesEx($intElementId, $intIBlockId, $arValue);
			if(class_exists('\Bitrix\Iblock\PropertyIndex\Manager')){
				\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($intIBlockId, $intElementId);
			}
			return true;
		}
		return false;
	}
	
	/**
	 *	Get property value
	 */
	protected function getPropertyValue($intElementId, $intPropertyId, $intIBlockId=null){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$mResult = null;
			$arFilter = [
				'ID' => $intPropertyId,
				'EMPTY' => 'N',
			];
			$intIBlockId = $intIBlockId ? $intIBlockId : $this->intIBlockId;
			$resPropValues = \CIBlockElement::getProperty($intIBlockId, $intElementId, [], $arFilter);
			while($arPropValue = $resPropValues->fetch()){
				if($arPropValue['PROPERTY_TYPE'] == 'F'){
					$arPropValue['VALUE'] = ['name'=>'', 'type'=>'', 'tmp_name'=>'', 'error'=>4, 'size'=>0, 'description'=>''];
				}
				if($arPropValue['MULTIPLE'] == 'Y'){
					if(!is_array($mResult)){
						$mResult = [];
					}
					if(in_array($arPropValue['PROPERTY_TYPE'], ['S', 'F'])){
						$mResult[$arPropValue['PROPERTY_VALUE_ID']] = [
							'VALUE' => $arPropValue['VALUE'],
							'DESCRIPTION' => $arPropValue['DESCRIPTION'],
						];
					}
					else{
						$mResult[$arPropValue['PROPERTY_VALUE_ID']] = $arPropValue['VALUE'];
					}
				}
				else{
					if(in_array($arPropValue['PROPERTY_TYPE'], ['S', 'F'])){
						$mResult = [
							'VALUE' => $arPropValue['VALUE'],
							'DESCRIPTION' => $arPropValue['DESCRIPTION'],
						];
					}
					else{
						$mResult = $arPropValue['VALUE'];
					}
				}
			}
			return $mResult;
		}
		return false;
	}
	
	/**
	 *	Wrapper for getFields
	 */
	protected function getProperty($intPropertyId, $bJustData=true, $intIBlockId=null){
		$arProperty = $this->getFields(['PROPERTIES' => ['ID' => $intPropertyId]], 'PROPERTY_'.$intPropertyId,
			$intIBlockId);
		if($bJustData){
			$arProperty = $arProperty['DATA'];
		}
		return $arProperty;
	}
	
	/**
	 *	Wrapper for CIBlockElement::setElementSection()
	 */
	protected function setElementSection($intElementId, $arElementSectionsId){
		if(\CIBlockElement::setElementSection($intElementId, $arElementSectionsId, false, $this->intIBlockId)){
			$this->updateElementIndex($intElementId);
			return true;
		}
		return false;
	}
	
	/**
	 *	Set SEO field for element
	 */
	protected function setSeoField($intElementId, $strKey, $strValue){
		$obProps = new \Bitrix\IBlock\InheritedProperty\ElementTemplates($this->intIBlockId, $intElementId);
		$obProps->set([
			$strKey => $strValue,
		]);
		unset($obProps);
	}
	
	/**
	 *	
	 */
	protected function updateElementIndex($intElementId){
		\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($this->intIBlockId, $intElementId);
	}
	
}

?>