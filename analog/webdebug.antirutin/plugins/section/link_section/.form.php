<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('ACTION', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div id="<?=$this->getId('format');?>">
			<?=Helper::selectBox($this->getInputName('action'), [
				'set' => static::getMessage('ACTION_SET'),
				'delete' => static::getMessage('ACTION_DELETE'),
			], $this->get('action'), static::getMessage('ACTION_EMPTY'), 'data-role="action"');?>
		</div>
	</div>
</div>

<div class="plugin-form__field">
	<div class="plugin-form__field-title">
		<?=$this->fieldName('SECTION', true);?>
	</div>
	<div class="plugin-form__field-value">
		<div data-role="select_section">
			<div data-role="filter_wrapper">
				<input type="text" data-role="filter"
					placeholder="<?=static::getMessage('SECTION_FILTER_PLACEHOLDER')?>" />
			</div>
			<div data-role="select_wrapper">
				<?
				$arSections = IBlock::getIBlockSections($this->intIBlockId, $intMaxDepth=5);
				// $func = function($arSections, &$arParents){
					
				// };
				foreach($arSections as &$arSection){
					if($arSection['IBLOCK_SECTION_ID']){
						$intSectionId = $arSection['IBLOCK_SECTION_ID'];
						while(true){
							$arParent = $arSections[$intSectionId];
							if($arParent){
								unset($arParent['PARENTS']);
								$arSection['PARENTS'][$arParent['ID']] = $arParent;
								if($arParent['IBLOCK_SECTION_ID']){
									$intSectionId = $arParent['IBLOCK_SECTION_ID'];
									continue;
								}
							}
							break;
						}
					}
					$arSection['CHAIN'] = [];
					if(is_array($arSection['PARENTS'])){
						$arSection['PARENTS'] = array_reverse($arSection['PARENTS']);
						foreach($arSection['PARENTS'] as $arParent){
							$arSection['CHAIN'][] = array_intersect_key($arParent, array_flip(['ID', 'NAME']));
						}
					}
					$arSection['CHAIN'][] = array_intersect_key($arSection, array_flip(['ID', 'NAME']));
					if(Helper::getOption('section_name_chain') == 'Y'){
						$arSection['CHAIN'] = array_slice($arSection['CHAIN'], -1, 1);
					}
					$arSection['CHAIN_TEXT'] = implode(' / ', array_map(function($arSection){
						return sprintf('%s [%d]', $arSection['NAME'], $arSection['ID']);
					}, $arSection['CHAIN']));
				}
				unset($arSection);
				?>
				<select name="<?=$this->getInputName('section');?>" id="<?=$this->getId('section');?>" size="12"
					data-role="section">
					<?=Helper::includeFile('iblock_sections_select', [
						'ENTITY_TYPE' => Helper::TYPE_ELEMENT,
						'IBLOCK_ID' => $this->intIBlockId,
						'SECTIONS_ID' => $arSections,
						'SECTIONS_ID_SELECTED' => $this->get('section'),
						'CALLBACK_DATA' => function(&$arSection){
							$strText = htmlspecialcharsbx($arSection['TEXT']);
							$strText = str_replace('&amp;nbsp;', '&nbsp;', $strText);
							$strChain = htmlspecialcharsbx($arSection['CHAIN_TEXT']);
							$strChain = str_replace('&amp;nbsp;', '&nbsp;', $strChain);
							return sprintf('data-text-lite="%s" data-text-full="%s"', $strText, $strChain);
						}
					])?>
				</select>
			</div>
			<br/>
			<div>
				<span><?=static::getMessage('SECTION_SELECTED_COUNT');?>: </span>
				<span data-role="link_sections_count">0</span>
			</div>
		</div>
	</div>
</div>

<input type="hidden" data-role="error_no_action" value="<?=static::getMessage('ERROR_NO_ACTION');?>" />
<input type="hidden" data-role="error_no_section" value="<?=static::getMessage('ERROR_NO_SECTION');?>" />
