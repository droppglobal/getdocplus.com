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
if(ACL::HasPermission("admin/knowledge/add")){
	$grid->AddTitleRightHtml('<a class="btn btn-xs btn-info" href="'.site_url("admin/knowledge/add").'" ><i class="fa fa-plus"></i>Add New</a>');
}
$options_category=["0"=>"All Category"];
$mainobj=new Mknowledge();
$opts=Mcategory::getKnowledgeCategoryListHtmlOptionArray('A');
foreach ($opts as $key=>$vl){
    $options_category[$key]=$vl;
}
$status=array_merge(["*"=>"All"],$mainobj->GetPropertyOptions("status"));


$grid->AddSortableModel("Title", "title", 150 ,"left");
$grid->SetXSCombindeField("title");
$grid->AddModelCustomSearchable("Category", "cat_id", 150 ,"left","select",$options_category);
$grid->AddSortableModel("Sticky/Pinned", "is_stickey", 50 ,"center");
$grid->AddModelCustomSearchable("Status", "status", 50 ,"center","select",$status);
	    
if(ACL::HasPermission("admin/knowledge/edit")){
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
