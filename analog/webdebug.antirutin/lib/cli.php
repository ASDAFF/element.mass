<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\Log;

Helper::loadMessages(__FILE__);

class Cli {
	
	const CRON_JOB = '#^(([a-z0-9*/,\-]+)\s+([a-z0-9*/,\-]+)\s+([a-z0-9*/,\-]+)\s+([a-z0-9*/,\-]+)\s+([a-z0-9*/,\-]+))\s+(.*?)$#';
	const MULTITHREAD_TEST_SUCCESS = 'SUCCESS';
	
	const PROFILE_ID = 'profile';
	const EXTERNAL_ID = 'external';
	
	protected static $bIsRoot;
	protected static $intBitrixUser;
	protected static $arError;
	
	/**
	 *	Check if script are executing by cli (command-line-interface)
	 */
	public static function isCli(){
		return php_sapi_name() == 'cli';
	}
	
	/**
	 *	Check ifuser is root
	 */
	public static function isRoot(){
		if(is_null(static::$bIsRoot) && static::isExec() && static::isLinux()){
			@exec('whoami', $arExec);
			static::$bIsRoot = count($arExec) == 1 && reset($arExec) == 'root';
		}
		return static::$bIsRoot;
	}
	
	/**
	 *	Check ifuser is root
	 */
	public static function getBitrixUser(){
		if(!is_numeric(static::$intBitrixUser) && function_exists('fileowner')){
			$strDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
			if(!is_dir($strDir)){
				$strDir = $_SERVER['DOCUMENT_ROOT'];
			}
			static::$intBitrixUser = fileowner($strDir);
		}
		return static::$intBitrixUser;
	}
	
	/**
	 *	Get process ID
	 */
	public static function getPid(){
		return getMyPid();
	}
	
	/**
	 *	Is OS linux?
	 */
	public static function isLinux(){
		return stripos(PHP_OS, 'linux') !== false;
	}
	
	/**
	 *	Is OS windows?
	 */
	public static function isWindows(){
		return stripos(PHP_OS, 'win') !== false;
	}
	
