<?
namespace WD\Antirutin;

use
	\Bitrix\Main\Entity,
	\WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);

/**
 * Class ProfileActionTable
 * @package WD\Antirutin\Helper
 */

class ProfileActionTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'wd_antirutin_profile_action';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		$strLang = 'WDA_PROFILEACTION_';
		return [
			'ID' => new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage($strLang.'FIELD_ID'),
			]),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', [
				'title' => Helper::getMessage($strLang.'FIELD_PROFILE_ID'),
				'required' => true,
			]),
			'HASH' => new Entity\StringField('HASH', [
				'title' => Helper::getMessage($strLang.'FIELD_HASH'),
				'required' => true,
			]),
			'SORT' => new Entity\StringField('SORT', [
				'title' => Helper::getMessage($strLang.'FIELD_SORT'),
			]),
			'PLUGIN' => new Entity\StringField('PLUGIN', [
				'title' => Helper::getMessage($strLang.'FIELD_PLUGIN'),
				'required' => true,
			]),
			'TITLE' => new Entity\StringField('TITLE', [
				'title' => Helper::getMessage($strLang.'FIELD_TITLE'),
			]),
			'PARAMS' => new Entity\TextField('PARAMS', [
				'title' => Helper::getMessage($strLang.'FIELD_PARAMS'),
			]),
			'COLLAPSED' => new Entity\StringField('COLLAPSED', [
				'title' => Helper::getMessage($strLang.'FIELD_COLLAPSED'),
			]),
			'DATE_CREATE' => new Entity\DatetimeField('DATE_CREATE', [
				'title' => Helper::getMessage($strLang.'FIELD_DATE_CREATE'),
			]),
			'DATE_MODIFIED' => new Entity\DatetimeField('DATE_MODIFIED', [
				'title' => Helper::getMessage($strLang.'FIELD_DATE_MODIFIED'),
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
		$obResult = parent::add($data);
		return $obResult;
	}
	
	/**
	 *	Update item
	 */
	public static function update($primary, array $data) {
		if(!isset($data['DATE_MODIFIED'])){
			$data['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
		}
		$obResult = parent::update($primary, $data);
		return $obResult;
	}
	
}
?>