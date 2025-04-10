<?
namespace WD\Antirutin; // The same namespace like a plugin

use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

class ValueItem {
	
	protected $arData;
	protected $strField;
	protected $arField;
	protected $mValue;
	protected $mDescription;
	
	/**
	 *	Get value
	 */
	public function __construct(array $arData){
		$this->arData = $arData;
		if(!is_null($arData['VALUE'])){
			$this->setValue($arData['VALUE']);
			if(!is_null($arData['FIELD'])){
				$this->setField($arData['FIELD']);
			}
			if(!is_null($arData['FIELD_ARRAY'])){
				$this->setFieldArray($arData['FIELD_ARRAY']);
			}
			if(!is_null($arData['DESCRIPTION'])){
				$this->setDescription($arData['DESCRIPTION']);
			}
			if(!is_null($arData['DESCRIPTION'])){
				$this->setDescription($arData['DESCRIPTION']);
			}
		}
	}
	
	/**
	 *	Get param
	 */
	public function get($key){
		return $this->arData[$key];
	}
	
	/**
	 *	Set param
	 */
	public function set($key, $value){
		$this->arData[$key] = $value;
	}
	
	/**
	 *	Get field
	 */
	public function getField(){
		return $this->strField;
	}
	
	/**
	 *	Set field
	 */
	public function setField($strField){
		$this->strField = $strField;
	}
	
	/**
	 *	Get field array
	 */
	public function getFieldArray(){
		return $this->arField;
	}
	
	/**
	 *	Set field array
	 */
	public function setFieldArray($arField){
		$this->arField = $arField;
	}
	
	/**
	 *	Get value
	 */
	public function getValue(){
		return $this->mValue;
	}
	
	/**
	 *	Set value
	 */
	public function setValue($mValue){
		$this->mValue = $mValue;
		$this->arData['VALUE'] = $mValue;
	}
	
	/**
	 *	Get description
	 */
	public function getDescription(){
		return $this->mDescription;
	}
	
	/**
	 *	Set description
	 */
	public function setDescription($mDescription){
		$this->mDescription = $mDescription;
		$this->arData['DESCRIPTION'] = $mValue;
	}

}

?>