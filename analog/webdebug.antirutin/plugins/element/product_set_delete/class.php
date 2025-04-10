<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\PluginElement;

class ProductSetDelete extends PluginElement {

	const GROUP = 'SETS';
	
	/**
	 *	Execute!
	 */
	public function processElement($elementId){
		if(\Bitrix\Main\Loader::includeModule('catalog') && class_exists('CCatalogProductSet')){
			$bDeleteSet = $this->get('type') == 'all' || $this->get('type') == 'set';
			$bDeleteGroup = $this->get('type') == 'all' || $this->get('type') == 'group';
			if($bDeleteSet){
				\CCatalogProductSet::deleteAllSetsByProduct($elementId, \CCatalogProductSet::TYPE_GROUP);
			}
			if($bDeleteGroup){
				\CCatalogProductSet::deleteAllSetsByProduct($elementId, \CCatalogProductSet::TYPE_SET);
			}
		}
		return true;
	}

}
