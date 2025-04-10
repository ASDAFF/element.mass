<?
/**
 * Class for support
 */

namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\Json;

Helper::loadMessages(__FILE__);

class Support {
	
	const LANG = 'WDA_SUPPORT_';
	
	/**
	 *	Upload file
	 */
	public static function sendRequest($strName, $strEmail, $strSubject, $strMessage){
		if(!strlen($strName)){
			return Helper::getMessage(static::LANG.'ERROR_EMPTY_NAME');
		}
		elseif(!strlen($strEmail)){
			return Helper::getMessage(static::LANG.'ERROR_EMPTY_EMAIL');
		}
		elseif(!strlen($strSubject)){
			return Helper::getMessage(static::LANG.'ERROR_EMPTY_SUBJECT');
		}
		elseif(!strlen($strMessage)){
			return Helper::getMessage(static::LANG.'ERROR_EMPTY_MESSAGE');
		}
		$strUrl = 'https://www.nulled.com/module_support.php';
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client_partner.php');
		$arPost = [
			'name' => $strName,
			'email' => $strEmail,
			'subject' => $strSubject,
			'message' => $strMessage,
			'module_id' => WDA_MODULE,
			'php_version' => PHP_VERSION,
			'module_version' => Helper::getModuleVersion(WDA_MODULE),
			'bitrix_version' => sprintf('%s (%s)', SM_VERSION, SM_VERSION_DATE),
			'bitrix_charset' => SITE_CHARSET,
			'license_hash' => md5(sprintf('BITRIX%sLICENCE', \CUpdateClientPartner::getLicenseKey())),
			'domain' => Helper::getCurrentDomain(),
			'https' => \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? 'Y' : 'N',
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		];
		if(!Helper::isUtf()){
			$arPost = Helper::convertEncoding($arPost, 'CP1251', 'UTF-8');
		}
		$arRequest = [
			'CONTENT' => http_build_query($arPost),
			'SKIP_HTTPS_CHECK' => true,
		];
		$strJson = HttpRequest::post($strUrl, $arRequest);
		return Json::decode($strJson);
	}

}
?>