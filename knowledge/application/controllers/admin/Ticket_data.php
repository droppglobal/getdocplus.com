<?php
/**
 * Version 1.0.0
 * Creation date: 03/Oct/2017
 * @Written By: S.M. Sarwar Hasan
 * Sarwar Hasan
 */
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
APP_Controller::LoadGridController ();
class Ticket_data extends APP_GridDataController {
	private function set_order_by(){
		if (empty ( $this->orderBy )) {
			$this->orderBy=['last_replied_by_type'=>'DESC','last_reply_time'=>'DESC'];
		}
	}
	private function set_need_reply($id,&$mainobj){
		$cookie_name=Mapp_setting::get_cookie_prefix()."_nr_".$id;
		if(isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]=="Y"){
			$mainobj->last_replied_by_type("='' or last_replied_by_type='U'",true);
		}
	}
	private function set_default_order_by(){
		if (empty ( $this->orderBy )) {
			$this->orderBy=['last_replied_by_type'=>'DESC','last_c_reply'=>'DESC'];
		}
	}
	private function get_ticket_list($status = '') {
		
		$is_show_reply=false;
	    $mainobj = new Mticket ();
	    $status = strtoupper ( $status );
		if ($status == "A") {			
			$mainobj->status("A");
			if (empty ( $this->orderBy )) {
				$this->orderBy = "opened_time";
				$this->order = "DESC";
			}
		}  elseif ($status == "P") {
			$is_show_reply=true;
			$mainobj->status("!='C'",true);
			$this->set_need_reply('aat',$mainobj);
			$this->set_default_order_by();
		}

		elseif ($status == "C") {
			$mainobj->status("C");
			if (empty ( $this->orderBy )) {
				$this->orderBy = "last_c_reply";
				$this->order = "DESC";
			}
		}elseif ($status == "MY") {
			$is_show_reply=true;
			$this->set_need_reply('myt',$mainobj);
			$mainobj->status("!='C'",true);
			$userdata=GetAdminData();
			$mainobj->assigned_on($userdata->id);
			$this->set_default_order_by();
		}elseif ($status == "MA") {
			$is_show_reply=true;
			$userdata=GetAdminData();
			$mainobj->assigned_on($userdata->id);
			$this->set_need_reply('mat',$mainobj);
			$this->set_default_order_by();
		}elseif ($status == "MU") {
			$is_show_reply=true;
			$userdata=GetAdminData();
			$mainobj->assigned_on("");
			$this->set_need_reply('unt',$mainobj);
			$mainobj->status("!='C'",true);
			$this->set_default_order_by();
		}
		elseif ($status == "MC") {			
			$mainobj->status("C");
			$userdata=GetAdminData();
			$mainobj->assigned_on($userdata->id);
			if (empty ( $this->orderBy )) {
				$this->orderBy = "last_reply_time";
				$this->order = "DESC";
			}
		}elseif ($status == "MP") {
			$is_show_reply=true;
			$mainobj->status("!='C'",true);
			$userdata=GetAdminData();
			$this->set_need_reply('mpt',$mainobj);
			$mainobj->assigned_on($userdata->id);
			$mainobj->is_paid_ticket("Y");
			$this->set_default_order_by();
		}elseif ($status == "AP") {
			$is_show_reply=true;
			$this->set_need_reply('apt',$mainobj);
			$mainobj->is_paid_ticket("Y");
			$this->set_default_order_by();
		} else {			
			$mainobj->status("!='C'",true);
			$this->set_default_order_by();
		}
		
		$this->setDownloadFileName ( "ticket-list" );
		$customs=[];
		$hasCustomData=false;
		$mainobj->setCustomFields($customs,$hasCustomData);
        $site_user=new Msite_user();
        $mainobj->Join($site_user, "id", "ticket_user","left");
		if($this->srcItem=='cat_id'){
		    $mainobj->cat_id($this->srcText);
            $this->srcItem="";
            $this->srcText="";
        }elseif($this->srcItem=='client_email'){
            $site_user->email("like '{$this->srcText}%'",true);
            $this->srcItem="";
            $this->srcText="";
        }elseif($this->srcItem=='client_name'){
            $site_user->first_name("like '%{$this->srcText}%' OR last_name like '%{$this->srcText}%'",true);
            $this->srcItem="";
            $this->srcText="";
        }elseif($this->srcItem=='in_ticket_body' && !empty($this->srcText)){
			$this->srcText=trim($this->srcText);
			$query="MATCH(title, ticket_body) AGAINST('".$this->srcText."' IN BOOLEAN MODE)";
			$mainobj->title(" is not null and ".$query,true);
			$this->srcItem="";
			$this->srcText="";
		}
		$searchType='both';
		$records = $mainobj->CountALL ( $this->srcItem, $this->srcText, $this->multiparam, $searchType );

		if ($records > 0) {
			$this->SetGridRecords ( $records );
			$result = $mainobj->SelectAllGridData ( "id,ticket_track_id,cat_id,title,ticket_user,opened_time,re_open_time,re_open_by,re_open_by_type,user_type,status,assigned_on,last_replied_by,last_replied_by_type,last_reply_time,ticket_rating,priroty,is_public,is_open_using_email,is_paid_ticket,reply_counter,first_name,last_name,photo_url", $this->orderBy, $this->order, $this->rows, $this->limitStart, $this->srcItem, $this->srcText, $this->multiparam, $searchType );
			if ($result) {
				$has_edit_permission = ACL::HasPermission ( "admin/ticket/edit" );
				$has_delete_permission = ACL::HasPermission ( "admin/ticket-confirm/ticket-delete" );
				$has_details_permission = ACL::HasPermission ( "admin/ticket/details" );
				$has_change_category_permission = ACL::HasPermission ( "admin/ticket/change-category" );
				
				$re_open_by_type_options = $mainobj->GetPropertyOptions ( "re_open_by_type" );
				$user_type_options = $mainobj->GetPropertyOptions ( "user_type" );
				$status_options = $mainobj->GetPropertyOptionsTag( "status" );
				$priroty_options = $mainobj->GetPropertyOptionsTag( "priroty" );
				$usersType=[];
				
				foreach ( $result as $data ) {

					$data->last_replied_by_type=$data->last_replied_by_type=="G"?"U":$data->last_replied_by_type;

					$data->re_open_by_type =$data->re_open_by_type=="G"?"U":$data->re_open_by_type;
					if($hasCustomData) {
						Mticket_custom_field::bindGridCustomFieldData( $data, $customs );
					}
					if($data->re_open_by_type=="U" && !in_array($data->re_open_by, $usersType)){
						$usersType[]=$data->re_open_by;
					}
					if($data->last_replied_by_type=="U" && !in_array($data->last_replied_by, $usersType)){
						$usersType[]=$data->last_replied_by;
					}
					
					$data->title="<a class='ticket-title' target='_blank' href='" . site_url ("admin/ticket/details/".$data->id) ."'>".$data->title."</a>";
					
				}
				$user_list=[];
				if(!empty($usersType)){					
					$msiteu=new Msite_user();
					$status_str="'".implode("','", $usersType)."'";
					$msiteu->id("in ($status_str)",true);
					$user_list=$msiteu->SelectAllWithIdentity("id",'id,first_name,last_name,photo_url');
				}

				 $isHidepriority=Mapp_setting::GetSettingsValue("is_priority_ad_hide", "N")== "Y";
				$staff_list=Mapp_user::get_all_user();				
				foreach ( $result as &$data ) {
                    $data->action = "";
                    if ($has_details_permission) {
                        $data->action .= " <a class='btn btn-success btn-xs' href='" . site_url("admin/ticket/details/" . $data->id) . "'><i class='fa fa-eye'></i> " . __("Details") . "</a>";
                    }
                    if ($has_delete_permission) {
                        $data->action .= " <a class='ConfirmAjaxWR btn btn-danger btn-xs' data-msg='Are you sure to delete?' href='" . site_url("admin/ticket-confirm/ticket-delete/" . $data->id) . "'><i class='fa fa-trash'></i>" . __("Delete") . "</a>";
                    }
                    $data->re_open_by_type = getTextByKey($data->re_open_by_type, $re_open_by_type_options);
                    $data->user_type = getTextByKey($data->user_type, $user_type_options);
                    if ($isHidepriority) {
                        $data->priroty = "";
                    } else {
                        $data->priroty = "<a data-effect='mfp-move-from-top' class='popupformWR' href='" . admin_url("ticket/change-priority/" . $data->id ) . "'>".getTextByKey($data->priroty, $priroty_options)."</a>";
                    }

                    $bkUser= $data->ticket_user;
                    $data->ticket_user = get_grid_user_img($data->first_name . " " . $data->last_name, $data->photo_url, $data->ticket_user, $data->user_type);

                    $data->last_replied_by = $this->get_user_data($data->last_replied_by_type, $data->last_replied_by, $user_list, $staff_list);

                    $data->assigned_on = $this->get_user_data("A", $data->assigned_on, [], $staff_list);
                    $data->cat_id = Mcategory::getCategoryStr($data->cat_id, "text-info text-bold");

                    if ($has_change_category_permission) {
                        if ($data->cat_id == "-") {
                            $data->cat_id = "<a data-effect='mfp-move-from-top' class='popupformWR btn btn-success btn-xs' href='" . admin_url("ticket/change-category/" . $data->id . "/Y") . "'>" . __("Set Category") . "</a>";
                        } else {
                            $data->cat_id = "<a data-effect='mfp-move-from-top' class='popupformWR' href='" . admin_url("ticket/change-category/" . $data->id) . "'>" . $data->cat_id . "</a>";
                        }
                    }
                    $data->opened_time = "<span class='tooltip2' data-tooltip-position='top' title='Opened on " . get_user_datetime_default_format($data->opened_time) . "' >" . app_time_elapsed_string($data->opened_time) . "</span>";
                    $data->last_reply_time = "<span class='tooltip2' data-tooltip-position='top' title='Last replied on " . get_user_datetime_default_format($data->last_reply_time) . "' >" . app_time_elapsed_string($data->last_reply_time) . "</span>";

                    if ($data->status != "C" && empty($data->assigned_on)) {
                        if (Mticket::hasTicketAssignPermission($data)) {
                            $data->assigned_on = "<a href='" . admin_url("ticket/set-assign/{$data->id}") . "' class='popupformWR btn btn-xs btn-success'><i class='fa fa-plus-circle'></i> " . __("Set Assign") . "</a>";
                            $data->assigned_on .= " <a href='" . admin_url("ticket-confirm/assign-me/{$data->id}") . "' data-msg='" . __("Are you sure to assign yourself?") . "' class='ConfirmAjaxWR btn btn-xs btn-success'><i class='fa fa-user-circle'></i> " . __("Assign Me") . "</a>";
                        }

                    }
                    $data->status = getTextByKey($data->status, $status_options);
                    $data->title = " <span class='grid-t-title'>" . $data->priroty . " " . $data->title . "</span><div class='grid-tkt-props'>" . GetGridProperitySpan("Category", $data->cat_id) . GetGridProperitySpan("Current Status", $data->status). GetGridProperitySpan("Replied", $data->reply_counter, "", "label label-default text-bold"). GetGridProperitySpan("Ticket user", "<a class='popupformWR' href='".admin_url("client/profile/{$bkUser}")."'>".$data->ticket_user."</a>")."</div>";
					if($is_show_reply) {
						if(empty($data->last_replied_by_type) || $data->last_replied_by_type=='U') {
							$data->last_replied_by = "<span class='nr-container'><span>".$data->last_replied_by."</span><span class='g-need-reply badge label-warning'>" . __("Need Reply") . "</span></span>";

						}
					}

                }
			}
			$this->SetGridData ( $result );
		}
		$this->DisplayGridResponse ();
	}
	private function set_ticket_data_title(&$data){
	    
	}
	private function get_user_data($user_type,$user_value,$user_list,$staff_list){
        if(($user_type=="S" && $user_value=="SYS")){
            return get_grid_user_img(__("SYSTEM"),base_url("images/logo.png"),$user_value,$user_type);
        }
		if(($user_type=="U" || $user_type=="G") && !empty($user_value)){
			if(isset($user_list[$user_value])){
				return get_grid_user_img($user_list[$user_value]->first_name." ".$user_list[$user_value]->last_name,$user_list[$user_value]->photo_url,$user_list[$user_value]->id,"U");
			}
		}elseif($user_type=="A" && !empty($user_value)){
			if(isset($staff_list[$user_value])){
			    $admin_url=Mapp_user::get_user_image_url($staff_list[$user_value]->id);
				return get_grid_user_img($staff_list[$user_value]->title,$admin_url,$staff_list[$user_value]->id,"A");
			}
		}else{
			return $user_value;
		}
	}
	function ticket_list() {
		if (! ACL::HasPermission ( "admin/ticket/index" )) {
			$this->DisplayGridPermissionDenied ();
			return;
		}
		$this->get_ticket_list ( 'P' );
	}
	function my_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/my-ticket" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'MY' );
	}
	function my_assigned_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/my-assigned-ticket" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'MA' );
	}
	function unassigned_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/unassigned-ticket" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'MU' );
	}
	function my_paid_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/my-paid-ticket" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'MP' );
	}
	function all_paid_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/all-paid-ticket" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'AP' );
	}
	function mc_ticket() {
	    if (! ACL::HasPermission ( "admin/ticket/my-closed" )) {
	        $this->DisplayGridPermissionDenied ();
	        return;
	    }
	    $this->get_ticket_list ( 'MC' );
	}

	function closed_ticket() {
		if (! ACL::HasPermission ( "admin/ticket/closed-ticket" )) {
			$this->DisplayGridPermissionDenied ();
			return;
		}
		$this->get_ticket_list ( 'C' );
	}
}
?>