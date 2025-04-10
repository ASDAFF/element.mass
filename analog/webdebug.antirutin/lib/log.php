<?
namespace WD\Antirutin;

use 
	\WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);

class Log {
	
	/**
	 *	Save message to log
	 */
	public static function add($mMessage, $intProfileId=false, $bDebug=false){
		if(!static::isLoggingOn() || $bDebug && !static::isDebugMode()){
			return;
		}
		$strPrefix = '';
		if(is_array($mMessage) && isset($mMessage['PREFIX']) && isset($mMessage['MESSAGE'])){
			$strPrefix = sprintf('[%s] ', $mMessage['PREFIX']);
			$mMessage = $mMessage['MESSAGE'];
		}
		if(is_array($mMessage)) {
			$mMessage = print_r($mMessage, true);
		}
		elseif($mMessage === false){
			$mMessage = '~FALSE~';
		}
		elseif($mMessage === true){
			$mMessage = '~TRUE~';
		}
		elseif($mMessage === null){
			$mMessage = '~NULL~';
		}
		elseif(is_object($mMessage)){
			ob_start();
			var_dump($mMessage);
			$mMessage = ob_get_clean();
		}
		#
		$strMicro = microtime(true);
		$strMicro = $strMicro - floor($strMicro);
		$strMicro = number_format($strMicro, 4, '.', '');
		$strMicro = substr($strMicro, 1);
		#
		$bNoFile = false;
		$strFilename = static::getLogFilename($intProfileId);
		if(!is_file($strFilename)){
			$bNoFile = true;
			Helper::createDirectoriesForFile($strFilename, true);
		}
		#
		$mMessage = '['.date('d.m.Y H:i:s').$strMicro.'] '.$strPrefix.$mMessage."\n";
		#
		$intLogMaxSize = static::getLogMaxSize();
		if($intLogMaxSize > 0){
			$intCurrentSize = static::getLogSize($intProfileId);
			$intNewSize = ($intLogMaxSize - static::LOG_DELTA * 1024) * 1024;
			if($intCurrentSize >= $intNewSize){
				$strFileContent = file_get_contents($strFilename, false, null, -1 * $intNewSize);
				file_put_contents($strFilename, $strFileContent);
				unset($strFileContent);
			}
		}
		#
		$intBytes = file_put_contents($strFilename, $mMessage, FILE_APPEND | LOCK_EX);
		#
		if($bNoFile){
			Helper::changeFileOwner($strFilename);
		}
		#
		unset($strMicro, $strFilename);
		return $intBytes > 0;
	}
	
	/**
	 *	Is logging turned on?
	 */
	protected static function isLoggingOn(){
		$bLogging = Helper::getOption('log_write', '') != 'N';
		return $bLogging;
	}
	
	/**
	 *	Is debug mode?
	 */
	protected static function isDebugMode(){
		$bDebug = Helper::getOption('log_debug_mode', '') == 'Y';
		$bCliDebug = defined('WDA_DEBUG') && WDA_DEBUG === true;
		return $bDebug || $bCliDebug;
	}
	
	/**
	 *	Get filename of log
	 */
	public static function getLogFilename($intProfileId=false, $strRelative=false){
		$strServerId = Helper::getOption('server_uniq_id', '', 'main');
		$strUploadDir = Helper::getOption('upload_dir', '/upload/', 'main');
		$strBasename = 'log'.($intProfileId ? '_'.sprintf('%03d', $intProfileId) : '').'.'.$strServerId.'.log';
		$strResult = Helper::root().'/'.$strUploadDir.'/'.WDA_MODULE.'/log/'.$strBasename;
		if($strRelative){
			$strResult = substr($strResult, strlen(Helper::root()));
		}
		return $strResult;
	}
	
	/**
	 *	Get log filesize
	 */
	public static function getLogSize($intProfileId=false, $bFormat=false){
		$strLogFilename = static::getLogFilename($intProfileId, false);
		$strLogSize = is_file($strLogFilename) ? filesize($strLogFilename) : 0;
		if($bFormat){
			if(!$strLogSize){
				$strLogSize = '0 '.Helper::getMessage('FILE_SIZE_Kb');
			}
			else {
				$strLogSize = \CFile::FormatSize($strLogSize);
			}
		}
		return $strLogSize;
	}
	
	/**
	 *	Get log max filesize
	 */
	public static function getMaxSize($bPreview=true, $bFormat=true){
		if($bFormat){
			if($bPreview){
				return Helper::formatSize(static::PREVIEW_SIZE * 1024);
			}
			else{
				return Helper::formatSize(static::DETAIL_SIZE * 1024);
			}
		}
		else{
			return static::PREVIEW_SIZE * 1024;
		}
	}
	
	/**
	 *	Get log max size
	 */
	protected static function getLogMaxSize(){
		$intResult = 0;
		$intCoreLogMaxSize = Helper::getOption('log_max_size', 0);
		$intModuleLogMaxSize = Helper::getOption('log_max_size', 0);
		if(defined('WDA_EXP_LOG_MAX_SIZE') && is_numeric(WDA_EXP_LOG_MAX_SIZE) && WDA_EXP_LOG_MAX_SIZE > 0){
			$intResult = WDA_EXP_LOG_MAX_SIZE;
		}
		elseif(is_numeric($intModuleLogMaxSize) && $intModuleLogMaxSize > 0){
			$intResult = $intModuleLogMaxSize;
		}
		elseif(is_numeric($intCoreLogMaxSize) && $intCoreLogMaxSize > 0){
			$intResult = $intCoreLogMaxSize;
		}
		$intResult *= 1024 * 1024;
		return round($intResult);
	}
	
	/**
	 *	Delete log file
	 */
	public static function deleteLog($intProfileId=false){
		$strLogFileName = static::getLogFilename($intProfileId);
		if (strlen($strLogFileName) && is_file($strLogFileName) && filesize($strLogFileName)){
			@unlink($strLogFileName);
		}
	}
	
	/**
	 *	Download log file
	 */
	public static function downloadLog($intProfileId=null){
		$strLogFileName = static::getLogFilename($intProfileId);
		$strDownloadFilename = $intProfileId > 0 ? WDA_MODULE.'_'.$intProfileId : WDA_MODULE;
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$strDownloadFilename.'.log');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		if(is_file($strLogFileName)){
			header('Content-Length: '.filesize($strLogFileName));
			readfile($strLogFileName);
		}
	}

}
?>