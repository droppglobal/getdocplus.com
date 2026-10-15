<?php
$grid=new jQGrid();
$grid->url =$grid_url;
$grid->width = "auto";
$grid->height = 340;
$grid->rowNum = 20;
$grid->pager = "#pagerb";
$grid->container = ".grid-body";
$grid->ShowReloadButtonInTitle=true;
$grid->ShowDownloadButtonInTitle=true;
if(ACL::HasPermission("admin/app-permission/add-edit-appuser")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/app-permission/add-edit-appuser").'" ><i class="fa fa-plus"></i>Add New</a>');
}
$grid->AddModelNonSearchable("User", "user", 80 ,"center");
$grid->SetXSCombindeField("user");
$grid->AddModelNonSearchable("Title", "title", 100 ,"center");
$grid->AddModelNonSearchable("Email", "email", 120 ,"center");
$grid->AddModelNonSearchable("Role", "role", 80 ,"center");
$grid->AddModelNonSearchable("Status", "status", 70 ,"center");
if(ACL::HasPermission("admin/app-permission/add-edit-appuser")){
	$grid->AddModelNonSearchable("Action", "action", 80 ,"center");
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