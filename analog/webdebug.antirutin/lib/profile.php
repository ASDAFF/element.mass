<?
namespace WD\Antirutin;

use
	\Bitrix\Main\Entity,
	\WD\Antirutin\Cli,
	\WD\Antirutin\Helper,
	\WD\Antirutin\ProfileActionTable as ProfileAction;

Helper::loadMessages(__FILE__);

/**
 * Class ProfileTable
 * @package WD\Antirutin\Helper
 */

class ProfileTable extends Entity\DataManager {
	
	const LANG = 'WDA_PROFILE_';
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'wd_antirutin_profile';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		return [
			'ID' => new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage(static::LANG.'FIELD_ID'),
			]),
			'ACTIVE' => new Entity\StringField('ACTIVE', [
				'title' => Helper::getMessage(static::LANG.'FIELD_ACTIVE'),
			]),
			'NAME' => new Entity\StringField('NAME', [
				'title' => Helper::getMessage(static::LANG.'FIELD_NAME'),
				'required' => true,
			]),
			'CODE' => new Entity\StringField('CODE', [
				'title' => Helper::getMessage(static::LANG.'FIELD_CODE'),
			]),
			'DESCRIPTION' => new Entity\TextField('DESCRIPTION', [
				'title' => Helper::getMessage(static::LANG.'FIELD_DESCRIPTION'),
			]),
			'SORT' => new Entity\IntegerField('SORT', [
				'title' => Helper::getMessage(static::LANG.'FIELD_SORT'),
			]),
			'ENTITY_TYPE' => new Entity\StringField('ENTITY_TYPE', [
				'title' => Helper::getMessage(static::LANG.'FIELD_ENTITY_TYPE'),
				'required' => true,
			]),
			'IBLOCK_ID' => new Entity\IntegerField('IBLOCK_ID', [
				'title' => Helper::getMessage(static::LANG.'FIELD_IBLOCK_ID'),
				'required' => true,
			]),
			'SELECT_SECTIONS' => new Entity\StringField('SELECT_SECTIONS', [
				'title' => Helper::getMessage(static::LANG.'FIELD_SELECT_SECTIONS'),
			]),
			'MAX_DEPTH' => new Entity\IntegerField('MAX_DEPTH', [
				'title' => Helper::getMessage(static::LANG.'FIELD_MAX_DEPTH'),
			]),
			'SECTIONS_ID' => new Entity\TextField('SECTIONS_ID', [
				'title' => Helper::getMessage(static::LANG.'FIELD_SECTIONS_ID'),
			]),
			'INCLUDE_SUBSECTIONS' => new Entity\StringField('INCLUDE_SUBSECTIONS', [
				'title' => Helper::getMessage(static::LANG.'FIELD_INCLUDE_SUBSECTIONS'),
			]),
			'FILTER' => new Entity\TextField('FILTER', [
				'title' => Helper::getMessage(static::LANG.'FIELD_FILTER'),
			]),
			'SETTINGS' => new Entity\TextField('SETTINGS', [
				'title' => Helper::getMessage(static::LANG.'FIELD_SETTINGS'),
			]),
			'EMAIL' => new Entity\TextField('EMAIL', [
				'title' => Helper::getMessage(static::LANG.'FIELD_EMAIL'),
			]),
			'LOCKED' => new Entity\StringField('LOCKED', [
				'title' => Helper::getMessage(static::LANG.'FIELD_LOCKED'),
			]),
			'DATE_CREATE' => new Entity\DatetimeField('DATE_CREATE', [
				'title' => Helper::getMessage(static::LANG.'FIELD_DATE_CREATE'),
			]),
			'DATE_MODIFIED' => new Entity\DatetimeField('DATE_MODIFIED', [
				'title' => Helper::getMessage(static::LANG.'FIELD_DATE_MODIFIED'),
			]),
			'DATE_START' => new Entity\DatetimeField('DATE_START', [
				'title' => Helper::getMessage(static::LANG.'FIELD_DATE_START'),
			]),
			'DATE_FINISH' => new Entity\DatetimeField('DATE_FINISH', [
				'title' => Helper::getMessage(static::LANG.'FIELD_DATE_FINISH'),
			]),
		];
	}
	
	/**
	 *	Add item
	 */
	public static function add(array $data){
		if(!isset($data['DATE_CREATE'])){
			$data['DATE_CREATE'] = new \Bitrix\Main\Type\DateTime();
		}
		if(!isset($data['DATE_MODIFIED'])){
			$data['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
		}
		if(strlen($data['CODE']) && !static::isCodeUnique($data['CODE'])){
			$obResult = new \Bitrix\Main\ORM\Data\AddResult();
			$obResult->addError(new \Bitrix\Main\Error(Helper::getMessage(static::LANG.'ERROR_CODE_NOT_UNIQUE')));
			return $obResult;
		}
		$actions = $data['ACTIONS'];
		unset($data['ACTIONS']);
		if(!is_array($actions) || empty($actions)) {
			$obResult = new \Bitrix\Main\ORM\Data\AddResult();
			$obResult->addError(new \Bitrix\Main\Error(Helper::getMessage(static::LANG.'ERROR_NO_ACTIONS')));
			return $obResult;
		}
		$obResult = parent::add($data);
		if($obResult->isSuccess()){
			$primary = $obResult->getId();
			static::setProfileActions($primary, $actions);
		}
		return $obResult;
	}
	
	/**
	 *	Update item
	 */
	public static function update($primary, array $data) {
		if(!isset($data['DATE_MODIFIED'])){
			$data['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
		}
		if(strlen($data['CODE']) && !static::isCodeUnique($data['CODE'], $primary)){
			$obResult = new \Bitrix\Main\ORM\Data\AddResult();
			$obResult->setId($primary);
			$obResult->addError(new \Bitrix\Main\Error(Helper::getMessage(static::LANG.'ERROR_CODE_NOT_UNIQUE')));
			return $obResult;
		}
		$bSetActions = false;
		if(isset($data['ACTIONS'])){
			$bSetActions = true;
			$actions = $data['ACTIONS'];
			unset($data['ACTIONS']);
			if(!is_array($actions) || empty($actions)) {
				$obResult = new \Bitrix\Main\ORM\Data\AddResult();
				$obResult->addError(new \Bitrix\Main\Error(Helper::getMessage(static::LANG.'ERROR_NO_ACTIONS')));
				return $obResult;
			}
		}
		$obResult = parent::update($primary, $data);
		if($obResult->isSuccess() && $bSetActions){
			static::setProfileActions($primary, $actions);
		}
		return $obResult;
	}
	
	/**
	 *	Delete item
	 */
	public static function delete($primary) {
		$obResult = parent::delete($primary);
		if($obResult->isSuccess()){
			static::setProfileActions($primary, []);
			Cli::setProfileCronTasks($primary, []);
		}
		return $obResult;
	}
	
	/**
	 *	Is code unique
	 */
	public static function isCodeUnique($code, $primary=null){
		if(strlen($code)){
			$code = trim($code);
		}
		if(!strlen($code)){
			return true;
		}
		$arFilter = [
			'=CODE' => $code,
		];
		if(!is_null($primary)){
			$arFilter['!ID'] = $primary;
		}
		$arResult = static::getList([
			'filter' => $arFilter,
			'select' => ['CNT'],
			'runtime' => [
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
			],
		])->fetch();
		return !$arResult['CNT'];
	}
	
	/**
	 *	Get all actions for profile
	 */
	public static function getProfileActions($primary){
		$arResult = [];
		$resCurrentPluginActions = ProfileAction::getList([
			'order' => ['SORT' => 'ASC'],
			'filter' => ['PROFILE_ID' => $primary],
		]);
		while($arCurrentPluginAction = $resCurrentPluginActions->fetch()){
			$arCurrentPluginAction['~PARAMS'] = $arCurrentPluginAction['PARAMS'];
			$arCurrentPluginAction['PARAMS'] = unserialize($arCurrentPluginAction['PARAMS'], ['allowed_classes' => false]);
			if(!is_array($arCurrentPluginAction['PARAMS'])){
				$arCurrentPluginAction['PARAMS'] = [];
			}
			if(!is_array($arCurrentPluginAction) || !strlen($arCurrentPluginAction['HASH'])){
				continue;
			}
			$arCurrentPluginAction['PARAMS'] = array_merge([
				'_plugin' => $arCurrentPluginAction['PLUGIN'],
				'_title' => $arCurrentPluginAction['TITLE'],
				'_collapsed' => $arCurrentPluginAction['COLLAPSED'] == 'Y' ? 'Y' : 'N',
				'_sort' => $arCurrentPluginAction['SORT'],
			], $arCurrentPluginAction['PARAMS']);
			$arResult[$arCurrentPluginAction['HASH']] = $arCurrentPluginAction;
		}
		return $arResult;
	}
	
	/**
	 *	Update actions (for both add and update)
	 */
	protected static function setProfileActions($primary, array $actions){
		$bResult = true;
		# Prepare, transform rand_id_***
		foreach($actions as $strActionHash => $arAction){
			$actions[$strActionHash] = Helper::replaceActionRandInputValues($arAction);
		}
		# Save
		$arCurrentPluginActions = [];
		$resCurrentPluginActions = ProfileAction::getList([
			'order' => ['ID' => 'ASC'],
			'filter' => ['PROFILE_ID' => $primary],
			'select' => ['ID', 'HASH'],
		]);
		while($arCurrentPluginAction = $resCurrentPluginActions->fetch()){
			$arCurrentPluginActions[$arCurrentPluginAction['HASH']] = $arCurrentPluginAction;
		}
		$obConnection = \Bitrix\Main\Application::getConnection();
		$obConnection->startTransaction();
		foreach($arCurrentPluginActions as $strHash => $arAction){
			if(!empty($actions[$strHash])){
				$arNewAction = static::prepareActionArray($primary, $strHash, $actions[$strHash]);
				$obResult = ProfileAction::update($arAction['ID'], $arNewAction);
				unset($actions[$strHash]);
				if(!$obResult->isSuccess()){
					$bResult = false;
					break;
				}
			}
			else{
				$obResult = ProfileAction::delete($arAction['ID']);
				if(!$obResult->isSuccess()){
					$bResult = false;
					break;
				}
			}
		}
		foreach($actions as $strHash => $arAction){
			if(is_array($arAction) && !empty($arAction)){
				$arNewAction = static::prepareActionArray($primary, $strHash, $arAction);
				$obResult = ProfileAction::add($arNewAction);
				if(!$obResult->isSuccess()){
					$bResult = false;
					break;
				}
			}
		}
		if($bResult){
			$obConnection->commitTransaction();
		}
		else{
			$obConnection->rollbackTransaction();
		}
		return $bResult;
	}
	
	/**
	 *	Prepare action for save
	 */
	protected static function prepareActionArray($primary, $strHash, $arAction){
		$strPlugin = $arAction['_plugin']; unset($arAction['_plugin']);
		$strTitle = $arAction['_title']; unset($arAction['_title']);
		$strCollapsed = $arAction['_collapsed']; unset($arAction['_collapsed']);
		$strSort = $arAction['_sort']; unset($arAction['_sort']);
		$arResult = [
			'PROFILE_ID' => $primary,
			'HASH' => $strHash,
			'SORT' => $strSort,
			'PLUGIN' => $strPlugin,
			'TITLE' => $strTitle,
			'PARAMS' => serialize($arAction),
			'COLLAPSED' => $strCollapsed == 'Y' ? 'Y' : 'N',
		];
		if(!is_numeric($arResult['SORT']) || $arResult['SORT'] <= 0){
			unset($arResult['SORT']);
		}
		return $arResult;
	}

	/**
	 * Save DATE_START for profile
	 */
	public static function setDateStart($primary){
		return static::update($primary, ['DATE_START' => new \Bitrix\Main\Type\DateTime()])->isSuccess();
	}

	/**
	 * Save DATE_FINISH for profile
	 */
	public static function setDateFinish($primary){
		return static::update($primary, ['DATE_FINISH' => new \Bitrix\Main\Type\Datetime])->isSuccess();
	}

	/**
	 * Lock profile
	 */
	public static function lock($primary){
		$bResult = false;
		if(!is_file($strFilename = static::getProfileLockFilePath($primary))){
			if(!is_dir($strDir = pathinfo($strFilename, PATHINFO_DIRNAME))){
				mkdir($strDir, BX_DIR_PERMISSIONS, true);
			}
			touch($strFilename);
		}
		if(is_file($strFilename)){
			$resLockFile = fopen($strFilename, 'a');
			$bResult = flock($resLockFile, LOCK_EX|LOCK_NB);
		}
		return $bResult;
	}

	/**
	 * Unlock profile
	 */
	public static function unlock($primary){
		$bResult = true;
		$strFilename = static::getProfileLockFilePath($primary);
		if(is_file($strFilename)){
			$resLockFile = fopen($strFilename, 'a');
			flock($resLockFile, LOCK_UN);
			fclose($resLockFile);
			#unlink($resLockFile);
		}
		return $bResult;
	}

	/**
	 * Get filename for profile lock
	 */
	public static function getProfileLockFilePath($primary){
		return sprintf('%s%s/%s', Helper::root(), static::getLockDir(), static::getProfileLockFileName($primary));
	}

	/**
	 * Get filename for profile lock
	 */
	public static function getProfileLockFileName($primary){
		return sprintf('profile_%03d.lock', $primary);
	}

	/**
	 * Get dir with lock
	 */
	public static function getLockDir(){
		return sprintf('/upload/%s/lock', WDA_MODULE);
	}
	
}
?>