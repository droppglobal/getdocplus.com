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
if(ACL::HasPermission("admin/faq-list/add")){
	$grid->AddTitleRightHtml('<a data-effect="mfp-move-from-top" class="popupformWR btn btn-xs btn-info" href="'.site_url("admin/faq-list/add").'" ><i class="fa fa-plus"></i>'.__('Add New').'</a>');
}

$catgs=[0=>"All"]+Mfaq_category::FetchAllKeyValue("id", "name");
$grid->AddModelCustomSearchable("Category", "cat_id", 80 ,"center","select",$catgs);
$grid->SetXSCombindeField("cat_id");
$grid->AddModelNonSearchable("Question", "question", 100 ,"center");
$grid->AddModelNonSearchable("Entry Date", "entry_date", 100 ,"center");
$grid->AddModelNonSearchable("Status", "status", 80 ,"center");
	    
if(ACL::HasPermission("admin/faq-list/edit")){
	$grid->AddModelNonSearchable("Action", "action", 80 ,"center");
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