	/**
	 *	Check ifhosting is timeweb
	 */
	public static function isHostingTimeweb(){
		if(static::isExec() && static::isLinux()) {
			@exec('uname -a', $arExec);
			if(stripos($arExec[0], 'timeweb.ru') !== false) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Is `exec` function available?
	 */
	public static function isExec(){
		return function_exists('exec');
	}
	
	/**
	 *	Is `proc_open` function available?
	 */
	public static function isProcOpen(){
		return function_exists('proc_open');
	}
	
	/**
	 *	Parse parameters passed to php-file
	 */
	public static function getCliArguments($strArgumentKey=null){
		global $argv;
		$arResult = [];
		if(is_array($argv)){
			foreach(array_slice($argv, 1) as $strArgument){
				parse_str($strArgument, $arArgument);
				if(is_array($arArgument)) {
					$arResult = array_merge($arResult, $arArgument);
				}
			}
		}
		if(is_string($strArgumentKey)){
			return $arResult[$strArgumentKey];
		}
		return $arResult;
	}
	
	/**
	 *	Check if crontab can be managed by this script (only if Linux OS)
	 */
	public static function canAutoSet(){
		if(static::isLinux() && static::isExec()) {
			$strPhpFile = Helper::root().'/bitrix/modules/'.WDA_MODULE.'/cli/check_autoset.php';
			$strCommand = 'php '.$strPhpFile.' > /dev/null 2>&1';
			$strSchedule = '00 00 01 01 01';
			if(static::addCronTask($strCommand, $strSchedule)) {
				static::deleteCronTask($strCommand, $strSchedule);
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Compile command from elements
	 */
	public static function buildCommand($strPhpPath, $strCommand, $strScriptName=null, $bSetMbstring=true, $strConfig='', $strOutput='', $strExternalId=null){
		$arCommand = [];
		$arCommand[] = $strPhpPath;
		if($bSetMbstring) {
			$arCommand[] = static::getMbstringParams();
		}
		if(strlen($strConfig)){
			$arCommand[] = $strConfig;
		}
		if(is_numeric($strCommand)){ // ifit is profile ID
			$strCommand = static::getProfilePhpCommand($strCommand, $strScriptName, $strExternalId);
		}
		$arCommand[] = '-f '.$strCommand;
		if(strlen($strOutput)){
			$arCommand[] = '>> '.$strOutput.' 2>&1';
		}
		return implode(' ', $arCommand);
	}
	
	/**
	 *	Get mbstring params for command
	 */
	public static function getMbstringParams(){
		if(Helper::isUtf()) {
			return '-d mbstring.func_overload=2 -d mbstring.internal_encoding=UTF-8';
		}
		else {
			return '-d mbstring.func_overload=0 -d mbstring.internal_encoding=CP1251';
		}
		return 'php';
	}
	
	/**
	 *	Get full path to cron php-file
	 */
	public static function getPhpFile($strFile){
		return Helper::root().'/bitrix/modules/'.WDA_MODULE.'/cli/'.$strFile;
	}
	
	/**
	 *	Get profile command for cron (without php and configs)
	 */
	public static function getProfilePhpCommand($intProfileID, $strScriptName=null, $strExternalId=null, $arArguments=null){
		$strScriptName = !is_null($strScriptName) ? $strScriptName : 'execute.php';
		$arArgumentsTmp = [
			static::PROFILE_ID => $intProfileID,
		];
		if(!is_null($strExternalId)){
			$arArgumentsTmp[static::EXTERNAL_ID] = $strExternalId;
		}
		if(is_array($arArguments)){
			foreach($arArguments as $key => $value){
				$arArgumentsTmp[$key] = $value;
			}
		}
		$arArguments = $arArgumentsTmp;
		unset($arArgumentsTmp);
		$strCommand = static::getPhpFile($strScriptName);
		foreach($arArguments as $key => $value){
			$strCommand .= ' '.$key.'='.(is_array($value)?implode(',', $value):$value);
		}
		return $strCommand;
	}
	
	/**
	 *	Check wheather profile is configured in crontab
	 */
	public static function isProfileOnCron($intProfileID, $strScriptName=null, $strExternalId=null){
		$strCommand = static::getProfilePhpCommand($intProfileID, $strScriptName, $strExternalId);
		return static::isCronTaskConfigured($strCommand);
	}
	
	/**
	 *	Delete task for profile
	 */
	public static function deleteProfileCron($intProfileID, $strScriptName=null, $strExternalId=null){
		$strCommand = static::getProfilePhpCommand($intProfileID, $strScriptName, $strExternalId);
		return static::deleteCronTask($strCommand);
	}
	
	/**
	 *	Get path to default php binary
	 */
	public static function getDefaultPhpPath(){
		if(static::isLinux() && static::isExec()) {
			@exec('which php', $arExecResult);
			if(is_array($arExecResult) && strlen($arExecResult[0])){
				return $arExecResult[0];
			}
		}
		return 'php';
	}
	
	/**
	 *	Try to get path to php binary (cli)
	 */
	public static function getPhpPath(){
		if(static::isLinux() && static::isExec()) {
			$arPhpVariants = [];
			# Detect from PHP_BINARY
			if(defined('PHP_BINARY') && strlen(PHP_BINARY) && is_file(PHP_BINARY)){
				$strBinary = PHP_BINARY;
				$strBinary = str_replace('/bin/php-cgi', '/bin/php', $strBinary);
				$strBinary = str_replace('-cgi', '', $strBinary);
				$arPhpVariants[] = $strBinary;
			}
			# Detect from whereis
			$arPotentialPhpPaths = static::getPotentialPhpPaths();
			foreach($arPotentialPhpPaths as $strPath){
				if(is_file($strPath)){
					$arPhpVariants[] = $strPath;
				}
			}
			# Check detected variants
			$strUsedPhpVersion = static::getSitePhpVersion();
			foreach($arPhpVariants as $strPath){
				@exec($strPath.' -v', $arOutput);
				foreach($arOutput as $strOutput){
					if(preg_match('#PHP\s?(\d+\.\d+.\d+)#i', $strOutput, $arMatch)) {
						$strCheckVersion = $arMatch[1];
						if($strCheckVersion == $strUsedVersion) {
							return $strPath;
						}
					}
				}
				unset($arOutput);
			}
		}
		# Return default value
		return static::getDefaultPhpPath();
	}
	
	/**
	 *	Get potential php-paths
	 */
	public static function getPotentialPhpPaths(){
		$arResult = [];
		if(static::isLinux() && static::isExec()) {
			@exec('whereis php', $arExecResult);
			if(preg_match('#^php:\s?(.*?)$#i', $arExecResult[0], $arMatch)){
				$arExecResult = array_slice(explode(' ', $arExecResult[0]), 1);
				$arExclude = [
					'#\.gz$#i',
					'#\.ini$#i',
					'#^/etc/#i',
					'#^/usr/lib/#i',
					'#^/usr/lib64/#i',
					'#^/usr/share/#i',
				];
				foreach($arExecResult as $strPath){
					$bExcluded = false;
					foreach($arExclude as $strPattern){
						if(preg_match($strPattern, $strPath)){
							$bExcluded = true;
							break;
						}
					}
					if(!$bExcluded){
						$arResult[] = $strPath;
					}
				}
			}
		}
		return $arResult;
	}

	/**
 	 *	Get used PHP version
	 */
	public static function getSitePhpVersion(){
		$strResult = PHP_VERSION;
		if(preg_match('#(\d+\.\d+.\d+)#', $strResult, $arMatch)){
			$strResult = $arMatch[0];
		}
		return $strResult;
	}
	
	/**
	 *	Check threads ase supported
	 */
	public static function isMultithreadingSupported(){
		if(!static::isProcOpen()){
			return false;
		}
		$arCommand = static::getFullCommand('check_thread.php');
		$arArguments = array(
			'profile' => '0',
			'iblock' => '0',
			'id' => '0',
		);
		$obThread = new Thread($arCommand['COMMAND'], $arArguments);
		$fTime = microtime(true);
		$intMaxTime = 3;
		while($obThread->isRunning()){
			if(microtime(true) - $fTime >= $intMaxTime){
				break;
			}
			usleep(100000);
		}
		if(!$obThread->isRunning()){
			$arResult = $obThread->result();
			if(static::isWindows()){
				$arResult['stderr'] = Helper::convertEncodingFrom($arResult['stderr'], 'cp866');
				$arResult['stdout'] = Helper::convertEncodingFrom($arResult['stdout'], 'cp866');
			}
			if($arResult['stdout'] == static::MULTITHREAD_TEST_SUCCESS){
				return true;
			}
			else{
				$arResult = [
					'ERROR' => true,
					'COMMAND' => $arCommand['COMMAND'],
					'STDOUT' => $arResult['stdout'],
					'STDERR' => $arResult['stderr'],
				];
				static::$arError = array_slice($arResult, 1);
				return $arResult;
			}
		}
		return false;
	}
	
	/**
	 *	Get CPU cores count
	 */
	public static function getCpuCoresCount(){
		if(static::isExec()) {
			if(static::isWindows()){
				$strCoreCount = @exec('echo %NUMBER_OF_PROCESSORS%');
				if(is_numeric($strCoreCount) && $strCoreCount > 0){
					return $strCoreCount;
				}
			}
			else {
				$strCoreCount = @exec('grep -c processor /proc/cpuinfo');
				if(is_numeric($strCoreCount) && $strCoreCount > 0){
					return $strCoreCount;
				}
			}
		}
		return false;
	}

	/*** CRON ***/
	
	/**
	 *	Add cron job
	 */
	public static function addCronTask($mCommand, $strSchedule=''){
		if(static::isLinux()){
			if(is_array($mCommand)) {
				$mCommand = static::buildCommand($mCommand[0], $mCommand[1], $mCommand[2], $mCommand[3], $mCommand[4], $mCommand[5], $mCommand[6]);
			}
			if(!strlen($mCommand) || !static::isExec()) {
				return false;
			}
			$strSchedule = strlen($strSchedule) ? trim($strSchedule).' ' : '* * * * * ';
			if(!static::isCronTaskConfigured($mCommand, $strSchedule)) {
				$strCommandEscaped = str_replace('"', '\"', $mCommand);
				@exec('(crontab -l 2>/dev/null; echo "'.$strSchedule.$strCommandEscaped.'") | crontab -', $arExecResult);
			}
			return static::isCronTaskConfigured($mCommand, $strSchedule);
		}
		return false;
	}

	/**
	 *	Delete cron job
	 */
	public static function deleteCronTask($strCommand, $strSchedule=null){
		if(!strlen($strCommand) || !static::isExec() || !static::isLinux()) {
			return false;
		}
		$strSchedule = is_string($strSchedule) && strlen($strSchedule) ? trim($strSchedule).' ' : '';
		$strCommandEscaped = str_replace('"', '\"', $strCommand);
		$strExecCommand = 'crontab -l | grep -v -F "'.$strSchedule.$strCommandEscaped.'" | crontab -';
		@exec($strExecCommand, $arExecResult);
		return !static::isCronTaskConfigured($strCommand, $strSchedule);
	}
	
	/**
	 *	Get cron jobs
	 */
	public static function getCronTasks(){
		$arResult = [];
		if(static::isExec() && static::isLinux()) {
			$strCommand = 'crontab -l;';
			@exec($strCommand, $arCommandResult);
			$strPath = '/bitrix/modules/'.WDA_MODULE.'/';
			foreach($arCommandResult as $Key => $strCommandResult){
				$arCommand = static::parseCronTask($strCommandResult);
				if(is_array($arCommand) && (!is_string($strPath) || stripos($arCommand['COMMAND'], $strPath) !== false)) {
					$arResult[] = $arCommand;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get profile cron tasks
	 */
	public static function getProfileCronTasks($intProfileId){
		$arResult = [];
		foreach(static::getCronTasks() as $arCronTask){
			if($arCronTask['PROFILE_ID'] == $intProfileId){
				$arResult[] = $arCronTask;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Set profile cron actions
	 *	$arTasks = [
			paap6zysibyyzpnydxee3ijhenhz4f9r => Array (
				[0] => 25
				[1] => 10
				[2] => *
				[3] => *
				[4] => *
			)
	 ]
	 */
	public static function setProfileCronTasks($intProfileId, $arTasks){
		if(!$intProfileId || !static::canAutoSet() || !is_array($arTasks)){
			return false;
		}
		$bResult = true;
		$strCommandLite = static::getProfilePhpCommand($intProfileId);
		static::deleteCronTask($strCommandLite);
		foreach($arTasks as $strExternalId => $arTask){
			if(is_numeric($strExternalId)) {
				$strExternalId = null;
			}
			$arCommand = static::getFullCommand(null, $intProfileId, Log::getLogFilename($intProfileId), $strExternalId);
			$strSchedule = sprintf('%s %s %s %s %s', $arTask['minute'], $arTask['hour'], $arTask['day'], $arTask['month'],
				$arTask['weekday']);
			$bResult = static::addCronTask($arCommand['COMMAND'], $strSchedule);
			if(!$bResult){
				static::deleteProfileCron($intProfileId);
				break;
			}
		}
		return $bResult;
	}

	/**
	 *	Check cron job exists
	 */
	public static function isCronTaskConfigured($strCommand, $strSchedule=''){
		if(!strlen($strCommand) || !static::isExec() || !static::isLinux()) {
			return false;
		}
		$strSchedule = strlen($strSchedule) ? trim($strSchedule).' ' : '';
		$strCommandEscaped = str_replace('"', '\"', $strCommand);
		$strPattern = preg_quote($strCommand).'(\s|$)';
		foreach(static::getCronTasks() as $arTask){
			if(preg_match('#'.$strPattern.'#i', $arTask['COMMAND'], $arMatch)){
				return true;
			}
		}
		return false;
	}

	/**
	 *	Check selected task schedule
	 */
	public static function getCronTaskSchedule($strCommand, $bAsArray=false){
		if(static::isCronTaskConfigured($strCommand)) {
			$arJobs = static::getCronTasks();
			foreach($arJobs as $arJob){
				if(stripos($arJob['COMMAND_FULL'], $strCommand) !== false) {
					$arJob = explode(' ', $arJob['COMMAND_FULL']);
					$arSchedule = array_slice($arJob,0,5);
					if($bAsArray) {
						return $arSchedule;
					}
					return implode(' ', $arSchedule);
				}
			}
		}
		return false;
	}
	
	/**
	 *	Parse full command (with time and command)
	 */
	public static function parseCronTask($strCommand){
		if(preg_match(static::CRON_JOB, $strCommand, $arMatch)) {
			$strProfileId = '';
			$strExternalId = '';
			$arCommand = preg_split('#[\s]+#', $strCommand);
			foreach($arCommand as $strPart){
				if(preg_match('#^'.static::PROFILE_ID.'=(.*?)$#', $strPart, $arMatch2)){
					$strProfileId = $arMatch2[1];
				}
				elseif(preg_match('#^'.static::EXTERNAL_ID.'=(.*?)$#', $strPart, $arMatch2)){
					$strExternalId = $arMatch2[1];
				}
			}
			return [
				'COMMAND_FULL' => $arMatch[0],
				'COMMAND' => $arMatch[7],
				'PROFILE_ID' => $strProfileId,
				'EXTERNAL_ID' => $strExternalId,
				'SCHEDULE' => $arMatch[1],
				'MINUTE' => $arMatch[2],
				'HOUR' => $arMatch[3],
				'DAY' => $arMatch[4],
				'MONTH' => $arMatch[5],
				'WEEKDAY' => $arMatch[6],
			];
		}
		return false;
	}
	
	/********************************************************************************************************************/
	
	/**
	 *	Get full cli command info
	 */
	public static function getFullCommand($strScriptName, $intProfileId=null, $strOutput=null, $strExternalId=null){
		$bCanAutoSet = static::canAutoSet();
		$strPhpPath = Helper::getOption('php_path');
		if(!strlen($strPhpPath)) {
			$strPhpPath = static::getDefaultPhpPath();
		}
		$strCommand = static::getProfilePhpCommand($intProfileId, $strScriptName, $strExternalId);
		$arSchedule = static::getCronTaskSchedule($strCommand, true);
		$arSchedule = is_array($arSchedule) ? $arSchedule : [];
		$bMbstring = Helper::getOption('php_mbstring', 'Y') == 'Y' ? true : false;
		$strConfig = Helper::getOption('php_config', '');
		$strStdout = Helper::getOption('php_output_stdout') == 'Y' && !is_null($strOutput) ? $strOutput : null;
		$strCommandFull = static::buildCommand($strPhpPath, $strCommand, $strScriptName, $bMbstring, $strConfig, $strStdout, $strExternalId);
		#
		$bAlreadyInstalled = static::isProfileOnCron($intProfileId, $strScriptName);
		if(!$bAlreadyInstalled){
			$arSchedule = ['*', '*', '*', '*', '*'];
		}
		#
		return [
			'COMMAND' => $strCommandFull,
			'COMMAND_SHORT' => $strCommand,
			'SCHEDULE' => $arSchedule,
			'ALREADY_INSTALLED' => $bAlreadyInstalled,
			'CAN_AUTO_SET' => $bCanAutoSet,
			'PHP_PATH' => $strPhpPath,
			'PHP_MBSTRING' => $bMbstring,
			'PHP_CONFIG' => $strConfig,
			'SCRIPT_NAME' => $strScriptName,
			'PROFILE_ID' => $intProfileId,
			'OUTPUT_FILENAME' => $strOutput,
		];
	}
	
	/**
	 *	Check php version
	 */
	public static function checkPhpVersion($strManualPhpPath){
		$arResult = [
			'SUCCESS' => false,
			'MESSAGE' => null,
			'VERSION' => null,
		];
		$strUsedPhpVersion = static::getSitePhpVersion();
		if(strlen($strManualPhpPath) && is_file($strManualPhpPath) && static::isExec()){
			if(!preg_match('#\s#', $strManualPhpPath)){
				exec($strManualPhpPath.' -v', $arOutput);
				$bFound = false;
				foreach($arOutput as $strOutput){
					if(preg_match('#PHP\s?(\d+\.\d+.\d+)#i', $strOutput, $arMatch)) {
						$bFound = true;
						$strCheckVersion = $arMatch[1];
						$arResult['VERSION'] = $strCheckVersion;
						if($strCheckVersion == $strUsedPhpVersion) {
							$arResult['SUCCESS'] = true;
							$arResult['MESSAGE'] = Helper::getMessage('Похоже, все настроено правильно.'."\n".'На сайте используется PHP #VERSION#.', array(
								'#VERSION#' => $strUsedPhpVersion,
							));
						}
						else{
							$arResult['MESSAGE'] = Helper::getMessage('Версия PHP по указанному пути #PHP_PATH# (#VERSION_TEST#)'."\n".'не совпадает в версией PHP сайта (#VERSION_SITE#).', array(
								'#PHP_PATH#' => $strManualPhpPath,
								'#VERSION_TEST#' => $strCheckVersion,
								'#VERSION_SITE#' => $strUsedPhpVersion,
							));
						}
					}
				}
				unset($arOutput);
				if(!$bFound){
					$arResult['MESSAGE'] = Helper::getMessage('Указан некорректный путь к PHP: #PHP_PATH#'."\n".'На сайте используется PHP #VERSION#.', array(
						'#PHP_PATH#' => $strManualPhpPath,
						'#VERSION#' => $strUsedPhpVersion,
					));
				}
			}
			else{
				$arResult['MESSAGE'] = Helper::getMessage('Указан некорректный путь к PHP: #PHP_PATH#'."\n".'На сайте используется PHP #VERSION#.', array(
					'#PHP_PATH#' => $strManualPhpPath,
					'#VERSION#' => $strUsedPhpVersion,
				));
			}
		}
		return $arResult;
	}

}
?>