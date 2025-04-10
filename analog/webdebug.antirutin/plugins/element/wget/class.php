<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class Wget extends PluginElement {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = true;
		if(!strlen($strSiteHomepage = $this->get('site_homepage'))){
			$this->setError(static::getMessage('ERROR_EMPTY_SITE_HOMEPAGE'));
			return false;
		}
		$arFeatures = ['FIELDS' => true];
		$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arFeatures);
		if(strlen($strUrl = $arElement['DETAIL_PAGE_URL'])){
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