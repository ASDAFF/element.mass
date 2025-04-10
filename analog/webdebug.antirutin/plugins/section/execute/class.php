<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginSection;

class Execute extends PluginSection {
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		$bResult = false;
		$strCode = $this->get('code');
		if(empty($strCode)){
			$this->setError(static::getMessage('ERROR_NO_CODE'));
			return false;
		}
		$strCode = $strCode.';';
		$strCode = 'namespace WD\Antirutin;'.PHP_EOL.$strCode;
		$bResult = eval($strCode);
		return $bResult;
	}
	
	/**
	 *	Get demo code
	 */
	protected  function getDemoCode(){
		return static::getMessage('DEMO_CODE');
	}
	
}

?>