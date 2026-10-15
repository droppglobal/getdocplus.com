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
if(ACL::HasPermission("admin/app-permission/role-add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.admin_url("app-permission/role-add").'" ><i class="fa fa-plus"></i>Add New</a>');
}
$grid->AddModelNonSearchable("Title", "title", 100 ,"center");
$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("On Ticket Display As", "display_as", 100 ,"center");
$grid->AddModelNonSearchable("Has Super Power", "grade", 100 ,"center");
	    
if(ACL::HasPermission("admin/app-permission/role-edit")){
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
