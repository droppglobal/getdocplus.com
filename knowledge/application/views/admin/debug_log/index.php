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



$grid->SetXSCombindeField("entry_type");
$grid->AddModelNonSearchable("Log Type", "log_type", 50 ,"center");
$grid->AddModelNonSearchable("Title", "title", 150 ,"center");
$grid->AddModelNonSearchable("Status", "status", 50 ,"center");
$grid->AddModelNonSearchable("Entry Type", "entry_type", 80 ,"center");
$grid->AddModelNonSearchable("Entry Time", "entry_time", 80 ,"center");
if(ACL::HasPermission("admin/debug-log-confirm/clean-data")){
    $grid->AddTitleRightHtml('<a  data-msg="Are you sure?"  class=" ConfirmAjaxWR btn btn btn-xs btn-danger" href="'.site_url("admin/debug-log-confirm/clean-data").'" ><i class="fa fa-trash"></i>'.__('Clear Log').'</a>');
}
if(ACL::HasPermission("admin/debug-log/view-dtls")){
	$grid->AddModelNonSearchable("Action", "action",80 ,"center");
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
