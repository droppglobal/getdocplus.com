<?php 
$grid=new jQGrid();
$grid->url =$grid_url;
$grid->width = "auto";
$grid->height = "auto";
$grid->rowNum = 20;
$grid->pager = "#pagerb";
$grid->container = ".grid-body";
$grid->ShowReloadButtonInTitle=true;
$grid->ShowDownloadButtonInTitle=true;

if(ACL::HasPermission("admin/admin-message/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/admin-message/add").'" ><i class="fa fa-plus"></i>'.__('Send Message').'</a>');
}

$grid->AddModelNonSearchable("To", "to_user", 100 ,"center");
$grid->SetXSCombindeField("to_user");

$grid->AddModelNonSearchable("Last Replied", "last_replied", 100 ,"center");
$grid->AddModelNonSearchable("Time", "entry_time", 100 ,"center");
$grid->AddModelNonSearchable("Subject", "subject", 100 ,"center");
if(ACL::HasPermission("admin/admin-message/edit")){
	$grid->AddModelNonSearchable("Action", "action", 100 ,"center");
}

?>
<div class="box box-primary">
     <div class="box-body grid-body">
     <?php $grid->show();?>
     </div><!-- /.box-body -->
</div>
<script type="text/javascript">
$(function(){
	AddOnCloseMethod(<?php echo $grid->ReloadMethod();?>);
});
   
</script>
