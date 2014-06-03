<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Synac Technology, S.L., Roman Buechler <rb@synac.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Dmail unsubscription' for the 'dmail_unsubscribe' extension.
 *
 * @author	Synac Technology, S.L., Roman Buechler <rb@synac.com>
 * @package	TYPO3
 * @subpackage	tx_dmailunsubscribe
 */
class tx_dmailunsubscribe_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_dmailunsubscribe_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_dmailunsubscribe_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'dmail_unsubscribe';	// The extension key.
	var $receiverTable = ''; // table where receiver record comes from
  var $aReceiver     = array(); // receiver record (from fe_users or tt_address)

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {

    // init pi
    $this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	

	  // init template
	  $file = 'EXT:dmail_unsubscribe/res/html/template.html';
	  if(isset($this->conf['template'])) $file = $this->conf['template'];
    $this->templateCode = $this->cObj->fileResource($file);
    if(!$this->templateCode) return 'Please define a template';

		if(!$this->checkPrecondition()){
      $aMarker['ll_unsubscription_failed'] = $this->pi_getLL('unsubscription_failed');
      $subPart = $this->cObj->getSubpart($this->templateCode,'FAILURE_PANEL');
      $content = $this->cObj->substituteMarkerArray($subPart,$aMarker,'###|###',1);
    } else {
      // preconditions were met proceed with unsubscribing
      $uid = $this->aReceiver['uid'];
      
      // update fe_users record
      if($this->receiverTable === 'fe_users'){
        $rec = array(
          'tstamp'                      => time(),
          'module_sys_dmail_newsletter' => 0
        );
        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
          $this->receiverTable,
          'uid=' . $uid,
          $rec
        );
      };
      
      // update tt_address record
      if($this->receiverTable === 'tt_address'){
        $rec = array(
          'tstamp' => time(),
          'hidden' => 1
        );
        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
          $this->receiverTable,
          'uid=' . $uid,
          $rec
        );
      };
      
      // generate notification
      if($res){
        $aMarker['ll_unsubscription_successful'] = sprintf(
          $this->pi_getLL('unsubscription_successful'),
          $this->aReceiver['email']
        );
        $subPart = $this->cObj->getSubpart($this->templateCode,'SUCCESS_PANEL');
        $content = $this->cObj->substituteMarkerArray($subPart,$aMarker,'###|###',1);
      } else {
        $aMarker['ll_unsubscription_failed'] = $this->pi_getLL('unsubscription_failed');
        $subPart = $this->cObj->getSubpart($this->templateCode,'FAILURE_PANEL');
        $content = $this->cObj->substituteMarkerArray($subPart,$aMarker,'###|###',1);
      };
    };

		return $this->pi_wrapInBaseClass($content);
	}


	function checkPrecondition(){
		$cmd = t3lib_div::_GP('cmd'); // unsubscribe
		$sRid = t3lib_div::_GP('rid');
		$sRid = str_replace(array('fe_users','tt_address'),array('f','t'),$sRid);
		$aRid = t3lib_div::trimExplode('_',$sRid,1); // ###SYS_TABLE_NAME###_###USER_uid###
		$aC = t3lib_div::_GP('aC'); // ###SYS_AUTHCODE###
		
    // check command
    if($cmd !== 'unsubscribe') return false;
		
    // check reciever
    if(count($aRid)!=2) return false;
		if($aRid[0]!=='f' && $aRid[0]!=='t') return false;
		$aRid[1] = intval($aRid[1]);
		if($aRid[0]==='f'){
      // unsubscribe from table fe_users
      $this->receiverTable = 'fe_users';
    };
    if($aRid[0]==='t'){
      // unsubscribe from tt_address
      $this->receiverTable = 'tt_address';
    };
    $this->aReceiver = $this->getRecord($this->receiverTable,$aRid[1]);
    if(!$this->aReceiver) return false;
    
    //check authentication code
    $tempRow = array();
		foreach($this->aReceiver as $k => $v) {
			$tempRow[$k] = htmlspecialchars($v);
		}
		$authCode_fieldList = 'uid';
		if(isset($this->conf['authCode_fieldList'])) $authCode_fieldList = $this->conf['authCode_fieldList'];
		$authCode = t3lib_div::stdAuthCode($tempRow, $authCode_fieldList);
		if($authCode !== $aC) return false;
    
    return true;
  }


  function getRecord($tableName,$uid){
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
      '*',
      $tableName,
      'uid=' . $uid . ' ' . $this->cObj->enableFields($tableName)
    );
    $rows = array();
    while($row = $GLOBALS[TYPO3_DB]->sql_fetch_assoc($res)){
      $rows[] = $row;
    }
    $GLOBALS['TYPO3_DB']->sql_free_result($res);
    if (count($rows)) {
      return $rows[0];
    } else {
      return false;
    }
  }
    

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dmail_unsubscribe/pi1/class.tx_dmailunsubscribe_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dmail_unsubscribe/pi1/class.tx_dmailunsubscribe_pi1.php']);
}

?>