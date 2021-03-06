<?php
defined('PHPFOX') or exit('NO DICE!');

class Newapi_Service_Dvs extends Phpfox_Service {
    function __construct() {
        $this->_oApi = Phpfox::getService('newapi');
        $this->_sTable = Phpfox::getT('ko_brightcove');
    }

    public function getVins() {
        $iTotal = 0;
        $aRows = array();
        $iDvsId = $this->_oApi->get('dvs');
        $sVin = $this->_oApi->get('vin');
        $aVins = explode(',', $sVin);
        $aDvs = array(
            'vin_button_label' => 'Virtual Test Drive'
        );

        if($iDvsId && count($aVins)) {
            list($aRows, $aDvs) = Phpfox::getService('dvs.vin')->getVins($aVins, $iDvsId);
            $iTotal = count($aRows);
        }

        $this->_oApi->setTotal($iTotal);
        return array('vin' => $aRows, 'text' => $aDvs['vin_button_label']);
    }
}
?>