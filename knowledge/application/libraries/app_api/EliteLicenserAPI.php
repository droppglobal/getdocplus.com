<?php

class EliteLicenserAPI extends APP_API {
	
	function __construct() {
		$this->set_api_type( self::$API_TYPE_FIELD );
		$this->menu_icon = "ap ap-elite-licenser text-info";
		add_filter('api-action-title',[$this,'SetTitleFilter'],10,2);
		add_filter('api-action-title-EliteLicenser',[$this,'SetTitleFilter']);
	}
	
	public function SetTitleFilter($title,$api_name=''){
		$action    = GetValue( "action" );
		if ( $action == "chkhost" ) {
			return "Elite Licenser - Ban Host Check";
		}elseif ( $action == "chek_ban_host" ) {
			return "Elite Licenser - Ban Host Status";
		}
        return "Elite Licenser";
    }
	public function set_configuration_list() {
		$this->addInputText( "API Settings", "Server End Point", "api_server", "Your license eerver end point url", "", true, "", "fld-AppsbdLicense-api-type fld-AppsbdLicense-api-type-o" );
		$this->addInputText( "API Settings", "API Key", "api_key", "Enter the api of your license key", "", false );
		
		$this->addHtml("API Settings",'<hr/>');
		$this->addInputToggle("API Settings", "Check Support Expiry", "ischeck_expiry_date","If you enable it then one expire support it will return false return with a expiry message","N");
	}
	
	public function SetAdminMenu() {
		
		$apihost = $this->get_config_value( "api_server" );
		$api_key = $this->get_config_value( "api_key" );
		if ( ! empty( $apihost ) && ! empty( $api_key ) ) {
			$menu=AppMenu::AddAddonMenu("ADA","Elite Licenser",""," ap ap-elite-licenser",[],'','','',true);
			$menu->AddSubMenu("ADA","Check Elite License","admin/api-setting/process-api/" . $this->get_name() . "?action=mchklic","fa fa-crosshairs",  "popupform");
			$menu->AddSubMenu("ADA","Check Host","admin/api-setting/process-api/" . $this->get_name() . "?action=chkhost","fa fa-crosshairs",  "popupform");
			
			
		}
	}
	
