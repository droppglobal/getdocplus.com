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

if(ACL::HasPermission("admin/ticket-payment/add")){
}

$grid->AddModelNonSearchable("Payment Description", "payment_des", 180 ,"left");
$grid->AddModelNonSearchable("Amount", "amount", 60 ,"center");
$grid->SetXSCombindeField("amount");
$grid->AddModelNonSearchable("Created By", "created_by", 80 ,"center");
$grid->AddModelNonSearchable("Method", "payment_method", 60 ,"center");
$grid->AddSortableModel("Create Date", "create_date", 115 ,"center");
$grid->AddModelNonSearchable("Process Date", "process_date", 115 ,"center");
$status=[];
$grid->AddModelCustomSearchable("Status", "status", 80 ,"center","select",["*"=>"All","P"=>"Pending","A"=>"Paid","F"=>"Failed"]);
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
