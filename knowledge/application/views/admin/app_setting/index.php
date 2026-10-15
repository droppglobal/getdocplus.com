<?php 
$grid=new jQGrid();
$grid->url =$grid_url;
$grid->width = "auto";
$grid->height = "auto";
$grid->rowNum = 100;
$grid->pager = "#pagerb";
$grid->container = ".grid-body";
$grid->ShowReloadButtonInTitle=true;
$grid->ShowDownloadButtonInTitle=true;
if(ACL::HasPermission("admin/app-setting/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.admin_url("app-setting/add").'" ><i class="fa fa-plus"></i>Add New</a>');
}

$grid->AddModelNonSearchable("Settings Name", "s_title", 100 ,"center");
$grid->AddModelNonSearchable("Settings Value", "s_val", 200 ,"center");
if(Mapp_user::IsSuperUser()){
    $grid->AddModelNonSearchable("Auto Load", "s_auto_load", 100 ,"center");
}
	    
if(ACL::HasPermission("app-setting/edit")){
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
