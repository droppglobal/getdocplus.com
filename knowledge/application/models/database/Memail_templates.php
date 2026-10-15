<?php
/**
 * Version 1.0.0
 * Creation date: 26/Sep/2016
 * @Written By: S.M. Sarwar Hasan
 * Genuity Systems
 * DB: k_word,type,subject,content,title
 */
class Memail_templates extends APP_Model{
	public $k_word;
	public $grp;
	public $status;
	public $subject;
	public $content;
	public $title;
	private static $_paramList=[];

	function __construct()
	{
		parent::__construct ();
		$this->SetValidation();
		$this->tableName="email_templates";
		$this->primaryKey="k_word";
		$this->uniqueKey=array();
		$this->multiKey=array();
		$this->autoIncField=array();
	}

	public static function setAddonParamList($keyword,$params){
		self::$_paramList[$keyword]=$params;
	}
	public static function getEmailParamList($keyword){
		$return_obj=array();
		$return_obj["site_name"]="Your site name";
		$return_obj["site_url"]="Your Site URL";
		if(isset(self::$_paramList[$keyword])) {
			return array_merge($return_obj,self::$_paramList[$keyword]);
		}
		switch ($keyword){
			case "API":
				$return_obj["name"]="Name of user";
				$return_obj["username"]="User login name";
				$return_obj["pass"]="User current password";
				$return_obj["login_button"]="User Login Button";
				break;
			case "UPI":
				break;
			case "TCL":
				$return_obj["ticket_feedback_button"]="Ticket Feedback Buttons";
			case "TRO":
				$return_obj["ticket_reopen_by"]="The user who reopen this ticket";
			case "TAC":
				$return_obj["ticket_closing_msg"]="Ticket Closing Message defined in Ticket settings";
			case "UOT":
				$return_obj["ticket_user"]="Name of ticket user";
			case "TRR":
				$return_obj["ticket_replied_user"]="The user who replaied last";
				$return_obj["replied_text"]="Replied Text";
			case "GOT":
				$return_obj["ticket_link"]="Ticket link";
				$return_obj["ticket_track_id"]="Ticket track id";
				$return_obj["ticket_title"]="Ticket title";
				$return_obj["ticket_category"]="Ticket cateroty";
				$return_obj["ticket_body"]="Ticket body";
				$return_obj["ticket_priroty"]="Ticket priroty";
				$return_obj["ticket_open_app_time"]="Ticket open time in app timezone (".date_default_timezone_get ().")";
				break;
			case "AFP":
				$return_obj["user_name"]="Admin or Staff name";
				$return_obj["recover_button"]="Recover Button";
				break;
			case "UFP":
				$return_obj["user_name"]="Name of user";
				$return_obj["recover_button"]="Recover Button";
				break;
			case "APC":
			case "UPC":
				$return_obj["user_name"]="Name of user";

				break;
			case "UWE":
				$return_obj["full_name"]="Name of user";
				$return_obj["login_info"]="User login information";
				break;
			case "AWE":
				$return_obj["full_name"]="Name of user";
				$return_obj["login_info"]="User login information";
				break;

			case "ANR":
				$return_obj["ticket_replied_user"]="User who replied";
				$return_obj["replied_text"]="Replied Text";
				$return_obj["ticket_status"]="Ticket current status";
			case "AAT":
				$return_obj["ticket_assigned_user"]="Name of ticket assigned user";
			case "ANT":
				$return_obj["ticket_user"]="Name of ticket user";
				$return_obj["ticket_link"]="Ticket link";
				$return_obj["ticket_track_id"]="Ticket track id";
				$return_obj["ticket_title"]="Ticket title";
				$return_obj["ticket_category"]="Ticket cateroty";
				$return_obj["ticket_body"]="Ticket body";
				break;
			default:
				break;
		}
		return $return_obj;
	}
	public static function getEmailParamListClearData($kayword){
		$return_obj=self::getEmailParamList($kayword);
		$return_obj=array_map(function($value){
			$value="";
		}, $return_obj);
		$return_obj["site_name"]=get_app_title();
		$return_obj["site_url"]=base_url();
		return $return_obj;
	}
	public static function addNewTemplate($k_word,$grp, $title,$status,$subject,$content){
		$obj=new self();
		if(!$obj->IsExists("k_word",$k_word)){
			$newobj=new self();
			$newobj->k_word($k_word);
			$newobj->grp($grp);
			$newobj->title($title);
			$newobj->status($status);
			$newobj->subject($subject);
			$newobj->content($content);
			return $newobj->Save();
		}else{
			return false;
		}
	}

	function Reset(){
		$this->merchant_id=$this->k_word=$this->status=$this->subject=$this->content=$this->title=null;
	}


	public function GetPropertyRawOptions($property,$isWithSelect=false){
		$returnObj=array();
		switch ($property) {
			case "status":
				$returnObj=array("A"=>"Active","I"=>"Inactive");
				break;
			default:
		}
		if($isWithSelect){
			return array_merge(array(""=>"Select"),$returnObj);
		}
		return $returnObj;

	}


	public function GetPropertyOptionsColor($property){
		$returnObj=array();
		switch ($property) {
			case "status":
				$returnObj=array("A"=>"green","I"=>"red");
				break;
			default:
		}
		return $returnObj;

	}

