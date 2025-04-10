<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\ValueItem,
	\WD\Antirutin\PluginElement;

class Execute extends PluginElement {
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bResult = false;
		$strCode = $this->get('code');
		if(empty($strCode)){
			$this->setError(static::getMessage('ERROR_NO_CODE'));
			return false;
		}
		$strCode = 'namespace WD\Antirutin;'.PHP_EOL.$strCode.';';
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