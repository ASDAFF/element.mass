<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strModuleCodeFull = str_replace('.', '_', WDA_MODULE);
$arOptions = &$arParams['OPTIONS'];
$obOptions = &$arParams['THIS'];

?>
<?foreach($arOptions as $arGroup):?>
	<tr class="heading">
		<td colspan="2"><?=$arGroup['NAME'];?><?if($arGroup['HINT']):?> <?=Helper::showHint($arGroup['HINT']);?><?endif?></td>
	</tr>
	<?foreach($arGroup['OPTIONS'] as $strOption => $arOption):?>
		<?
		$arOption['INPUT_ID'] = sprintf('%s_option_%s', $strModuleCodeFull, $strOption);
		$arOption['CODE'] = $strOption;
		$arOption['VALUE'] = $strValue = Helper::getOption($strOption);
		?>
		<tr id="wda-option-<?=$strOption;?>">
			<td width="40%"<?if($arOption['TOP'] == 'Y'):?> style="padding-top:10px; vertical-align:top;"<?endif?>>
				<?=Helper::showHint($arOption['HINT']);?>
				<label for="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>">
					<?if($arOption['REQUIRED']):?>
						<b><?=$arOption['NAME'];?></b>:
					<?else:?>
						<?=$arOption['NAME'];?>:
					<?endif?>
				</label>
			</td>
			<td width="40%">
				<?
				if(is_callable($arOption['CALLBACK_MAIN'])){
					call_user_func_array($arOption['CALLBACK_MAIN'], [$obOptions, $arOption]);
				}
				else{
					switch($arOption['TYPE']) {
						case 'text':
							?>
							<input type="text" name="<?=$strOption;?>" value="<?=$strValue;?>" id="<?=$arOption['INPUT_ID'];?>"
								<?=$arOption['ATTR'];?> />
							<?
							break;
						case 'password':
							?>
							<input type="password" name="<?=$strOption;?>" value="<?=$strValue;?>" id="<?=$arOption['INPUT_ID'];?>"
								<?=$arOption['ATTR'];?> />
							<?
							break;
						case 'textarea':
							?>
							<textarea name="<?=$strOption;?>" id="<?=$arOption['INPUT_ID'];?>"
								<?=$arOption['ATTR'];?> ><?=$strValue;?></textarea>
							<?
							break;
						case 'checkbox':
							if(stripos($arOption['ATTR'], 'disabled') !== false){
								$strValue = 'N';
							}
							?>
							<input type="hidden" name="<?=$strOption;?>" value="N" />
							<input type="checkbox" name="<?=$strOption;?>" value="Y" id="<?=$arOption['INPUT_ID'];?>"
								<?=$arOption['ATTR'];?> <?if($strValue=='Y'):?> checked="checked"<?endif?> />
							<?
							break;
						case 'select':
							?>
							<select name="<?=$strOption;?>" <?=$arOption['ATTR'];?> id="<?=$arOption['INPUT_ID'];?>">
								<?foreach($arOption['VALUES'] as $strItemValue => $strTitle):?>
									<option value="<?=$strItemValue;?>"
										<?if($strValue == $strItemValue):?> selected="selecter"<?endif?>><?=$strTitle;?></option>
								<?endforeach?>
							</select>
							<?
							break;
					}
				}
				if(is_callable($arOption['CALLBACK_MORE'])){
					print call_user_func_array($arOption['CALLBACK_MORE'], [$obOptions, $arOption]);
				}
				?>
			</td>
		</tr>
		<?
		if(is_callable($arOption['CALLBACK_BOTTOM'])){
			print call_user_func_array($arOption['CALLBACK_BOTTOM'], [$obOptions, $arOption]);
		}
		?>
	<?endforeach?>
<?endforeach?>