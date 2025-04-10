<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class Wget extends PluginSection {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		$bResult = true;
		if(!strlen($strSiteHomepage = $this->get('site_homepage'))){
			$this->setError(static::getMessage('ERROR_EMPTY_SITE_HOMEPAGE'));
			return false;
		}
		$arSelect = ['SECTION_PAGE_URL'];
		$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
		if(strlen($strUrl = $arSection['SECTION_PAGE_URL'])){
			$obHttp = new \Bitrix\Main\Web\HttpClient;
			$obHttp->disableSslVerification();
			$strResponse = $obHttp->get($strSiteHomepage.$strUrl);
			$strStatus = $obHttp->getStatus();
			// $arHeaders = $obHttp->getHeaders()->toArray();
			unset($obHttp);
			if($strStatus !== 200){
				$this->setError(static::getMessage('ERROR_BAD_STATUS', [
					'#STATUS#' => $strStatus,
					'#URL#' => $strSiteHomepage.$strUrl,
				]));
				$bResult = false;
			}
		}
		return $bResult;
	}
	
}

?>