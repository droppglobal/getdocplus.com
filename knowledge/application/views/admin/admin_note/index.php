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
if(ACL::HasPermission("admin/admin-note/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/admin-note/add").'" ><i class="fa fa-plus"></i>'.__('Add New').'</a>');
}

$grid->AddModelNonSearchable("Ref Id", "ref_id", 100 ,"center");
$grid->SetXSCombindeField("ref_id");
$grid->AddModelNonSearchable("Ref Type", "ref_type", 100 ,"center");
$grid->AddModelNonSearchable("User Id", "user_id", 100 ,"center");
$grid->AddModelNonSearchable("Note", "note", 100 ,"center");
$grid->AddModelNonSearchable("Entry Date", "entry_date", 100 ,"center");
	    
if(ACL::HasPermission("admin/admin-note/edit")){
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
