<style type="text/css">
    .dashboard-messages  {
      height: 412px; 
    }
.no-chat-msg {
    text-align: center;
    font-size: 25px;
    color: #dedede;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php _e("License Information") ; ?></h3>    
                  
                </div>
                <!-- /.box-header -->
                <div class="box-body p-0">
        		  <?php if(!empty($licinfo)){
        		     
        		  ?>
        		  <table class="table">
        		      <tr>
        		      	<th width="120px"><?php _e("License Key") ; ?></th>
        		      	<th width="10px">:</th>
        		      	<td><?php echo  get_hidden_star_string($licinfo->license_key);?></td>
        		      </tr>
        		      
        		      <tr>
        		      	<th><?php _e("License Title") ; ?></th>
        		      	<th width="10px">:</th>
        		      	<td><?php echo $licinfo->license_title;?></td>
        		      </tr>
        		      <tr>
        		      	<th><?php _e("License Domain") ; ?></th>
        		      	<th width="10px">:</th>
        		      	<td><?php echo $licinfo->license_domain;?></td>
        		      </tr>
        		      <?php if(ACL::HasPermission("admin/license/remove")){?>
        		      <tr>
        		      
        		      	<td colspan="3"><?php _e("If you want to remove license form this site then click ") ; ?> <a href="<?php echo admin_url("license/remove")?>" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> <?php _e("Remove License") ; ?></a> </td>
        		      
        		      </tr>	<?php }?>
        		  </table>
        		  <?php }?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer no-padding">
                  
                </div>
                <!-- /.footer -->
         </div>
         <!-- /.box -->
    </div>
    </div> 
      
    
<div class="row"></div>