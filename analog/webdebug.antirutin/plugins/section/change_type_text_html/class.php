<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection,
	\WD\Antirutin\Html2Text,
	\WD\Antirutin\Html2Text_cp1251,
	\WD\Antirutin\Html2Text_utf8,
	\Bitrix\Main\Loader;

class ChangeTypeTextHtml extends PluginSection
{

	protected $arFieldsFilter = [
		'FIELDS' => ['IS_WRITEABLE' => 'Y', 'TYPE_FULL' => ['S:HTML']],
	];

	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *    Execute!
	 */
	public function processSection($intSectionId) : bool{
		if($this->isEmpty('source_field')){
			$this->setError(static::getMessage('ERROR_NO_SOURCE_FIELD'));
			return false;
		}
		$bResult = true;
		$arSection = $this->getSectionDescription($intSectionId);
		if($arSection && $arSection['DESCRIPTION'] != toLower($this->get('type_field'))){
			$this->convertHtmlToText($arSection['DESCRIPTION'], $arSection['DESCRIPTION_TYPE']);
			$bResult = $this->update($intSectionId, [
				'DESCRIPTION' => $arSection['DESCRIPTION'],
				'DESCRIPTION_TYPE' => toLower($this->get('type_field')),
			]);
		}
		return $bResult;
	}

	private function getSectionDescription($intSectionId) {
		return \CIBlockSection::getList([], ['ID' => $intSectionId], false, ['DESCRIPTION', 'DESCRIPTION_TYPE'], false)->fetch();
	}

	private function convertHtmlToText(&$strText, $strOldType){
		$strOldType = toUpper($strOldType);
		$strNewType = toUpper($this->get('type_field'));
		if($this->get('html_to_text') == 'Y' && $strOldType == 'HTML' && $strNewType == 'TEXT'){
			new Html2Text(); # For autoload
			$arOptions = [
				'do_links' => 'none',
				'width' => '0',
			];
			if(Helper::isUtf()){
				$obHtml = new Html2Text_utf8($strText, $arOptions);
			}
			else{
				$obHtml = new Html2Text_cp1251($strText, $arOptions);
			}
			$strText = $obHtml->getText();
			$this->debug([$this->get('html_to_text'), $strOldType, $strNewType, $strText]);
			unset($obHtml);
		}
	}

}