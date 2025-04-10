<?
namespace WD\Antirutin\Plugins\Section;

use	\WD\Antirutin\PluginSection;

class Activate extends PluginSection {

	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************

	/**
	 *    Execute!
	 */
	public function processSection($intSectionId) : bool{
		$bResult = false;
		switch($this->get('mode')){
			case 'activate_absolutely':
				$bResult = $this->updateActiveWithoutConditions($intSectionId, true);
				break;
			case 'deactivate_absolutely':
				$bResult = $this->updateActiveWithoutConditions($intSectionId, false);
				break;
			case 'element_exist':
				$bResult = $this->updateActiveWithConditions($intSectionId, true);
				break;
			case 'element_absent':
				$bResult = $this->updateActiveWithConditions($intSectionId, false);
				break;
		}
		return $bResult;
	}

	protected function updateActiveWithConditions($intSectionId, bool $bNeedActivate) : bool{
		$bResult = false;
		$arElementFilter = $this->buildElementFilter($intSectionId);
		$bElementExist = !!\CIBlockElement::GetList([], $arElementFilter, false, ['nTopCount' => 1], ['ID'])->fetch();
		if ($bNeedActivate && $bElementExist) {
			$bResult = $this->updateActiveWithoutConditions($intSectionId, true);
		}
		if(!$bNeedActivate && !$bElementExist){
			$bResult = $this->updateActiveWithoutConditions($intSectionId, false);
		}
		return $bResult;
	}

	protected function buildElementFilter($intSectionId) : array {
		return [
			'IBLOCK_ID' => $this->intIBlockId,
			'SECTION_ID' => $intSectionId,
			'ACTIVE' => 'Y',
			'INCLUDE_SUBSECTIONS' => 'Y',
		];
	}

	protected function updateActiveWithoutConditions($intSectionId, bool $bNeedActivate) : bool {
		return $this->update($intSectionId, ['ACTIVE' => $bNeedActivate ? 'Y' : 'N']);
	}
}