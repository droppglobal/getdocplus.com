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
if(ACL::HasPermission("admin/category/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/category/add").'" ><i class="fa fa-plus"></i>Add New</a>');
}

$grid->AddModelNonSearchable("Title", "title",100 ,"left");
$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("Parent Category", "parent_category", 120 ,"left");
$grid->AddModelNonSearchable("Show On", "show_on", 100 ,"center");
$grid->AddModelNonSearchable("Status", "status", 50 ,"center");
	    
if(ACL::HasPermission("admin/category/edit")){
	$grid->AddModelNonSearchable("Action", "action",50 ,"center");
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
