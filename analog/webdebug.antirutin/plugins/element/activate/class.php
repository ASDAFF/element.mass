<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginElement;

class ActivateDeactivate extends PluginElement {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($intElementId){
		$bActivate = $this->get('mode') != 'deactivate';
		$strActive = $bActivate ? 'Y' : 'N';
		return $this->update($intElementId, ['ACTIVE' => $strActive]);
	}
	
}

?>