	function SetValidation(){
		$this->validations=array(
			"k_word"=>array("Text"=>"K Word", "Rule"=>"required|trim|max_length[3]"),
			"grp"=>array("Text"=>"Group", "Rule"=>"required|trim|max_length[20]"),
			"title"=>array("Text"=>"Title", "Rule"=>"required|trim|max_length[100]"),
			"status"=>array("Text"=>"Status", "Rule"=>"trim|max_length[1]"),
			"subject"=>array("Text"=>"Subject", "Rule"=>"required|trim|max_length[150]"),
			"content"=>array("Text"=>"Content", "Rule"=>"required|trim")

		);
	}
	static  function SendEmailTemplates($keyword,$toEmail,$subject="",$params=array(),$is_ticket_email=true){

		$obj=self::FindBy("k_word", $keyword);
		if(!empty($obj)){
			if($obj->status!="A"){
				return true;
			}
		}else{
			Mdebug_log::AddEmailLog("Email Send Failed, Unknown Keyword ({$keyword})", Mdebug_log::STATUS_FAILED , Mdebug_log::ENTRY_TYPE_ERROR);
			return false;
		}
		$search=array();
		$replace=array();
		foreach ($params as $key=>$value){
			$search[]="{{".$key."}}";
			$replace[]=$value;
		}
		if($is_ticket_email){
			$prefix=str_replace($search, $replace, '<div id="ticket_track_id" style="display: none;" >##TRACKID:{{ticket_track_id}}##</div>');
		}else{
			$prefix="";
		}
		$content=$prefix.str_replace($search, $replace, $obj->content);

		if(empty($subject)){
			$subject=$obj->subject;
		}
		$subject=str_replace($search, $replace, $subject);
		$content=self::getHtmlEmailContent($content,$subject,$is_ticket_email);
		$content=self::email_content_clean($content,true,true);
		$obj=new self();
		$obj->load->library("email");
		$obj->email->reinitialize();
		$obj->email->to($toEmail);
		$obj->email->subject($subject);
		$obj->email->message($content);
		return $obj->email->send(true);

	}
    static function email_content_clean($content,$is_full_html=false,$no_purify=false) {
	   if(!$no_purify){
		   $content = self::html_purify( $content );
       }
	    
	    $dom = new DOMDocument();
	    
	    $dom->preserveWhiteSpace = false;
        if(function_exists('mb_convert_encoding')){
		    $content=mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        }
	    $dom->loadHTML( $content );
	    $dom->formatOutput = true;
	    if ( $is_full_html ) {
		    return $dom->saveHTML();
	    } else {
		    $body = $dom->getElementsByTagName( 'body' )->item( 0 );

		    $content = ( $dom->saveHTML( $body ) );

		    return self::html_purify( $content );
	    }

    }
    static function html_purify($content) {
	    $config = HTMLPurifier_Config::createDefault();
	    $config->set('Core.Encoding', 'UTF-8');

	    $config->set('HTML.Doctype', 'HTML 4.01 Transitional'); 
	    
	    


	    $config->set('HTML.Allowed', 'div[style],p,a[href|title|target|class],img[src|alt|width|height|class],ul,ol,li,b,strong,i,em,br,table[border|cellspacing|cellpadding],thead,tbody,tr,th[colspan|rowspan],td[colspan|rowspan],h1,h2,h3,h4,h5,h6,span,hr,style,head,body');


	    $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,border-width,border-style,border-color,display');


	    $config->set('HTML.AllowedAttributes', 'id,style,class,src,alt,width,height,href');
	    $config->set('CSS.AllowTricky', true);


	    $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'ftp' => true]);

	    $config->set('Cache.DefinitionImpl', null);

	    $purifier = new HTMLPurifier($config);
	   return $purifier->purify($content);

    }
	static function getHtmlEmailContent($content,$subject="",$is_ticket_email=true){
		$content=self::email_content_clean($content);
		$css=file_get_contents(FCPATH."/css/mail.css");
		ob_start();
		?>
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title><?php echo $subject;?></title>
            <style type="text/css">
                <?php echo $css;?>

            </style>
        </head>
        <body data-start="start-here" itemscope itemtype="http://schema.org/EmailMessage"
              style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
              box-sizing: border-box; font-size: 14px;
              -webkit-font-smoothing: antialiased;
              -webkit-text-size-adjust: none;
              width: 100% !important; height: 100%;
              line-height: 1.6em; background-color: #f6f6f6;
              margin: 0;" bgcolor="#f6f6f6">
		<?php if($is_ticket_email){?><div style="display: none;">--start-</div><?php }?>
        <div class="body-container" style="padding: 20px;">
			<?php if($is_ticket_email){?><div class="replay-line" style="color:rgb(226, 223, 223); border-top: 1px dotted #ccc;font-size: 12px;"><?php echo Mapp_setting::GetSettingsValue('ticket_email_rp_str');?></div><?php }?>
            <div class="mail-container">
                <div class="mail-header"><strong><?php echo get_app_title();?></strong></div>
                <div class="mail-content">
					<?php echo $content;?>

                </div>
				<?php
				$footerEText='This email is a service from '.get_app_title().', delivered by <a href="http://olodesk.com" target="_blank">OLODesk</a> &copy; OlODESK'.date('Y');
				$emailFooter= Mapp_setting_api::GetSettingsValue("system", "email_footer",$footerEText);
				if(!empty($emailFooter)){
					?>
                    <div class="mail-footer">
                        <div class="copy-w-text"><?php echo $emailFooter;
							$isHidePoweredBy=Mapp_setting::GetSettingsValue("is_powered_by","Y")=="Y";
							if($isHidePoweredBy){
								?>
                                <br><?php _e("Powered By") ; ?> <a href="https://appsbd.com/bsspbl">Best Support System</a>
								<?php
							}
							?>
                        </div>
                    </div>
				<?php }?>
            </div>
        </div>
		<?php if($is_ticket_email){?><div style="display: none;">--end-</div><?php }?>
        </body>
        </html>

		<?php
		$contenthtml=ob_get_clean();
		return $contenthtml;
	}

	function GetAddForm($label_col=5,$input_col=7,$mainobj=null,$except=array(),$disabled=array()){

	}
}
