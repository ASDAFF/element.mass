<?
/**
 *	Class to work with options.php
 */

namespace WD\Antirutin;

use 
	\WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);

class Options {
	
	protected $arTabs;
	protected $arParams;
	protected $strBackUrl;
	
	protected $arGet;
	protected $arPost;

	protected $bPost;
	protected $bUpdate;
	protected $bApply;
	protected $bReset;
	
	protected $obTabControl;
	
	/**
	 *	Create object
	 */
	public function __construct($arTabs, $arParams=null){
		global $APPLICATION;
		#
		$this->arTabs = $arTabs;
		$this->arParams = is_array($arParams) ? $arParams : [];
		#
		$this->arGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
		$this->arPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();
		#
		$this->bPost = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isPost();
		$this->bUpdate = !!strlen($this->arPost['Update']);
		$this->bApply =  !!strlen($this->arPost['Apply']);
		$this->bReset =  !!strlen($this->arPost['RestoreDefaults']);
		#
		if(strlen($this->arGet['back_url_settings'])){
			$this->strBackUrl = $this->arGet['back_url_settings'];
		}
		#
		Helper::loadMessages(realpath(__DIR__.'/../../main/options.php'));
		Helper::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');
		#
		$this->obTabControl = new \CAdminTabControl(str_replace('.', '_', WDA_MODULE).'_tab_control', $arTabs);
		#
		\CUtil::initJSCore(['ajax', 'jquery', 'jquery2']);
		#
		foreach($this->arTabs as $strTab => $arTab){
			if(is_array($arTab['OPTIONS'])){
				$arTab['~OPTIONS'] = $arTab['OPTIONS'];
				$arTab['OPTIONS'] = $this->prepareOptions($arTab['OPTIONS']);
				foreach($arTab['OPTIONS'] as $strGroup => $arGroup){
					foreach($arGroup['OPTIONS'] as $strOption => $arOption){
						if(is_callable($arOption['HEAD_DATA'])){
							ob_start();
							call_user_func_array($arOption['HEAD_DATA'], [$this, $arOption]);
							$APPLICATION->addHeadString(ob_get_clean());
						}
					}
				}
			}
			$this->arTabs[$strTab] = $arTab;
		}
		#
		if($this->isSaving()){
			if($this->save()){
				$this->redirect();
				print 'Saved!';
			}
			else {
				print 'Error!';
			}
		}
		$this->display();
	}
	