	public function valid_configuration( array &$post_data, &$message = NULL ) {
		return true;
	}
	
	
	public function get_api_response( $field_value ,$is_force_result=false,$skip_checking=false) {
		$field_value =app_trim( $field_value );
		$response    = $this->apicall( "license/view_with_product_client", [ "license_code" => $field_value ] );
		$obj         = new APP_Field_API_Response();
		if ( ! empty( $response ) ) {
			$response = json_decode( $response );
			if ( !empty($response->status) ) {
				if($this->get_config_value("ischeck_expiry_date")=="Y"){
                   if(!$skip_checking) {
	                   if ( ! empty( $response->data->has_expiry ) && ( $response->data->has_expiry == "Y" && time() > strtotime( $response->data->expiry_time ) ) ) {
		                   $obj->SetResponse( false, "License key has been expire please renew license",
			                   $response->data );

		                   return $obj;
	                   }
	                   if ( ! $is_force_result && ( ! empty( $response->data->has_support ) && ( $response->data->has_support == "N" || ( $response->data->has_support == "Y" && time() > strtotime( $response->data->support_end_time ) ) ) ) ) {
		                   if ( $response->data->has_support == "N" ) {
			                   $obj->SetResponse( false,
				                   "This license key doesn't allow support, please change license", $response->data );
		                   } else {
			                   $obj->SetResponse( false, "Support time expire please renew support", $response->data );
		                   }

		                   return $obj;
	                   }
                   }
					$obj->SetResponse( true, "Successfully found", $response->data );
				}else{
					$obj->SetResponse( true, "Successfully found", $response->data );
				}
				
			} else {
				$obj->SetResponse( false, "Invalid Purchase Key", $response->data );
			}
		} else {
			$obj->SetResponse( false, "Invalid Purchase Key", NULL );
		}
		
		return $obj;
	}
	public function get_host_status( $host,$ip='') {
		$host = app_trim( $host );
		$ip   = app_trim( $ip );
		$response=$this->apicall( "host/check-ban", [
			"host" => $host,
			"ip"   => $ip
		] );
		if(is_object($response)){
		    return $response;
        }
		return json_decode($response);
	}
	public function unban_host( $host,$ip='') {
		$host = app_trim( $host );
		$ip   = app_trim( $ip );
		$response=$this->apicall( "host/unban", [
			"host" => $host,
			"ip"   => $ip
		] );
		if(is_object($response)){
			return $response;
		}
		return json_decode($response);
	}
	public function get_menu_title() {
		return "Elite Licenser";
	}
	
	
	/**
	 * @param APPAPIResponse $response_data
	 *
	 * @return string
	 */
	public function get_html_display_by_response( $response_data ) {
		
        $isAdmin=GetCurrentUserType()=="AD";
		ob_start();
		if ( $response_data->status && ! empty( $response_data->data ) ) {
			$returnUrl = $this->get_process_button_link() . "?action=mchklic&pcode=" . $response_data->data->purchase_key;
			$returnUrl = urlencode( $returnUrl );
			?>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default app-panel-box-2">
                        <div class="panel-heading p-l-5"><?php _e( "Buyer Details" ); ?></div>
                        <div class="panel-body p-0">

                            <table class="table m-b-0">
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Buyer Name" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td><?php echo $response_data->data->client_obj->name; ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e( "Email" ); ?></th>
                                    <th>:</th>
                                    <td><?php echo $response_data->data->client_obj->email; ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e( "Country" ); ?></th>
                                    <th>:</th>
                                    <td><?php echo $response_data->data->client_obj->country; ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e( "company" ); ?></th>
                                    <th>:</th>
                                    <td><?php echo $response_data->data->client_obj->company; ?></td>
                                </tr>
                                <tr>
                                    <th><?php _e( "Entry Date" ); ?></th>
                                    <th>:</th>
                                    <td><?php echo get_user_date_default_format( $response_data->data->client_obj->entry_time ); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 md-p-l-0">
                    <div class="panel panel-default app-panel-box-2">
                        <div class="panel-heading p-l-5"><?php _e( "License Details" ); ?></div>
                        <div class="panel-body p-0">
                            <table class="table m-b-0" style="z-index: 10; position: relative;">
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Product Name" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; "
                                        title="<?php echo $response_data->data->product_obj->product_name; ?>">
										<?php echo $response_data->data->product_obj->product_name; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 132px; "><?php _e( "License Title" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; "
                                        title="<?php echo $response_data->data->license_title; ?>">
										<?php echo $response_data->data->license_title; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Market" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; "
                                        title="<?php echo $response_data->data->market; ?>">
										<?php
											if ( empty( $response_data->data->market_description ) ) {
												$response_data->data->market_description = [];
											}
											$response_data->data->market_description = (array) $response_data->data->market_description;
											echo getTextByKey( $response_data->data->market, $response_data->data->market_description ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    $isLicExpire=!empty($response_data->data->expiry_time) && strtotime($response_data->data->expiry_time)<time();
                                    ?>
                                    <th style="width: 132px; "><?php _e( "License Expire Date" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip  text-bold <?php echo $response_data->data->has_expiry == "Y" && $isLicExpire?'text-danger':'text-success';?>"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; ">
										<?php echo $response_data->data->has_expiry == "Y"  ? (! empty( $response_data->data->expiry_time )?get_user_date_default_format( $response_data->data->expiry_time ): 'Unknown' ): "No Expiry"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    $isExpire=!empty($response_data->data->support_end_time) && strtotime($response_data->data->support_end_time)<time();
                                    ?>
                                    <th style="width: 132px; "><?php _e( "Support" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; ">
										<?php echo $response_data->data->has_support == "Y" && ! empty( $response_data->data->support_end_time ) ? '<span class=" '.($isExpire?'text-danger text-bold':'text-success').' ">'.get_user_date_default_format( $response_data->data->support_end_time ).'</span>' : "No Support"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Extra Param" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; "
                                        title="<?php echo $response_data->data->extra_param; ?>">
										<?php echo $response_data->data->extra_param; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Status" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; "
                                        title="<?php echo $response_data->data->status; ?>">
										<?php
											$response_data->data->status_description = (array) $response_data->data->status_description;
											echo getTextByKey( $response_data->data->status, $response_data->data->status_description );
											if (  $isAdmin && $response_data->data->status == "A" ) {
												?>

												<?php
											}
										?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 132px; "><?php _e( "Active Domains" ); ?></th>
                                    <th style="width: 8px; ">:</th>
                                    <td class="app-tooltip"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; ">

                                    </td>

                                </tr>
                                <tr>

                                    <td colspan="3"
                                        style="white-space: nowrap; overflow: hidden; max-width: 100px;  text-overflow: ellipsis; ">
                                        <div style="max-height: 100px; overflow: auto;">
                                            <ol>
												<?php foreach ( $response_data->data->active_domains as $active_domain ) {
												    $url=$this->APBD_CleanDomainName($active_domain);
													echo '<li>' . $active_domain . ((  $isAdmin && $response_data->data->status == "A" )? '
													<a class="popupformWR pull-right btn btn-success btn-xs" href="'.$this->get_process_button_link().'?action=resetkey&pcode='.$response_data->data->purchase_key.'&domain='.urlencode($url).'&rtn='.$returnUrl.'"
                                                   data-effect="mfp-move-from-top"  >'.__('Remove').'</a><div class="row m-t-5"></div> </li>':'');
												} ?>
                                            </ol>
                                        </div>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
			
			<?php
			
		} else {
			?>
            <h4 class="text-danger"><?php _e( "Response data doesn't valid" ); ?></h4>
			<?php
		}
		if ( GetCurrentUserType() == "AD" && ! empty( $response_data->data->purchase_code ) ) {
			
			$returnUrl = urlencode( current_url() );
			
			?>

            <div class="row">
                <div class="col-md-12 text-center p-b-15">
                    <a class="popupformWR btn btn-xs btn-info"
                       href="<?php echo $this->get_process_button_link(); ?>?action=chklic&pcode=<?php echo $response_data->data->purchase_code; ?>&rtn=<?php echo $returnUrl; ?>"><?php _e( "View License Information" ); ?></a>
                </div>
            </div>
			
			<?php
		}
		
		return ob_get_clean();
	}
    function APBD_CleanDomainName($domain) {
        $domain=app_trim($domain);
        $domain=strtolower($domain);
        $url=str_replace(['https://','http://'],"",$domain);
        $iswww=substr($url,0,4);
        if(strtolower($iswww)=="www."){
            $url=substr($url,4);
        }
        return $url;
    }
	
	public function do_porcess() {
		ob_start();
		$ci=get_instance();
		$ci->SetTitle("test");

		$action    = GetValue( "action" );
		$returnUrl = GetValue( "rtn" );
		$returnUrl = $returnUrl;
		if ( $action == "chklic" ) {
			$purchaseCode = GetValue( "pcode" );
			$this->getAppsbdLiceseCode( $purchaseCode, $returnUrl );
		} elseif ( $action == "mchklic" ) {
			$this->manualCheckLic();
		}elseif ( $action == "chkhost" ) {
			$this->check_host();
		}elseif ( $action == "unbanhost" ) {
			$this->unbanhost_request();
		}elseif ( $action == "chek_ban_host" ) {
			$this->host_list_view();
		} elseif ( $action == "pchklic" ) {
			$purchaseCode = GetValue( "pcode" );
			$this->manualCheckProcess( $purchaseCode, $returnUrl );
		} elseif ( $action == "resetkey" ) {
			$purchaseCode = GetValue( "pcode" );
			$this->resetKeyProcess( $purchaseCode, $returnUrl );
		}
		
		return ob_get_clean();
	}
	
	public function resetKeyProcess( $pcode, $returnUrl ) {
        $domain = GetValue( "domain" );
		$response = $this->apicall( "/license/remove_domain", [ "license_code" => $pcode, "domain" => $domain] );
		if ( ! empty( $response ) ) {
            $response2=$response;
			$response = json_decode( $response );

			if ( !empty($response->status) ) {
				redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=pchklic&pcode=$pcode" );
				return;
			} else {
                if(is_null($response) || empty($response->msg) || !is_object($response)){
                    AddError( "Not a valid response received form license server" );
                }else {
                    AddError($response->msg);
                }
                redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=pchklic&pcode=$pcode" );
                return;
			}
		}
	}
	public function reset_host( $host, $ip ) {
		$host = app_trim( $host );
		$ip   = app_trim( $ip );

		$response= $this->apicall( "host/unban", [
			"host" => $host,
			"ip"   => $ip
		]);

	}
	
	public function manualCheckProcess( $pcode, $returnUrl ) {
		$responseData = $this->get_api_response( $pcode,true ,true);
		$ci           = get_instance();
		if ( $responseData->status ) {
			?>
            <h3 class="p-t-10">License Key: <?php echo $pcode ?></h3>
			<?php
			echo $this->get_html_display_by_response( $responseData );
		} else {
		    if(!empty($responseData->msg)){
			?>
            
            <div class="alert alert-danger"><?php echo $responseData->msg; ?></div>
                <?php } ?>
            <div class="alert alert-danger">No data found</div>
			<?php
		}
		?>
        <div class="row btn-group-md popup-footer text-right">
			<?php if ( ! empty( $returnUrl ) ) { ?>
                <a class="<?php echo $ci->input->is_ajax_request() ? "popupformWR" : ""; ?> btn btn-info m-0"
                   href="<?php echo $returnUrl ?>"><?php _e( "Back" ); ?></a>
			<?php } ?>
            <button type="button" class="close-pop-up btn  btn-danger"><i
                        class="fa fa-times"></i> <?php _e( "Close" ); ?></button>
        </div>
		<?php
	}
	protected function check_host_response($responseData){
		if(is_object($responseData) ) {
			if ( empty( $responseData->status ) ) {
				if ( ! empty( $responseData->msg ) ) {
					AddError( $responseData->msg ,true);
				} else {
					AddError( "Error on request" ,true);
				}
				redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=chkhost");
			}else{
			    return;
			}
		}else{
			AddError( "Error on request2" ,true);
			redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=chkhost");
		}
	}
	public function unbanhost_request() {
		$host         = GetValue( 'host' );
		$ip           = GetValue( 'ip' );
		$responseData = $this->unban_host( $host, $ip );
		if(is_object($responseData) ) {
			if ( empty( $responseData->status ) ) {
				if ( ! empty( $responseData->msg ) ) {
					AddError( $responseData->msg ,true);
				} else {
					AddError( "Error on request" ,true);
				}
			}else{
				AddInfo( $responseData->msg ,true);
			}
		}
		redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=chek_ban_host&host=".urlencode($host)."&ip=".urlencode($ip));
	}
	public function host_list_view() {
            $host=GetValue('host');
            $ip=GetValue('ip');
            $responseData = $this->get_host_status( $host,$ip);
		    $this->check_host_response($responseData);
		    ?>
        <div class="clearfix">
            <div>
                <table class="table ">
                    <thead>
                        <tr>
                            <th><?php _e("Host") ; ?></th>
                            <th class="text-center"><?php _e("IP") ; ?></th>
                            <th class="text-right"><?php _e("Status") ; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($responseData)){
	                    foreach ( $responseData->data as $item ) {
                           ?>
                            <tr>
                                <td><?php echo $item->host; ?></td>
                                <td class="text-center"><?php echo $item->ip; ?></td>
                                <td class="text-right"><?php
				                    if($item->status!='N'){
					                    ?>
                                        <span class="text-danger"><?php _e("Banned") ; ?></span>
                                        <a href="<?php echo site_url( "admin/api-setting/process-api/" . $this->get_name() . "?action=unbanhost&host=".urlencode($item->host)."&ip=".urlencode($item->ip) ); ?>"
                                           class="btn btn-sm btn-danger popupform"><?php _e("Unban") ; ?></a>
					                    <?php
				                    }else{
					                    ?>
                                        <span class="text-success">Normal</span>
					                    <?php
				                    }?>
                                </td>
                            </tr>
                           <?php 
                        }
                        ?>
                    
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="row btn-group-md popup-footer ">
            <div class="col-md-6">
                <a href="<?php echo site_url("admin/api-setting/process-api/" . $this->get_name() . "?action=chkhost"); ?>" class="btn btn-info popupform"><i class="fa fa-angle-double-left"></i> Back</a>

            </div>
            <div class="col-md-6 text-right">

                <button type="button" class="close-pop-up btn  btn-danger"><i
                            class="fa fa-times"></i> <?php _e( "Cancel" ); ?></button>
            </div>

        </div>
		    <?php
	}
	public function check_host() {

		if ( IsPostBack ) {
		    $host=PostValue("host");
		    $ip=PostValue("ip");
		    if(empty($host)){
		        AddError("Host is required");
		    }
			redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=chek_ban_host&host=".urlencode($host)."&ip=".urlencode($ip) );

		}
		?>
        <div class="clearfix">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="control-label label-required" for="pcode"><?php _e( "Host" ); ?></label>
                    <input type="text" class="form-control" id="host" name="host" placeholder="Host"
                           data-bv-notempty="true"
                           data-bv-notempty-message="<?php _e( "Host is required" ); ?>">
                </div>
                <div class="form-group col-md-6">
                    <label class="control-label" for="host_ip"><?php _e( "IP" ); ?></label>
                    <input type="text" class="form-control" id="host_ip" name="ip" placeholder="Host">
                </div>
            </div>

        </div>
        <div class="row btn-group-md popup-footer text-right">
            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> View Status</button>
            <button type="button" class="close-pop-up btn  btn-danger"><i
                        class="fa fa-times"></i> <?php _e( "Cancel" ); ?></button>
        </div>
		<?php
	}
	public function manualCheckLic() {
		if ( IsPostBack ) {
			$purchaseCode = PostValue( "pcode" );
			if ( ! empty( $purchaseCode ) ) {
				redirect( "admin/api-setting/process-api/" . $this->get_name() . "?action=pchklic&pcode=$purchaseCode&rtn=" . current_url() );
				
				return;
			}
		}
		?>
        <div class="clearfix">
            <div class="form-group">
                <label class="control-label label-required" for="pcode"><?php _e( "Purchase Code" ); ?></label>
                <input type="text" class="form-control" id="pcode" name="pcode" placeholder="Purchase Code"
                       data-bv-notempty="true"
                       data-bv-notempty-message="<?php _e( "Purchase Code is required" ); ?>">
            </div>
        </div>
        <div class="row btn-group-md popup-footer text-right">
            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> View License Details</button>
            <button type="button" class="close-pop-up btn  btn-danger"><i
                        class="fa fa-times"></i> <?php _e( "Cancel" ); ?></button>
        </div>
		<?php
	}
	
	public function getAppsbdLiceseCode( $purchaseCode, $returnUrl = '' ) {
		
		$ci                       = get_instance();
		$licenseInfo              = new stdClass();
		$licenseInfo->domain      = "";
		$licenseInfo->type        = "";
		$licenseInfo->ip          = "";
		$licenseInfo->add_date    = "";
		$licenseInfo->app_version = "";
		$licenseInfo->status      = "";
		
		$request_param        = new stdClass();
		$request_param->pcode = $purchaseCode;
		$request_param->time  = time();
		$data                 = $this->callAppsbdLicense( $purchaseCode );
		$msg                  = "";
		if ( $data ) {
			if ( $data->status ) {
				$licenseInfo->domain      = $data->data->domain;
				$licenseInfo->type        = $data->data->lic_str;
				$licenseInfo->ip          = $data->data->ip;
				$licenseInfo->add_date    = $data->data->add_date;
				$licenseInfo->app_version = $data->data->app_version;
				$licenseInfo->status      = $data->data->status_str;
			} else {
				$msg = $data->msg;
			}
		}
		
		?>
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e( "Apps BD License info" ); ?></div>
            <div class="panel-body p-0">
				<?php if ( ! empty( $msg ) ) { ?>
                    <div class="col-md-12">
                        <div class="app-alert alert alert-danger m-t-10">
                            <i class="fa fa-exclamation-triangle faa-shake animated animated-2"></i>
							<?php echo $msg; ?>
                        </div>
                    </div>
				<?php } ?>
                <table class="table m-b-0">
                    <tr>
                        <th width="150px;">License Code</th>
                        <th width="10px;">:</th>
                        <td><?php echo $purchaseCode; ?></td>
                    </tr>
                    <tr>
                        <th width="150px;">License Type</th>
                        <th width="10px;">:</th>
                        <td><?php echo $licenseInfo->type; ?></td>
                    </tr>
                    <tr>
                        <th width="100px;">Domain</th>
                        <th width="10px;">:</th>
                        <td>
							<?php
								$licenseInfo->domain = ( strpos( $licenseInfo->domain, "http" ) === false ? "http://" : "" ) . $licenseInfo->domain;
								echo $licenseInfo->domain;
								if ( ! empty( $licenseInfo->domain ) ) {
									?>
                                    <a target="_blank" href="<?php echo $licenseInfo->domain; ?>"
                                       class="btn btn-xs btn-info">Visit</a>
								<?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>IP</th>
                        <th>:</th>
                        <td><?php echo $licenseInfo->ip; ?></td>
                    </tr>
                    <tr>
                        <th>Added</th>
                        <th>:</th>
                        <td><?php echo $licenseInfo->add_date; ?></td>
                    </tr>
                    <tr>
                        <th>App Version</th>
                        <th>:</th>
                        <td><?php echo $licenseInfo->app_version; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <th>:</th>
                        <td><?php echo $licenseInfo->status; ?>
                            <a class="<?php echo $ci->input->is_ajax_request() ? "popupformWR" : ""; ?> btn btn-xs  btn-danger m-0"
                               href="<?php echo $this->get_process_button_link(); ?>?action=resetkey&pcode=<?php echo $purchaseCode; ?>&rtn=<?php echo $returnUrl; ?>"><i
                                        class="fa fa-trash"></i> <?php _e( "Reset License Key" ); ?></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row btn-group-md popup-footer text-right">
			<?php if ( ! empty( $returnUrl ) ) { ?>
                <a class="<?php echo $ci->input->is_ajax_request() ? "popupformWR" : ""; ?> btn btn-info m-0 pull-left"
                   href="<?php echo $returnUrl ?>"><?php _e( "Back" ); ?></a>
			<?php } ?>


            <button type="button" class="close-pop-up btn  btn-danger"><i
                        class="fa fa-times"></i> <?php _e( "Close" ); ?></button>
        </div>
		<?php
		
	}
	
	private function callAppsbdLicense( $pcode ) {
		$url                  = "http://appsbdservice.com/applic/api/licinfo";
		$request_param        = new stdClass();
		$request_param->pcode = $pcode;
		$request_param->time  = time();
		$requestcode          = json_encode( $request_param );
		$key                  = "myappkey2018";
		$encrypt_code         = $this->app_encrypt( $requestcode, $key );
		$postarray            = [ "info" => $encrypt_code ];
		$param                = "?info=" . urlencode( $encrypt_code );
		$ch                   = curl_init();
		
		
		curl_setopt( $ch, CURLOPT_URL, $url . $param );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec( $ch );
		$info   = curl_getinfo( $ch );
		
		if ( $output === false ) {
			$obj             = new stdClass();
			$obj->status     = false;
			$obj->type       = "curl_error";
			$obj->msg        = curl_error( $ch );
			$obj->curl_errno = curl_errno( $ch );
			
			return json_encode( $obj );
		}
		curl_close( $ch );
		
		return ! empty( $output ) ? json_decode( $output ) : NULL;
		
	}
	
	private function resetAppsbdLicense( $pcode, $status = 'WD' ) {
		$url                       = "http://appsbdservice.com/applic/api/licinfo/reset";
		$request_param             = new stdClass();
		$request_param->pcode      = $pcode;
		$request_param->lic_status = $status;
		$request_param->time       = time();
		$requestcode               = json_encode( $request_param );
		$key                       = "myappkey2018";
		$encrypt_code              = $this->app_encrypt( $requestcode, $key );
		$postarray                 = [ "info" => $encrypt_code ];
		$param                     = "?info=" . urlencode( $encrypt_code );
		$ch                        = curl_init();
		
		
		curl_setopt( $ch, CURLOPT_URL, $url . $param );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec( $ch );
		$info   = curl_getinfo( $ch );
		
		if ( $output === false ) {
			$obj             = new stdClass();
			$obj->status     = false;
			$obj->type       = "curl_error";
			$obj->msg        = curl_error( $ch );
			$obj->curl_errno = curl_errno( $ch );
			
			return json_encode( $obj );
		}
		curl_close( $ch );
		
		return ! empty( $output ) ? json_decode( $output ) : NULL;
		
	}
	
	
	public function get_api_description() {
		ob_start();
		$response = $this->apicall( 'hello' );
		if ( ! empty( $response ) ) {
			$response = json_decode( $response );
			if ( !empty($response->status) ) {
				
				
				echo '<h3 class="m-t-0">ELite Licenser API successfully linked</h3>';
				
				?>
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e( "API Permissions" ); ?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">

                                <ul class="nav nav-stacked app-nav-stacked">
                                    <li>Add Product
                                        <span class="pull-right  ">
                                            <?php echo $response->data->add_product == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>
                                    <li>Edit Product <span class="pull-right  ">
                                            <?php echo $response->data->edit_product == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span></li>
                                    <li>View Product <span class="pull-right  ">
                                            <?php echo $response->data->view_product == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span></li>


                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="nav nav-stacked app-nav-stacked">

                                    <li>Add Client
                                        <span class="pull-right  ">
                                            <?php echo $response->data->add_client == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>
                                    <li>Edit Client
                                        <span class="pull-right  ">
                                            <?php echo $response->data->edit_client == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>
                                    <li>View Client
                                        <span class="pull-right  ">
                                            <?php echo $response->data->view_client == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>

                                </ul>

                            </div>
                            <div class="col-md-4">
                                <ul class="nav nav-stacked app-nav-stacked">

                                    <li>Add License
                                        <span class="pull-right  ">
                                            <?php echo $response->data->add_license == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>
                                    <li>Edit License
                                        <span class="pull-right  ">
                                            <?php echo $response->data->edit_license == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>
                                    <li>View License
                                        <span class="pull-right  ">
                                            <?php echo $response->data->view_license == "Y" ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>' ?>
                                        </span>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
				
				<?php
			} else {
				$apihost = $this->get_config_value( "api_server" );				
				$api_key = $this->get_config_value( "api_key" );
				if(!empty($apihost) && !empty($api_key)){
				    ?>
				    <div class="alert alert-danger">
                        Your API information is wrong.
                    </div>
				    <?php
                }
				?>
				<div class="panel panel-default">
				  <div class="panel-heading"><?php _e("About Eite Licenser"); ?></div>
				  <div class="panel-body">
                      <p class="card-text"><strong>Elite Licenser </strong> is a WordPress plugin for any types of
                          product licensing.
                          It also manages product updates, auto generates license code, built in
                          Envato licensing verification system,
                          full license control and more. It has full set of API, so you can handle
                          it by other applications as well.
                          One app handles license of all your products. You can handle any
                          language (PHP, .Net, Java, Android, etc.).
                          Also you can add licensing to more than one WordPress plugin or theme
                          and it can be installed on same WordPress.</p>

                      <a href="https://appsbd.com/elite-licenser" target="_blank" class="btn btn-sm btn-success">View Details</a>

                  </div>
				</div>
				<?php
			}
		}
		
		return ob_get_clean();
	}
	
	private function apicall( $url, $postarray = array() ) {
		$apihost = $this->get_config_value( "api_server" );
		$apihost = rtrim( $apihost, '/' ) . "/";
		$url     = ltrim( $url, '/' );
		$api_key = $this->get_config_value( "api_key" );
		if ( empty( $apihost ) || empty( $api_key ) ) {
			$obj             = new stdClass();
			$obj->status     = false;
			$obj->type       = "curl_error";
			$obj->error_msg  = "Invalid API Info";
			$obj->curl_errno = "";
			
			return json_encode( $obj );
		}
		
		$postarray['api_key'] = $api_key;
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $apihost . $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if ( count( $postarray ) > 0 ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postarray );
		}
		$output = curl_exec( $ch );
		$info   = curl_getinfo( $ch );
		
		if ( $output === false ) {
			$obj             = new stdClass();
			$obj->status     = false;
			$obj->type       = "curl_error";
			$obj->error_msg  = curl_error( $ch );
			$obj->curl_errno = curl_errno( $ch );
			
			return json_encode( $obj );
		}
		curl_close( $ch );
		
		return $output;
		
	}
	
	
}