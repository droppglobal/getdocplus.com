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
$grid->afterInsertRow="AfterInsertRow";


$grid->AddHiddenProperty("main_status","main_status");
$grid->AddModelNonSearchable("Time", "entry_time", 60 ,"center","",true);
$grid->AddModelNonSearchable("Title", "title", 100 ,"left");
$grid->SetXSCombindeField("entry_time");
$grid->AddModelNonSearchable("Message", "msg", 250 ,"left");
$grid->AddModelNonSearchable("View", "action", 60 ,"center");
if(ACL::HasPermission("admin/notification/seen-all")){
    $grid->AddTitleRightHtml('<a  class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/notification/seen-all").'" ><i class="fa fa-eye"></i>'.__('Mark as seen all').'</a>');
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
function AfterInsertRow(rowid, rowData, rowelem) {
    if(rowData.main_status=="A"){
        $('tr#' + rowid).addClass('tr-bg-green');
    }
}
</script>
