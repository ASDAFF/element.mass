<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement,
	\Bitrix\Main\Loader;

class CutValue extends PluginElement
{
	
	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S', 'S:HTML']],
		'PROPERTIES' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S', 'S:HTML']],
	];
	protected $arFieldsFilter2 = [
		'FIELDS' => ['!TYPE_FULL' => ['F']],
		'PROPERTIES' => ['!TYPE_FULL' => ['S:video', 'F']],
	];

	public function processElement($intElementId): bool {
		if($this->isEmpty('source_field')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		if($this->isEmpty('cut_field')){
			$this->setError(static::getMessage('ERROR_NO_CUT_FIELD'));
			return false;
		}
		$bResult = false;
		if($intCutPropertyId = $this->isProperty($this->get('cut_field'))){
			$arCutValues = $this->getCutPropValue($intElementId, $intCutPropertyId);
		}
		else{
			$arCutValues = $this->getFieldValue($intElementId, $this->get('cut_field'));
		}

		if($intSourcePropertyId = $this->isProperty($this->get('source_field'))){
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['PROPERTY_ID' => [$intSourcePropertyId]]);
			$arProperty = $arElement['PROPERTIES'][$intSourcePropertyId];
			$strFullType = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
			switch($strFullType){
				case 'S:HTML':
					if($arProperty['MULTIPLE'] == 'Y'){
						foreach($arProperty['VALUE'] as &$arSource){
							foreach ($arCutValues as $strCut){
								if(strlen($arSource['TEXT']) && strlen($strCut)) {
									$arSource['TEXT'] = $this->cutValue($arSource['TEXT'], $strCut, $this->isCaseSensitive(), $this->isLeaveDuplicateSpace());
								}
							}
						}
					}
					else{
						foreach ($arCutValues as $strCut) {
							if(strlen($arProperty['VALUE']['TEXT']) && strlen($strCut)) {
								$arProperty['VALUE']['TEXT'] = $this->cutValue($arProperty['VALUE']['TEXT'], $strCut, $this->isCaseSensitive(), $this->isLeaveDuplicateSpace());
							}
						}
					}
					break;
				case 'S':
					if($arProperty['MULTIPLE'] == 'Y'){
						foreach($arProperty['VALUE'] as &$arSource){
							foreach ($arCutValues as $strCut){
								if(strlen($arSource) && strlen($strCut)) {
									$arSource = $this->cutValue($arSource, $strCut, $this->isCaseSensitive(), $this->isLeaveDuplicateSpace());
								}
							}
						}
					}
					else{
						foreach ($arCutValues as $strCut) {
							if(strlen($arProperty['VALUE']) && strlen($strCut)) {
								$arProperty['VALUE'] = $this->cutValue($arProperty['VALUE'], $strCut, $this->isCaseSensitive(), $this->isLeaveDuplicateSpace());
							}
						}
					}
					break;
			}
			if($arProperty['MULTIPLE'] == 'Y'){
				$bResult = $this->setPropertyValue($intElementId, $intSourcePropertyId, null, [$intSourcePropertyId => $arProperty['VALUE']]);
			}
			else{
				$bResult = $this->setPropertyValue($intElementId, $intSourcePropertyId, $arProperty['VALUE']);
			}
		}
		else{
			$arSourceValues = $this->getFieldValue($intElementId, $this->get('source_field'));
			foreach($arSourceValues as $strSource){
				foreach ($arCutValues as $strCut) {
					if(strlen($strSource) && strlen($strCut)){
						$strSource = $this->cutValue($strSource, $strCut, $this->isCaseSensitive(), $this->isLeaveDuplicateSpace());
					}
				}
				$bResult = $this->update($intElementId, [$this->get('source_field') => $strSource]);
			}
		}
		return $bResult;
	}

	protected function getCutPropValue($intElementId, $intPropertyId) : array {
		$arResult = [];
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['PROPERTY_ID' => [$intPropertyId]]);
		$arProperty = $arElement['PROPERTIES'][$intPropertyId];
		$strFullType = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
		switch($strFullType) {
			case 'S:HTML':
				if($arProperty['MULTIPLE'] == 'Y') {
					foreach ($arProperty['VALUE'] as $arValue) {
						$arResult[] = $arValue['TEXT'];
					}
				}
				else{
					$arResult[0] = $arProperty['VALUE']['TEXT'];
				}
				break;
			case 'S:directory':
				Loader::includeModule('highloadblock');
				$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList([ 'filter' => ['TABLE_NAME' => $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']] ])->fetch();
				$obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
				$strEntityDataClass = $obEntity->getDataClass();
				if($arProperty['MULTIPLE'] == 'Y') {
					$rsItems = $strEntityDataClass::getlist(['order' => ['id' => 'asc'], 'filter' => ['UF_XML_ID' => $arProperty['VALUE']]]);
					while ($arItem = $rsItems->fetch()) {
						$arResult[] = $arItem['UF_NAME'];
					}
				}
				else{
					$arItem = $strEntityDataClass::getlist(['order' => ['id' => 'asc'], 'filter' => ['UF_XML_ID' => $arProperty['VALUE']]])->fetch();
					$arResult[0] = $arItem['UF_NAME'];
				}
				break;
			case 'E':
				if($arProperty['MULTIPLE'] == 'Y') {
					foreach ($arProperty['VALUE'] as $strConnectedElement) {
						$arFilter = [
							'ID' => $strConnectedElement,
						];
						$rsItems = \CIBlockElement::GetList([], $arFilter, false, false, []);
						while ($arItem = $rsItems->fetch()) {
							$arResult[] = $arItem['NAME'];
						}
						unset($arFilter);
					}
				}
				else{
					$arFilter = [
						'ID' => $arProperty['VALUE']
					];
					if($arItem = \CIBlockElement::GetList([], $arFilter, false, false, [])->fetch()){
						$arResult[0] = $arItem['NAME'];
					}
					unset($arFilter);
				}
				break;
			case 'G':
				if($arProperty['MULTIPLE'] == 'Y') {
					$arFilter = [
						'ID' => $arProperty['VALUE']
					];
					$rsItems = \CIBlockSection::GetList([], $arFilter, false, ['NAME'], false);
					while ($arItem = $rsItems->fetch()) {
						$arResult[] = $arItem['NAME'];
					}
					unset($arFilter);
				}
				else{
					$arFilter = [
						'ID' => $arProperty['VALUE']
					];
					if($arItem = \CIBlockSection::GetList([], $arFilter, false, ['NAME'], false)->fetch()){
						$arResult[0] = $arItem['NAME'];
					}
					unset($arFilter);
				}
				break;
			case 'S':
			case 'L':
				if($arProperty['MULTIPLE'] == 'Y') {
					$arResult = is_array($arProperty['VALUE']) ? $arProperty['VALUE'] : [];
				}
				else{
					$arResult[0] = $arProperty['VALUE'];
				}
				break;
		}
		return $arResult;
	}

	protected function getFieldValue($intElementId, $strField) : array{
		$arResult = [];
		$arFeatures = [
			'FIELDS' => [$strField],
		];
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		$arResult[] = $arElement['~'.$strField];
		return $arResult;
	}

	protected function cutValue($strSource, $strCut, bool $bIsCaseSensitive = false, bool $bIsLeaveDuplicateSpace = false) : string{
		if($bIsCaseSensitive){
			$strSource = str_replace($strCut, '', $strSource);
		}
		else{
			$strSource = str_ireplace($strCut, '', $strSource);
		}
		if(!$bIsLeaveDuplicateSpace){
			$strSource = preg_replace("#[ ]{2,}#", ' ', $strSource);
		}
		return trim($strSource);
	}

	protected function isCaseSensitive() : bool {
		return $this->get('case_sensitive') == 'Y';
	}

	protected function isLeaveDuplicateSpace() : bool {
		return $this->get('leave_duplicate_space') == 'Y';
	}
}