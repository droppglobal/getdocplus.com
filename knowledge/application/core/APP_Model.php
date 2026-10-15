<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
require_once APPPATH."core".DIRECTORY_SEPARATOR."main".DIRECTORY_SEPARATOR."CORE_Model.php";
/**
 * @author Sarwar
 * @ Last Updated: 23/OCT/2016
 */
class APP_Model extends CORE_Model {
    function __construct(){
        parent::__construct();
    }
	
    public function Save()
    {
      if(property_exists($this, "pvid")){
          if(!$this->IsSetPrperty("pvid")){
              $pvid=ProviderSession::get_session_provider_id();
              $this->pvid($pvid);
          }
      }
      return parent::Save();        
    }
	
    public function GetNewIncId($fieldName, $default, $param = array())
    {
        
        if(property_exists($this, "pvid")){
            if(!isset($param['pvid'])){
                $param['pvid']=ProviderSession::get_session_provider_id();                
            }
        }
        return parent::GetNewIncId($fieldName, $default, $param);
    }
    
}
	