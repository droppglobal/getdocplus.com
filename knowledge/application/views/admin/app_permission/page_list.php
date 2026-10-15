<?php
$grid=new jQGrid();
$grid->url =$grid_url;
$grid->width = "auto";

$grid->height = 340;
$grid->rowNum = 20;
$grid->pager = "#pagerb";
$grid->container = ".grid-body";
$grid->ShowReloadButtonInTitle=true;
$grid->ShowDownloadButtonInTitle=true;

$grid->AddSearchProperty("Product", "pid","select",$products);
$grid->AddModelNonSearchable("Product", "cprodtitle", 80 ,"center");
$grid->AddModelNonSearchable("Name", "ccustname", 80 ,"center");
$grid->AddModelNonSearchable("Country", "ccustcc", 80 ,"center");
$grid->AddModel("Email", "ccustemail", 120 ,"center");
$grid->AddModelNonSearchable("Amount", "ctransamount", 80 ,"center");
$grid->AddModelCustomSearchable("Trn. Type", "ctransaction", 120 ,"center","select",array("*"=>"All","SALE"=>"SALE","RFND"=>"RFND"));

if(function_exists("add_css")){
	
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