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
if(ACL::HasPermission("admin/canned-msg/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/canned-msg/add").'" ><i class="fa fa-plus"></i>'.__('Add New').'</a>');
}

$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("Title", "title", 100 ,"center");
$grid->AddModelNonSearchable("Added By", "added_by", 100 ,"center");
$grid->AddModelNonSearchable("Status", "status", 100 ,"center");
	    
if(ACL::HasPermission("admin/canned-msg/edit")){
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
