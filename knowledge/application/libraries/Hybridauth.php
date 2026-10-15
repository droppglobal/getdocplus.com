<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hybridauth Class
 */
class Hybridauth {

  /**
   * Reference to the Hybrid_Auth object
   *
   * @var Hybrid_Auth
   */
  public $HA;

  /**
   * Reference to CodeIgniter instance
   *
   * @var CI_Controller
   */
  protected $CI;

  /**
   * Class constructor
   *
   * @param array $config
   */
  public $current_config=null;
  public function __construct($config = array())
  {
    $this->CI =& get_instance();
    
    if (!$this->CI->load->config('hybridauth'))
    {
      log_message('error', 'Hybridauth config does not exist.');

      return;
    }

    
    $config = $this->CI->config->item('hybridauth');
    $provider=&$config['providers'];
      
      if(Mapp_setting_api::GetSettingsValue("social", "is_enable_envt_login","N")=="Y"){
          $provider['Envato']['keys']=array(
              "id"     => Mapp_setting_api::GetSettingsValue("social", "login_envt_client_id",""),
              "secret" => Mapp_setting_api::GetSettingsValue("social", "login_envt_secret","")
          );
          if(!empty($provider['Envato']['keys']['id']) && !empty($provider['Envato']['keys']['secret'])){
              $provider['Envato']['enabled']=TRUE;
          }
      }
      
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_g_login","N")=="Y"){       
        $provider['Google']['keys']=array(
            "id"     => Mapp_setting_api::GetSettingsValue("social", "login_g_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_g_secret","")            
        );
        if(!empty($provider['Google']['keys']['id']) && !empty($provider['Google']['keys']['secret'])){
            $provider['Google']['enabled']=TRUE;
        }
    }    
    
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_f_login","N")=="Y"){        
        $provider['Facebook']['keys']=array(
            "id"     => Mapp_setting_api::GetSettingsValue("social", "login_f_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_f_secret","")            
        );
        if(!empty($provider['Facebook']['keys']['id']) && !empty($provider['Facebook']['keys']['secret'])){
            $provider['Facebook']['enabled']=TRUE;
        }
    }
    
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_t_login","N")=="Y"){        
        $provider['Twitter']['keys']=array(
            "key"     => Mapp_setting_api::GetSettingsValue("social", "login_t_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_t_secret","")           
        );
        if(!empty($provider['Twitter']['keys']['key']) && !empty($provider['Twitter']['keys']['secret'])){
            $provider['Twitter']['enabled']=TRUE;
        }
    }    
    
    
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_gh_login","N")=="Y"){
        $provider['GitHub']['keys']=array(
            "id"     => Mapp_setting_api::GetSettingsValue("social", "login_gh_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_gh_secret","")
        );
        if(!empty($provider['GitHub']['keys']['id']) && !empty($provider['GitHub']['keys']['secret'])){
            $provider['GitHub']['enabled']=TRUE;
        }
    }
    
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_l_login","N")=="Y"){
        $provider['LinkedIn']['keys']=array(
            "id"     => Mapp_setting_api::GetSettingsValue("social", "login_l_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_l_secret","")
        );
        if(!empty($provider['LinkedIn']['keys']['id']) && !empty($provider['LinkedIn']['keys']['secret'])){
            $provider['LinkedIn']['enabled']=TRUE;
        }
    }
    
    if(Mapp_setting_api::GetSettingsValue("social", "is_enable_y_login","N")=="Y"){
        $provider['Yahoo']['keys']=array(
            "id"     => Mapp_setting_api::GetSettingsValue("social", "login_y_client_id",""),
            "secret" => Mapp_setting_api::GetSettingsValue("social", "login_y_secret","")
        );
        if(!empty($provider['Yahoo']['keys']['id']) && !empty($provider['Yahoo']['keys']['secret'])){
            $provider['Yahoo']['enabled']=TRUE;
        }
    }
      if(Mapp_setting_api::GetSettingsValue("social", "is_enable_ds_login","N")=="Y"){
          $provider['Discord']['keys']=array(
              "id"     => Mapp_setting_api::GetSettingsValue("social", "login_ds_client_id",""),
              "secret" => Mapp_setting_api::GetSettingsValue("social", "login_ds_secret","")
          );
          if(!empty($provider['Discord']['keys']['id']) && !empty($provider['Discord']['keys']['secret'])){
              $provider['Discord']['enabled']=TRUE;
          }
      }
    
    $config['base_url'] = $this->CI->config->site_url('index.php/social/endpoint');
   
   $this->current_config=$config;
    try
    {
      
      $this->_init();

      
      $this->HA = new Hybrid_Auth($config);

      log_message('info', 'Hybridauth Class is initialized.');
    }
    catch (Exception $e)
    {
	    throw new Exception($e->getMessage(), $e->getCode());
    	
    }
  }

  /**
   * Process the HA request
   */
  public function process()
  {
    $this->_init('Hybrid_Endpoint');

    Hybrid_Endpoint::process();
  }

  /**
   * Initialize HA library
   *
   * @param string $class_name The class name to initialize
   *
   * @throws \Exception
   */
  protected function _init($class_name = 'Hybrid_Auth')
  {
    list($dir, $filename) = explode('_', $class_name);

    if (class_exists($class_name))
    {
      
    }
    elseif (file_exists(APPPATH . "third_party/hybridauth/hybridauth/{$dir}/{$filename}.php"))
    {
      
      require_once APPPATH . "third_party/hybridauth/hybridauth/{$dir}/{$filename}.php";
    }
    elseif (file_exists(FCPATH . 'vendor/autoload.php'))
    {
      
      require_once FCPATH . 'vendor/autoload.php';
    }

    if (!class_exists($class_name))
    {
      throw new Exception("Could not load the {$class_name} class.");
    }

    log_message('info', "{$class_name} class is loaded.");
  }

}
