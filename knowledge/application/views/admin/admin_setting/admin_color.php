<form id="app-ad-color-form" method="post" action="<?php echo admin_url("admin-setting-confirm/modify/ao");?>" data-beforesend="on_beforesend" data-on-complete="on_complete" class="form app-ajax-form form-horizontal">
	        <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php _e("Admin Theme Color");?></h3>

                </div>
                <!-- /.box-header -->
                <div class="box-body text-center pb-3">
                    <?php
                    if(file_exists(VIEWPATH."override/themes/default/main.php")){?>
                        <div class="alert alert-warning" role="warning">
                            <strong><?php _e('Override') ?> :</strong> <?php _e("You have override default theme main.php file, may be color change it not effects") ; ?>
                        </div>
                <?php } ?>
                    <div class="form-group m-t-20">
                        <div class="col-md-12">
                            <div class="">
				                <?php
				                $app_chat_pattern_selected= $mainobj->GetPostValue("admin_color","default");
				                $app_chatpattern_op=
                                    array(
                                            "_none"=>'<span class="full-width-flex flex-center border-radius-5px">'.__("None").' </span>',
                                            "default"=>'<span class="full-width-flex bg-theme-default border-radius-5px"> </span>',
                                            "purple"=>'<span class="full-width-flex bg-theme-purple border-radius-5px"> </span>',
                                            "blue"=>'<span class="full-width-flex bg-theme-blue border-radius-5px"> </span>',
                                            "cyan"=>'<span class="full-width-flex bg-theme-cyan border-radius-5px"> </span>',
                                            "green"=>'<span class="full-width-flex bg-theme-green border-radius-5px"> </span>',
                                            "orange"=>'<span class="full-width-flex bg-theme-orange border-radius-5px"> </span>',
                                            "pink"=>'<span class="full-width-flex bg-theme-pink border-radius-5px"> </span>',
                                            "red"=>'<span class="full-width-flex bg-theme-red border-radius-5px"> </span>',
                                            "gray"=>'<span class="full-width-flex bg-theme-gray border-radius-5px"> </span>',
                                            "black"=>'<span class="full-width-flex bg-theme-black border-radius-5px"> </span>',

				                );
				                GetHTMLRadioBoxByArray("Choose HTML Editor","config[admin_color]","admin_color",true,$app_chatpattern_op,$app_chat_pattern_selected,false,"transparent");
				                ?>
                            </div>
                            <span class="form-group-help-block"><?php _e("It will display in chat window");?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-12  text-center p-10" for="admin_color_type"><?php _e("Type"); ?></label>
                        <div class="col-md-12">
                            <div class="">
				                <?php
				                $admin_color_type= $mainobj->GetPostValue("admin_color_type","D");
				                $admin_color_type_op=array("D"=>'<img src="'.base_url('images/dark-admin.png').'"/><div>'.__('Dark').'</div>',
                                                           "L"=>'<img src="'.base_url('images/light-admin.png').'"/><div>'.__('Light').'</div>',
				                );
				                GetHTMLRadioBoxByArray("Choose HTML Editor","config[admin_color_type]","admin_color_type",true,$admin_color_type_op,$admin_color_type,false,"#fff",'app-admin-theme-type',"");
				                ?>
                            </div>
                            <span class="form-group-help-block"><?php _e("It will display in chat window");?></span>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-right">
                  <button id="color-submit-btn" type="submit" class="btn btn-sm btn-success" ><i class="fa fa-save"></i> <?php _e("Save");?></button>
                </div>
                <!-- /.footer -->
         </div>
         <!-- /.box -->
         </form>