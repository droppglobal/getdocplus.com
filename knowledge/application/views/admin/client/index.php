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
if(ACL::HasPermission("admin/client/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/client/add").'" ><i class="fa fa-plus"></i>'.__('Add New').'</a>');
}

$grid->AddSearchProperty("Name","full_name");
$grid->AddModelNonSearchable("First Name", "first_name", 120 ,"left");
$grid->SetXSCombindeField("first_name");
$grid->AddModelNonSearchable("Last Name", "last_name", 120 ,"lrft");
$grid->AddModel("Email", "email", 120 ,"center");
$grid->AddModelNonSearchable("Verified", "is_verified_email", 50 ,"center");
$grid->AddModelNonSearchable("Gender", "gender", 60 ,"center");
$grid->AddModelNonSearchable("Country", "country", 60 ,"center");
$grid->AddModelNonSearchable("Join Date", "join_date", 80 ,"center");
$customes=Mcustom_field::getGridColumn("R");
get_grid_custom_column($grid,$customes);
$grid->AddModelNonSearchable("Status", "status", 80 ,"center");
if(ACL::HasPermission("admin/client/edit")){
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
