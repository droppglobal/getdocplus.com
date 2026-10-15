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
if(ACL::HasPermission("admin/page/add")){
	$grid->AddTitleRightHtml('<a  class="btn btn-xs btn-info" href="'.site_url("admin/page/add").'" ><i class="fa fa-plus"></i>'.__('Add New').'</a>');
}
$grid->AddModelNonSearchable("Title", "title", 150 ,"center");
$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("Added", "added_on", 50 ,"center");
$grid->AddModelNonSearchable("Status", "status", 50 ,"center");
	    
if(ACL::HasPermission("admin/page/edit")){
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
