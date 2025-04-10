<?
/**
 * Class to work with file uploader
 */

namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

class Uploader {
	
	/**
	 *	Upload file
	 */
	public static function uploadFile($strBase64Data, $strName=null, $strMimeType=null){
		$mResult = null;
		#
		if(preg_match('#^data:([a-z0-9-.]+/[a-z0-9-.]+);base64,#i', $strBase64Data, $arMatch)){
			if(is_null($strMimeType) || !strlen($strMimeType)){
				$strMimeType = $arMatch[1];
			}
			$strBase64Data = substr($strBase64Data, strlen($arMatch[0]));
		}
		#
		$strDir = static::getTmpDir().randString(16, 'abcdefghijklnmopqrstuvwxyz');
		if(!is_dir(Helper::root().$strDir)){
			mkdir(Helper::root().$strDir, BX_DIR_PERMISSIONS, true);
		}
		$strFile = strlen($strName) ? $strName : md5(randString(32)).static::getTypeExtension($strMimeType, true);
		$strFilename = $strDir.'/'.$strFile;
		#
		if($intSize = file_put_contents(Helper::root().$strFilename, base64_decode($strBase64Data, true))){
			$mResult = [
				'Name' => $strFilename,
				'Size' => $intSize,
				'Type' => $strMimeType,
			];
		}
		return $mResult;
	}
	
	/**
	 *	Get upload temporary directory
	 */
	public static function getTmpDir(){
		$strDir = sprintf('/%s/%s/tmp/', Helper::getOption('upload_dir', 'upload', 'main'), WDA_MODULE);
		if(!is_dir(Helper::root().$strDir)){
			mkdir(Helper::root().$strDir, BX_DIR_PERMISSIONS, true);
		}
		return $strDir;
	}
	
	/**
	 *	Get extension by type
	 */
	public static function getTypeExtension($strType, $bDot=false){
		$arTypes = [
			'image/jpg' => 'jpg',
			'image/jpeg' => 'jpeg',
			'image/png' => 'png',
			'image/gif' => 'gif',
			'image/bmp' => 'bmp',
		];
		return isset($arTypes[$strType]) ? ($bDot ? '.' : '').$arTypes[$strType] : null;
	}
	
	/**
	 *	Agent method for remove old files
	 */
	public static function agentRemoveTmpUploads(){
		$strTmpDir = Helper::root().static::getTmpDir();
		$arDirs = Helper::scandir($strTmpDir, [
			'FILES' => false,
			'DIRS' => true,
			'RECURSIVELY' => false,
		]);
		$intStoragePeriod = intVal(Helper::getOption('upload_storage_period'));
		$intTime = time();
		if($intStoragePeriod > 0){
			foreach($arDirs as $intDirKey => $strDir){
				$arFiles = Helper::scandir($strDir, [
					'FILES' => true,
					'DIRS' => false,
					'RECURSIVELY' => false,
				]);
				foreach($arFiles as $intFileKey => $strFile){
					if($intTime - filemtime($strFile) > $intStoragePeriod){
						@unlink($strFile);
						if(!is_file($strFile)){
							unset($arFiles[$intFileKey]);
						}
					}
				}
				if(empty($arFiles)){
					@rmdir($strDir);
				}
			}
		}
		return sprintf('%s();', __METHOD__);
	}

}
?>