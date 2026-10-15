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

$grid->AddModelNonSearchable("Ticket Title", "title", 80 ,"left");
$grid->SetXSCombindeField("title");

$grid->AddModelNonSearchable("Ticket Status", "status",50 ,"center");
$grid->AddModelNonSearchable("Assigned", "assigned_on",50 ,"center");
$grid->AddModelNonSearchable("Opened", "opened_time",70 ,"center");
$grid->AddModelNonSearchable("Last Replied", "last_reply_time",70 ,"center");
$grid->AddModelNonSearchable("Message", "f_msg", 120 ,"center");

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
