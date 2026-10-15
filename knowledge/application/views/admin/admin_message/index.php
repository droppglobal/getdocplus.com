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



$grid->AddModelNonSearchable("From", "from_user", 100 ,"center");
$grid->SetXSCombindeField("from_user");
$grid->AddModelNonSearchable("Last Replied", "last_replied", 100 ,"center");
$grid->AddModelNonSearchable("Time", "entry_time", 100 ,"center");
$grid->AddModelNonSearchable("Subject", "subject", 100 ,"center");
$grid->AddModelNonSearchable("Action", "action", 100 ,"center");

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
