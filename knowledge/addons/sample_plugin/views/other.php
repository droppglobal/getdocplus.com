<div class="row">
    <div class="col-md-12">
		<?php  echo form_open ( current_url(),array("class"=>"form  form-horizontal","id"=>"app_basic_form","method"=>"post"));?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php _e("Other Form");?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3 label-required" for="test_name"><?php $this->_e("Test Input") ; ?></label>
                            <div class="col-md-9">
                        			<input type="text"
                                              class="form-control" id="test_name" name="test_name"
                                              placeholder="<?php $this->_e("Test Input"); ?>" data-bv-notempty="true"
                                              data-bv-notempty-message="<?php  $this->_e(" %s is required","Payment currency");?>"
                                    />
                                <span class="form-group-help-block  "><?php $this->_e("your help text");?></span>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-left">
                <button type="submit" class="btn btn-sm btn-success" ><i class="fa fa-save"></i> Save</button>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.box -->
		<?php echo form_close();?>
    </div>
</div> 