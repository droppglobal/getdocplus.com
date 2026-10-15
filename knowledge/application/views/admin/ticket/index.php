<?php
/**
 * Its property status
 *
 * @var APP_Controller $this
 */
$cookie_post_fix=get8BitHashCode($grid_url);

$grid=new jQGrid();
$grid->url =$grid_url;
$grid->width = "auto";
$grid->height = "auto";
$grid->rowNum = 20;
$grid->pager = "#pagerb";
$grid->container = ".grid-body";
$grid->ShowReloadButtonInTitle=true;
$grid->ShowDownloadButtonInTitle=true;
$grid->loadComplete="data_on_complete";
$need_cookie_name=!empty($is_nr)?$is_nr:'';
$cookie_name=Mapp_setting::get_cookie_prefix()."_".$cookie_post_fix;
$need_reply_cookie_name=Mapp_setting::get_cookie_prefix()."_nr_".$need_cookie_name;
$is_checked=false;
$is_need_reply=false;
if(isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]=="Y"){
    $is_checked=true;
}
if(isset($_COOKIE[$need_reply_cookie_name]) && $_COOKIE[$need_reply_cookie_name]=="Y"){
	$is_need_reply=true;
}
if(!empty($is_nr)) {
	$grid->AddTitleRightHtml( '<div class="form-group form-group-sm"><label class="control-label" for="is_need_reply">' . __( 'Need Reply ' ) . '</label><div class="togglebutton "><label><input ' . ( $is_need_reply ? ' checked="checked"' : "" ) . '  type="checkbox"  value="Y" class="" id="is_need_reply"  name="is_need_reply" ></label></div></div>' );
}
$users=Mapp_user::FetchAllKeyValue("id","title",true);
$grid->AddTitleRightHtml('<div class="form-group form-group-sm"><label class="control-label" for="is_auto_refresh">'.__('Enable Auto Reload (every %d min) ',1).'</label><div class="togglebutton "><label><input '.($is_checked?' checked="checked"':"").'  type="checkbox"  value="Y" class="" id="is_auto_refresh"  name="is_auto_refresh" ></label></div></div>');
$grid->AddSearchProperty("Track ID", "ticket_track_id");
$grid->AddSearchProperty("Client Email", "client_email");
$grid->AddSearchProperty("Client Name", "client_name");

$grid->AddModel("Title", "title", 260 ,"left");
$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("Open Time", "opened_time", 60 ,"center","",true);
$grid->AddModelCustomSearchable("Assigned", "assigned_on", 100 ,"left",'select',$users);
$grid->AddModelNonSearchable("Last Reply", "last_replied_by", 100 ,"left");
$grid->AddModelNonSearchable("L.Reply Time", "last_reply_time", 60 ,"center","",true);
$grid->isColumnChoseable=true;
$customes=Mcustom_field::getGridColumn();
get_grid_custom_column($grid,$customes);
if(ACL::HasPermission("admin/ticket/details")){
	$grid->AddModelNonSearchable("Action", "action", 80 ,"center");
}
$categories=["0"=>"All Category"]+Mcategory::getCategoryListHtmlOptionArrayOnlyTicket('A');
$ticket=new Mticket();
$prTicket=array_merge(["*"=>"All Priorities"],$ticket->GetPropertyOptions("priroty"));
$grid->AddSearchProperty("Category", "cat_id","select",$categories);
$grid->AddSearchProperty("Priority", "priroty","select",$prTicket);
$grid->AddSearchProperty("In Ticket Body", "in_ticket_body");

?>
<style>
.gs-jq-grid .ui-jqgrid .loading {
  top: 20% !important;
}
.nr-container{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.nr-container .g-need-reply{
    font
}
</style>
<div class="box box-primary">
     <div class="box-body grid-body">
     <?php $grid->show();?>
     </div><!-- /.box-body -->
</div>
<script type="text/javascript">
var reload_interval=null;
function data_on_complete(rdata){
	setToolTipNalert();
	$("[aria-describedby$=cat_id]").removeAttr("title");
}
function Reload_setting(){
	var isChecked=$("#is_auto_refresh").is(":checked");
	if(isChecked){
        setCookie(appGlobalLang.base_cookie_name+"_<?php echo $cookie_post_fix;?>", "Y", 15,"/");
    	if(!reload_interval){
    		reload_interval=setInterval(
    	    function(){
    		<?php echo $grid->ReloadMethod();?>();
    		},60000);
    	}
	}else{
		try{
			deleteCookie(appGlobalLang.base_cookie_name+"_<?php echo $cookie_post_fix;?>");
			clearInterval(reload_interval);
			reload_interval=null;
		}catch(e){}
	}
}
function toggle_need_reply(){
    var isChecked=$("#is_need_reply").is(":checked");
    if(isChecked){
        setCookie("<?php echo $need_reply_cookie_name;?>", "Y", 30,"/");
	    <?php echo $grid->ReloadMethod();?>();
    }else{
        try{
            deleteCookie("<?php echo $need_reply_cookie_name;?>");
	        <?php echo $grid->ReloadMethod();?>();
        }catch(e){}
    }
}
/*window.onbeforeunload = function(e) {
    return 'Are you sure you want to leave this page?  You will lose any unsaved data.';
 };*/
$(function(){
	AddOnPageCloseMethod(function(e){
		console.log("Called");
	});
	AddOnCloseMethod(<?php echo $grid->ReloadMethod();?>);
	AddOnShowNotificationMethod(<?php echo $grid->ReloadMethod();?>);
	Reload_setting();
	$("#is_auto_refresh").on("change",function(){
		Reload_setting();
			  
	});
	$("#is_need_reply").on("change",function(){
        toggle_need_reply();

    });
	
});
   
</script>
