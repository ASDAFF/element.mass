<?
/**
 *	Class to work with HTTP-requests
 */

namespace WD\Antirutin;

class HttpRequest {
	
	protected static $arHttpResponseHeaders;
	protected static $strFinalUrl;
	protected static $strCharset;

	/**
	 *	Do http request
	 */
	public static function getHttpContent($strURL, $arParams=array()){
		if(substr($strURL, 0, 2)=='//') {
			$strURL = 'http:'.$strURL;
		}
		if (!in_array($arParams['METHOD'], array('GET', 'POST', 'HEAD', 'PUT', 'DELETE'))) {
			$arParams['METHOD'] = 'GET';
		}
		if (!is_numeric($arParams['TIMEOUT']) || $arParams['TIMEOUT']<=0) {
			$arParams['TIMEOUT'] = 30;
		}
		$arParams['HEADER'] = trim($arParams['HEADER']);
		if(strlen($arParams['HEADER'])){
			$arParams['HEADER'] .= "\r\n";
		}
		if (isset($arParams['BASIC_AUTH'])) {
			$arParams['HEADER'] .= 'Authorization: Basic '.$arParams['BASIC_AUTH']."\r\n";
		}
		if (isset($arParams['OAUTH'])) {
			$arParams['HEADER'] .= 'Authorization: OAuth '.$arParams['OAUTH']."\r\n";
		}
		if (isset($arParams['TOKEN_AUTH'])) {
			$arParams['HEADER'] .= 'Authorization: Token '.$arParams['TOKEN_AUTH']."\r\n";
		}
		if ($arParams['IGNORE_ERRORS']!==false) {
			$arParams['IGNORE_ERRORS'] = true;
		}
		if(!empty($arParams['USER_AGENT'])) {
			$arParams['HEADER'] .= 'User-Agent: '.$arParams['USER_AGENT']."\r\n";
		} else {
			$arParams['HEADER'] .= 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, '
				.'like Gecko) Chrome/59.0.3071.86 Safari/537.36'."\r\n";
		}
		
		static::$strFinalUrl = $strURL;
		$IsCurl = function_exists('curl_version');
		if($IsCurl) {
			if($arParams['ADD_SYSTEM_HEADERS'] !== false){
				$arParams['HEADER'] = 'Accept: */*'."\r\n".'Connection: close'."\r\n".$arParams['HEADER'];
			}
			$Curl = curl_init();
			curl_setopt($Curl, CURLOPT_URL, $strURL);
			curl_setopt($Curl, CURLOPT_HEADER, true);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($Curl, CURLOPT_TIMEOUT, $arParams['TIMEOUT']);
			if ($arParams['METHOD']=='POST') {
				curl_setopt($Curl, CURLOPT_POST, true);
			} else {
				curl_setopt($Curl, CURLOPT_CUSTOMREQUEST, $arParams['METHOD']);
				if ($Params['METHOD']=='HEAD') {
					curl_setopt($Curl, CURLOPT_NOBODY, true);
				}
			}
			if($arParams['CRLF'] === true) {
				curl_setopt($Curl, CURLOPT_CRLF, true);
			}
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, ($arParams['FOLLOW_REDIRECT']===false ? false : true));
			if (!empty($arParams['HEADER'])) {
				curl_setopt($Curl, CURLOPT_HTTPHEADER, explode("\r\n",$arParams['HEADER']));
			}
			if (trim($arParams['CONTENT'])!='') {
				curl_setopt($Curl, CURLOPT_POSTFIELDS, $arParams['CONTENT']);
			}
			if ($arParams['SKIP_HTTPS_CHECK']===true) {
				curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, false);
			}
			$strResult = curl_exec($Curl);
			$HeaderSize = curl_getinfo($Curl, CURLINFO_HEADER_SIZE);
			static::$arHttpResponseHeaders = explode("\r\n", (substr($strResult, 0, $HeaderSize)));
			foreach(static::$arHttpResponseHeaders as $Key => $strHeader) {
				if (trim($strHeader)=='') {
					unset(static::$arHttpResponseHeaders[$Key]);
				}
			}
			$strResult = substr($strResult, $HeaderSize);
			curl_close($Curl);
			if(empty(static::$arHttpResponseHeaders) && empty($strResult)){
				$strResult = false;
			}
		} else {
			$arContext = array(
				'http' => array(
					'method' => $arParams['METHOD'],
					'timeout' => $arParams['TIMEOUT'],
					'ignore_errors' => $arParams['IGNORE_ERRORS'],
				)
			);
			$arContext['http']['follow_location'] = $arParams['FOLLOW_REDIRECT']===false ? false : true;
			if (!empty($arParams['HEADER'])) {
				$arContext['http']['header'] = $arParams['HEADER'];
			}
			if (trim($arParams['CONTENT'])!='') {
				$arContext['http']['content'] = $arParams['CONTENT'];
			}
			$resContext = stream_context_create($arContext);
			$strResult = @file_get_contents($strURL, false, $resContext);
			static::$arHttpResponseHeaders = $http_response_header;
		}
		static::$strCharset = static::detectCharset();
		if($arParams['AUTO_CONVERT_ENCODING'] === true){
			$bUtf = defined('BX_UTF') && BX_UTF === true;
			if(static::$strCharset == 'UTF-8' && !$bUtf){
				$strResult = $GLOBALS['APPLICATION']->convertCharset($strResult, 'UTF-8', 'windows-1251');
			}
			elseif(static::$strCharset == 'windows-1251' && $bUtf){
				$strResult = $GLOBALS['APPLICATION']->convertCharset($strResult, 'windows-1251', 'UTF-8');
			}
		}
		return $strResult;
	}
	
	/**
	 *	Do GET-request
	 */
	public static function get($strURL, $arParams=array()){
		$arParams['METHOD'] = 'GET';
		return static::getHttpContent($strURL, $arParams);
	}
	
	/**
	 *	Do POST-request
	 */
	public static function post($strURL, $arParams=array()){
		$arParams['METHOD'] = 'POST';
		return static::getHttpContent($strURL, $arParams);
	}
	
	/**
	 *	Send one file to remote server
	 */
	public static function sendFile($arFile, $strUrl){
		$strBoundary = '---------------------' . substr(md5(rand(0, 32000)), 0, 10);
		$strPostData = '';
		$strPostData .= '--'.$strBoundary."\r\n";
		$strPostData .= 'Content-Disposition: form-data; name="file"; filename="'.$arFile['name'].'"'."\r\n";
		$strPostData .= 'Content-Type: '.$arFile['type']."\r\n";
		$strPostData .= "\r\n".file_get_contents($arFile['tmp_name'])."\r\n";
		$strPostData .= '--'.$strBoundary."\r\n";
		$arParams = array(
			'METHOD' => 'POST',
			'HEADER' => 'Content-Type: multipart/form-data; boundary='.$strBoundary,
			'CONTENT' => $strPostData,
		);
		return static::getHttpContent($strUrl, $arParams);
	}
	
	/**
	 *	Get response headers
	 */
	public static function getHeaders(){
		return static::$arHttpResponseHeaders;
	}
	
	/**
	 *	Get single header
	 */
	public static function getHeader($strHeader, $bMultipleIsArray=false){
		$mResult = null;
		$strHeader = toLower($strHeader);
		if(is_array(static::$arHttpResponseHeaders)){
			foreach(static::$arHttpResponseHeaders as $strResponseHeader){
				if(preg_match('#^([A-z0-9-_]+):[\s]?(.*?)$#', $strResponseHeader, $arMatch)){
					$strHeaderName = $arMatch[1];
					$strHeaderValue = $arMatch[2];
					if(toLower($strHeaderName) == $strHeader){
						if(is_null($mResult)){
							$mResult = $strHeaderValue;
						}
						else{
							if($bMultipleIsArray){
								if(!is_array($mResult)){
									$mResult = [$mResult];
								}
								$mResult[] = $strHeaderValue;
							}
							else{
								$mResult = $strHeaderValue;
							}
						}
					}
				}
			}
		}
		return $mResult;
	}
	
	/**
	 *	Get response code
	 */
	public static function getCode(){
		$intResult = 0;
		if(is_array(static::$arHttpResponseHeaders)){
			foreach(static::$arHttpResponseHeaders as $strHeader){
				if(preg_match('#^HTTP/([\d.]+) (\d+)#i', $strHeader, $arMatch)){
					$intResult = IntVal($arMatch[2]);
				}
			}
		}
		return $intResult;
	}
	
	/**
	 *	Get response charset
	 */
	public static function getResponseCharset(){
		return static::$strCharset;
	}
	
	/**
	 *	Is response charset UTF-8?
	 */
	public static function isResponseUtf(){
		return toUpper(static::$strCharset) == 'UTF-8';
	}
	
	/**
	 *	Auto convert downloaded html charset
	 */
	public static function convertPageCharset($strText, $strCharset) {
		global $APPLICATION;
		if(defined('BX_UTF') && BX_UTF===true) {
			if(ToUpper($Charset)!='UTF-8') {
				$APPLICATION->ConvertCharset($Text, 'CP1251', 'UTF-8');
			}
		} else {
			if(ToUpper($Charset)=='UTF-8') {
				$APPLICATION->ConvertCharset($Text, 'UTF-8', 'CP1251');
			}
		}
		return $Text;
	}
	
	/**
	 *	Detect charset based on response headers
	 */
	public static function detectCharset(){
		$strResult = false;
		$arAllowedCharset = [
			'UTF-8' => ['utf8', 'utf'],
			'windows-1251' => ['cp1251', 'cp-1251'],
		];
		if(is_array(static::$arHttpResponseHeaders)){
			foreach(static::$arHttpResponseHeaders as $strHeader){
				if(preg_match('#^Content-Type:[\s]?[a-z0-9-_/]+;[\s]?charset=([A-z0-9-_]+)#i', $strHeader, $arMatch)){
					$strResult = $arMatch[1];
				}
			}
		}
		if(strlen($strResult)) {
			$strResultCharset = false;
			foreach($arAllowedCharset as $key => $arValue){
				$arValue[] = $key;
				foreach($arValue as $strItem){
					if(toLower($strItem) == toLower($strResult)){
						$strResultCharset = $key;
						break;
					}
				}
			}
			$strResult = $strResultCharset;
		}
		if(!$strResult){
			reset($arAllowedCharset);
			$strResult = key($arAllowedCharset);
		}
		return $strResult;
	}
	
	/**
	 *	Get final url from headers
	 */
	public static function getFinalUrl() {
		$arHeaders = static::getHeaders();
		$strResult = static::$strFinalUrl;
		foreach($arHeaders as $strHeader){
			if(preg_match('#^Location:[\s]?(.*?)$#i', $strHeader, $arMatch)) {
				$strResult = $arMatch[1];
			}
		}
		return $strResult;
	}
	
}

?>