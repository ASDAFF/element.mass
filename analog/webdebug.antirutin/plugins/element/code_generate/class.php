<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class CodeGenerate extends PluginElement {
	
	const TYPE_IBLOCK = 'iblock';
	const TYPE_CUSTOM = 'custom';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'N', 'N:_ID_LIST'], '!CODE' => ['CREATED_BY__NAME', 'MODIFIED_BY__NAME', '__IBLOCK_SECTION_CHAIN']],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'S:FileMan', 'S:ElementXmlID', 'S:directory', 'N', 'L']],
		'SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		$strValue = $this->getSourceValue($intElementId);
		$strCode = $this->generateElementCode($strValue, $intElementId, $this->getTranslitParams());
		$bResult = $this->update($intElementId, ['CODE' => $strCode]);
		return $bResult;
	}
	
	/**
	 *	Get source value for transliteration
	 */
	protected function getSourceValue($intElementId){
		$strField = $this->get('field');
		if($this->isField($strField)){
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['FIELDS' => [$strField]]);
			return $arElement['~'.$strField];
		}
		elseif($intPropertyId = $this->isProperty($strField)){
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['PROPERTY_ID' => [$intPropertyId]], true);
			$arProperty = $arElement['PROPERTIES'][$intPropertyId];
			$strType = $arProperty['PROPERTY_TYPE'];
			if(strlen($arProperty['USER_TYPE'])){
				$strType .= ':'.$arProperty['USER_TYPE'];
			}
			switch($strType){
				case 'S:directory':
					$mValue = IBlock::getSDirectoryValuesByXmlId($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'], 
						$arProperty['~VALUE']);
					break;
				default:
					$mValue = $arElement['PROPERTIES'][$intPropertyId]['~VALUE'];
					break;
			}
			return is_array($mValue) ? implode(', ', $mValue) : $mValue;
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, ['SEO' => true]);
			return $arElement['SEO'][$strSeoField];
		}
		return false;
	}
	
	/**
	 *	Get translit params
	 */
	protected function getTranslitParams(){
		if($this->get('settings_type') == static::TYPE_CUSTOM){
			$arResult = [
				'UNIQUE' => $this->get('unique') == 'N' ? 'N' : 'Y',
				'TRANSLITERATION' => $this->get('transliteration') == 'N' ? 'N' : 'Y',
				'TRANS_LEN' => $this->get('trans_len') > 0 ? $this->get('trans_len') : '255',
				'TRANS_CASE' => $this->get('trans_case'),
				'TRANS_SPACE' => $this->get('trans_space'),
				'TRANS_OTHER' => $this->get('trans_other'),
				'TRANS_EAT' => $this->get('trans_eat') == 'N' ? 'N' : 'Y',
				'USE_GOOGLE' => $this->get('use_google') == 'Y' ? 'Y' : 'N',
			];
		}
		else{
			$arResult = Helper::getIBlockFields($this->intIBlockId, 'CODE', true);
		}
		$arResult['_EXISTS_ACTION'] = $this->get('exists_action');
		$arResult['_TEXT_BEGIN'] = $this->get('text_begin');
		$arResult['_TEXT_END'] = $this->get('text_end');
		return $arResult;
	}
	
	/**
	 *	Generate code for single element
	 */
	protected function generateElementCode($strSource, $intElementId, $arTranslitParams) {
		switch($arTranslitParams['_EXISTS_ACTION']){
			case 'add_id_element':
				return $this->generateElementCode_addIdElement($strSource, $intElementId, $arTranslitParams);
			default:
				return $this->generateElementCode_addIndex($strSource, $intElementId, $arTranslitParams);
		}
	}
	
	
	/**
	 *	Add element index to CODE if not unique
	 */
	protected function generateElementCode_addIndex($strSource, $intElementId, $arTranslitParams){
		$bUnique = $arTranslitParams['UNIQUE'] == 'Y';
		$bTranslit = $arTranslitParams['TRANSLITERATION'] == 'Y';
		$strResult = '';
		$intSuffix = 0;
		while(true) {
			if($bUnique){
				$intSuffix++;
			}
			if($bTranslit){
				$strResult = $this->translit($strSource, $arTranslitParams, 
					$intSuffix > 1 ? $arTranslitParams['TRANS_SPACE'].$intSuffix : null);
			}
			else{
				$strResult = $strSource;
			}
			if(!$bUnique){
				break;
			}
			$bCodeExists = static::isCodeExists($strResult, $intElementId);
			if(!$bCodeExists){
				break;
			}
		};
		return $strResult;
	}
	
	/**
	 *	Add element ID to CODE if not unique
	 */
	protected function generateElementCode_addIdElement($strSource, $intElementId, $arTranslitParams){
		$bUnique = $arTranslitParams['UNIQUE'] == 'Y';
		$bTranslit = $arTranslitParams['TRANSLITERATION'] == 'Y';
		$strResult = $strSource;
		if($bTranslit){
			$strResult = $this->translit($strSource, $arTranslitParams);
		}
		if($bUnique){
			$bCodeExists = static::isCodeExists($strResult, $intElementId);
			if($bCodeExists){
				$strResult = $this->translit($strSource.$arTranslitParams['TRANS_SPACE'].$intElementId, $arTranslitParams);
			}
		}
		return $strResult;
	}
	
	/**
	 *	Is code exists?
	 */
	protected function isCodeExists($strCode, $intElementId){
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'CODE' => $strCode,
			'!ID' => $intElementId,
		];
		return \CIBlockElement::getList([], $arFilter, []) > 0;
	}
	
	/**
	 *	Translit value
	 */
	protected function translit($strValue, $arTranslitParams, $strSuffix=null){
		$strSuffix = !is_null($strSuffix) ? trim($strSuffix) : '';
		if($arTranslitParams['USE_GOOGLE'] == 'Y'){
			$strValueOriginal = $strValue;
			$strValue = Helper::translate($strValue);
			if(!mb_strlen($strValue) && mb_strlen($strValueOriginal)){
				$strValue = $strValueOriginal;
			}
		}
		$intLength = intVal($arTranslitParams['TRANS_LEN']) - strlen($strSuffix);
		if(strlen($arTranslitParams['_TEXT_BEGIN'])){
			$intLength -= strlen($arTranslitParams['_TEXT_BEGIN']);
		}
		if(strlen($arTranslitParams['_TEXT_END'])){
			$intLength -= strlen($arTranslitParams['_TEXT_END']);
		}
		$strText = \CUtil::translit($strValue, LANGUAGE_ID, [
			'max_len' => $intLength + 1,
			'change_case' => $arTranslitParams['TRANS_CASE'],
			'replace_space' => $arTranslitParams['TRANS_SPACE'],
			'replace_other' => $arTranslitParams['TRANS_OTHER'],
			'delete_repeat_replace' => $arTranslitParams['TRANS_EAT'] == 'Y' ? true : false,
			'use_google' => 'false',
		]).$strSuffix;
		$strNext = substr($strText, $intLength, 1);
		$strText = substr($strText, 0, $intLength);
		if($this->get('prevent_cut') == 'Y'){
			$this->preventCut($strText, $strNext, $arTranslitParams['TRANS_SPACE']);
		}
		if(strlen($arTranslitParams['_TEXT_BEGIN'])){
			$strText = $arTranslitParams['_TEXT_BEGIN'].$strText;
		}
		if(strlen($arTranslitParams['_TEXT_END'])){
			$strText = $strText.$arTranslitParams['_TEXT_END'];
		}
		return $strText;
	}
	
	/**
	 *	Prevent cut in the midst of words
	 */
	protected function preventCut(&$strText, $strNext, $strTransSpace){
		if(strlen($strNext) && $strNext != $strTransSpace){
			if(is_numeric($intPos = strrpos($strText, $strTransSpace))){
				$strText = substr($strText, 0, $intPos);
			}
		}
	}
	
}

?>