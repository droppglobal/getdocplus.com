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

$grid->AddModelCustomSearchable("User Id", "user_id", 100 ,"center","select",Mapp_user::FetchAllKeyValue("id", "user",true));
$grid->SetXSCombindeField("user_id");
$grid->AddModelNonSearchable("Miss Login Tried", "total", 100 ,"center");
	    
if(ACL::HasPermission("admin/locked-user/edit")){
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
