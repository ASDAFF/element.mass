<?
namespace WD\Antirutin\Plugins\Section;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock,
	\WD\Antirutin\PluginSection;

class LinkSection extends PluginSection {
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processSection($intSectionId){
		$bResult = false;
		$strAction = $this->get('action');
		$intSelectedSectionId = $this->get('section');
		if(!strlen($strAction)){
			$this->setError(static::getMessage('ERROR_NO_ACTION'));
			return false;
		}
		# Action!
		switch($strAction) {
			case 'set':
				if($intSelectedSectionId){
					$bResult = $this->update($intSectionId, [
						'IBLOCK_SECTION_ID' => $intSelectedSectionId,
					]);
				}
				else{
					$this->setError(static::getMessage('ERROR_NO_SECTION'));
				}
				break;
			case 'delete':
				$bResult = $this->update($intSectionId, [
					'IBLOCK_SECTION_ID' => false,
				]);
				break;
		}
		# Trigger for update
		if($bResult){
			$this->update($intSectionId, [
				'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
			]);
		}
		return $bResult;
	}
	
}

?>