	/**
	 *	Prepare options (include from file in /include/options/)
	 *	It all depends on start slash:
	 *	[ test/test.php]: /bitrix/modules/webdebug.antirutin/include/options/
	 */
	public function prepareOptions($arOptions){
		$arResult = [];
		$strOptionsPath = realpath(__DIR__.'/../include/options/');
		foreach($arOptions as $strFile){
			$strFile = $strOptionsPath.'/'.$strFile;
			if(is_file($strFile)){
				$arNewOptions = require($strFile);
				$arResult[] = array_merge($arResult, $arNewOptions);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check if saving in progress
	 */
	public function isSaving(){
		return $this->bPost && ($this->bUpdate || $this->bApply || $this->bReset);
	}
	
	/**
	 *	Do save options
	 */
	public function save(){
		global $APPLICATION;
		if($this->bUpdate || $this->bApply){
			// Fix old values
			$arOldValues = [];
			$arNewValues = [];
			foreach($this->arTabs as $strTab => $arTab){
				if(is_array($arTab['OPTIONS'])){
					foreach($arTab['OPTIONS'] as $arGroup){
						foreach($arGroup['OPTIONS'] as $strOption => $arOption) {
							$arOldValues[$strOption] = Helper::getOption($strOption);
						}
					}
				}
			}
			// Save new values
			foreach($this->arTabs as $strTab => $arTab){
				if(is_array($arTab['OPTIONS'])){
					foreach($arTab['OPTIONS'] as $arGroup){
						foreach($arGroup['OPTIONS'] as $strOption => $arOption) {
							$strValue = $this->arPost[$strOption];
							$arNewValues[$strOption] = $strValue;
							if(is_array($strValue)){
								$strValue = implode(',', $strValue);
							}
							if(is_callable($arOption['CALLBACK_BEFORE_SAVE'])){
								call_user_func_array($arOption['CALLBACK_BEFORE_SAVE'], [$this, &$strValue, $arOption]);
							}
							Helper::setOption($strOption, $strValue);
						}
					}
				}
			}
			// After all options saved
			foreach($this->arTabs as $strTab => $arTab){
				if(is_array($arTab['OPTIONS'])){
					foreach($arTab['OPTIONS'] as $arGroup){
						foreach($arGroup['OPTIONS'] as $strOption => $arOption) {
							if(is_callable($arOption['CALLBACK_SAVE'])){
								$arOption['VALUE_OLD'] = $arOldValues[$strOption];
								$arOption['VALUE_NEW'] = $arNewValues[$strOption];
								call_user_func_array($arOption['CALLBACK_SAVE'], [$this, $arOption]);
							}
						}
					}
				}
			}
		}
		elseif($this->bReset){
			Helper::removeAllOptions();
		}
		#
		ob_start();
		$module_id = WDA_MODULE; // required for save rights
		$REQUEST_METHOD = $this->bPost ? 'POST' : 'GET'; // required for save rights
		$Update = $this->bUpdate || $this->bApply ? 'Y' : 'N'; // required for save rights
		$GROUPS = $GLOBALS['GROUPS']; // required for save rights
		$RIGHTS = $GLOBALS['RIGHTS']; // required for save rights
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');
		ob_end_clean();
		#
		if(is_callable($this->arParams['CALLBACK_SAVE'])){
			call_user_func_array($this->arParams['CALLBACK_SAVE'], [$this]);
		}
		#
		return true;
	}
	
	/**
	 *	Redirect after save
	 */
	public function redirect(){
		global $APPLICATION;
		$mUrl = null;
		if(strlen($this->arGet['back_url_settings']) && $this->bUpdate){
			$mUrl = $this->arGet['back_url_settings'];
		}
		elseif(strlen($this->arGet['back_url_settings']) && ($this->bApply || $bthis->Reset)){
			$mUrl = [
				'lang' => LANGUAGE_ID,
				'mid' => WDA_MODULE,
				'back_url_settings' => $this->arGet['back_url_settings'],
			];
		}
		else{
			$mUrl = [
				'lang' => LANGUAGE_ID,
				'mid' => WDA_MODULE,
			];
		}
		if(is_array($mUrl)){
			$mUrl = $APPLICATION->getCurPage(false).'?'.http_build_query($mUrl);
			if(is_object($this->obTabControl)){
				$mUrl .= '&'.$this->obTabControl->activeTabParam();
			}
		}
		LocalRedirect($mUrl, false, 302);
	}
	
	/**
	 *	Display all
	 */
	public function display(){
		$this->start();
		foreach($this->arTabs as $strTab => $arTab){
			$this->next();
			if(is_callable($arTab['CALLBACK'])){
				call_user_func_array($arTab['CALLBACK'], [$this, $arTab]);
			}
			elseif(is_array($arTab['OPTIONS'])){
				$this->displayOptions($arTab['OPTIONS']);
			}
			elseif(isset($arTab['DATA'])){
				print '<tr>';
					print '<td>';
						print $arTab['DATA'];
					print '</td>';
				print '</tr>';
			}
			elseif($arTab['RIGHTS']){
				global $APPLICATION;
				$module_id = WDA_MODULE; // required for obtain rights
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');
			}
			else{
				print 'Empty!';
			}
		}
		$this->end();
	}
	
	/**
	 *	Display options block
	 */
	public function displayOptions($arOptions){
		print Helper::includeFile('options', [
			'OPTIONS' => $arOptions,
			'THIS' => $this,
		]);
	}
	
	/**
	 *	Start tab
	 */
	public function start(){
		global $APPLICATION;
		$arQuery = [
			'lang' => LANGUAGE_ID,
			'mid' => WDA_MODULE,
		];
		if(strlen($this->arGet['back_url_settings'])){
			$arQuery['back_url_settings'] = $this->arGet['back_url_settings'];
		}
		?>
		<form method="post" action="<?=$APPLICATION->GetCurPage();?>?<?=http_build_query($arQuery);?>">
		<?
		print bitrix_sessid_post();
		$this->obTabControl->begin();
	}
	
	/**
	 *	Begin new tab
	 */
	public function next(){
		$this->obTabControl->beginNextTab();
	}
	
	/**
	 *	End tabs, show buttons
	 */
	public function end(){
		$bDisabled = !!$this->arParams['DISABLED'];
		$strBackUrl = $this->strBackUrl;
		?>
		</form>
		<?
		$this->obTabControl->buttons();
		?>
			<input<?if($bDisabled):?> disabled="disabled"<?endif?> class="adm-btn-save" type="submit" name="Update" 
				value="<?=Helper::getMessage('MAIN_SAVE')?>" title="<?=Helper::getMessage('MAIN_OPT_SAVE_TITLE')?>" />
			<input<?if($bDisabled):?> disabled="disabled"<?endif?> type="submit" name="Apply" 
				value="<?=Helper::getMessage('MAIN_OPT_APPLY')?>" title="<?=Helper::getMessage('MAIN_OPT_APPLY_TITLE')?>" />
			<?if(strlen($strBackUrl)):?>
				<input<?if($bDisabled):?> disabled="disabled"<?endif?> type="button" name="Cancel" 
					value="<?=Helper::getMessage('MAIN_OPT_CANCEL')?>" title="<?=Helper::getMessage('MAIN_OPT_CANCEL_TITLE')?>" 
					onclick="window.location='<?=htmlspecialcharsbx(\CUtil::addSlashes($strBackUrl))?>';" />
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($strBackUrl)?>" />
			<?endif?>
			<input<?if($bDisabled):?> disabled="disabled"<?endif?> type="submit" name="RestoreDefaults"
				value="<?=Helper::getMessage('MAIN_RESTORE_DEFAULTS')?>"
				title="<?=Helper::getMessage('MAIN_HINT_RESTORE_DEFAULTS')?>"
				onclick="return confirm('<?=addSlashes(Helper::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')">
		<?
		$this->obTabControl->end();
	}

}
?>