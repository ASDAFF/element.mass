<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class CodeGenerate extends PluginSection {
	
	const TYPE_IBLOCK = 'iblock';
	const TYPE_CUSTOM = 'custom';
	
	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => ['S', 'N', 'N:_ID_LIST']],
		'PROPERTIES' => ['TYPE_FULL' => ['S', 'N']],
		'SEO' => true,
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		if($this->isEmpty('field')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		$strValue = $this->getSourceValue($intSectionId);
		$strCode = $this->generateSectionCode($strValue, $intSectionId, $this->getTranslitParams());
		$bResult = $this->update($intSectionId, ['CODE' => $strCode]);
		return $bResult;
	}
	
	/**
	 *	Get source value for transliteration
	 */
	protected function getSourceValue($intSectionId){
		$strField = $this->get('field');
		if($this->isSectionField($strField)){
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, [$strField]);
			return $arSection['~'.$strField];
		}
		elseif($strPropertyCode = $this->isSectionProperty($strField)){
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, [$strField]);
			$arProperty = $arSection['PROPERTIES'][$strPropertyCode];
			$mValue = $arSection['PROPERTIES'][$strPropertyCode];
			return is_array($mValue) ? implode(', ', $mValue) : $mValue;
		}
		elseif($strSeoField = $this->isSeoField($strField)){
			$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, ['SEO' => true]);
			return $arSection['SEO'][$strSeoField];
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
			$arResult = Helper::getIBlockFields($this->intIBlockId, 'SECTION_CODE', true);
		}
		$arResult['_EXISTS_ACTION'] = $this->get('exists_action');
		$arResult['_TEXT_BEGIN'] = $this->get('text_begin');
		$arResult['_TEXT_END'] = $this->get('text_end');
		return $arResult;
	}
	
	/**
	 *	Generate code for single section
	 */
	protected function generateSectionCode($strSource, $intSectionId, $arTranslitParams) {
		switch($arTranslitParams['_EXISTS_ACTION']){
			case 'add_id_section':
				return $this->generateSectionCode_addIdSection($strSource, $intSectionId, $arTranslitParams);
			default:
				return $this->generateSectionCode_addIndex($strSource, $intSectionId, $arTranslitParams);
		}
	}
	
	
	/**
	 *	Add section index to CODE if not unique
	 */
	protected function generateSectionCode_addIndex($strSource, $intSectionId, $arTranslitParams){
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
			$bCodeExists = static::isCodeExists($strResult, $intSectionId);
			if(!$bCodeExists){
				break;
			}
		};
		return $strResult;
	}
	
	/**
	 *	Add section ID to CODE if not unique
	 */
	protected function generateSectionCode_addIdSection($strSource, $intSectionId, $arTranslitParams){
		$bUnique = $arTranslitParams['UNIQUE'] == 'Y';
		$bTranslit = $arTranslitParams['TRANSLITERATION'] == 'Y';
		$strResult = $strSource;
		if($bTranslit){
			$strResult = $this->translit($strSource, $arTranslitParams);
		}
		if($bUnique){
			$bCodeExists = static::isCodeExists($strResult, $intSectionId);
			if($bCodeExists){
				$strResult = $this->translit($strSource.$arTranslitParams['TRANS_SPACE'].$intSectionId, $arTranslitParams);
			}
		}
		return $strResult;
	}
	
	/**
	 *	Is code exists?
	 */
	protected function isCodeExists($strCode, $intSectionId){
		$arFilter = [
			'IBLOCK_ID' => $this->intIBlockId,
			'CODE' => $strCode,
			'!ID' => $intSectionId,
		];
		return \CIBlockSection::getList([], $arFilter, false, ['ID'])->fetch() !== false;
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