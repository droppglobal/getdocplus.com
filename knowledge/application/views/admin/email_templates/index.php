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
$grid->AddGroupColumn("grp",false);
$grid->AddHiddenProperty("grp");
$grid->AddModelNonSearchable("Title", "title", 150 ,"left");
$grid->SetXSCombindeField("title");
$grid->AddModelNonSearchable("Subject", "subject", 250 ,"left");
$grid->AddModelNonSearchable("Status", "status", 80 ,"center");
if(ACL::HasPermission("admin/email-templates/edit")){
	$grid->AddModelNonSearchable("Action", "action", 50 ,"center");
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
