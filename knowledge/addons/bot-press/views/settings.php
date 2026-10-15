<?php
/** @var string $pri_key */
/** @var array $product_cats */
?>
<div class="row">
    <div class="col-md-12">
		<?php  echo form_open ( current_url(),array("class"=>"form   bv-form","id"=>"app_basic_form","method"=>"post"));?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php _e("Settings Form");?></h3>
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
                        <label class="control-label " ><?php $this->_e("Request URL") ; ?></label>
                         <input readonly  class="form-control p-15" value="<?php echo site_url('site/action/bp-create-ticket'); ?>">


                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label label-required" for="pri_key"><?php $this->_e("Private Key") ; ?></label>
                            <div class="">
                                <input type="text"
                                       class="form-control" id="pri_key" name="pri_key"
                                       placeholder="<?php $this->_e("Private Key Input"); ?>" data-bv-notempty="true"
                                       value="<?php
                                       echo PostValue('pri_key',$pri_key); ?>"
                                       data-bv-notempty-message="<?php  $this->_e(" %s is required","Private Key");?>"
                                />
                                <span class="form-group-help-block  "><?php $this->_e("This key should be added into the request");?></span>
                            </div>

                        </div>

                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header">
                                Product Categories
                            </div>
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="col-md-6 p-l-0">
                                        <label class="control-label" ><?php $this->_e("Product Name") ; ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label" ><?php $this->_e("Private Key") ; ?></label>
                                    </div>
                                </div>
					            <?php foreach ($product_cats as $ind=>$product_cat) {?>

                                    <div class="col-md-12 m-b-5 p-l-0">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <input type="hidden" readonly
                                                     name="product[<?php echo $ind; ?>][name]"
                                                       placeholder="<?php $this->_e("Product Name"); ?>" data-bv-notempty="true"
                                                       value="<?php echo $product_cat->name; ?>"
                                                />
                                                <input type="text" disabled
                                                       class="form-control" id="pro_cat_<?php echo $ind; ?>" name="product[<?php echo $ind; ?>][name]"
                                                       placeholder="<?php $this->_e("Product Name"); ?>" data-bv-notempty="true"
                                                       value="<?php echo $product_cat->title; ?>"
                                                />
                                            </div>
                                            <div class="col-md-8">

									            <?php
									            $options_category=Mcategory::getCategoryListHtmlOptionArrayOnlyTicket('A');
									            ?>
                                                <select class="form-control" id="cat_id_<?php echo $ind ?>"  name="product[<?php echo $ind; ?>][cat_id]"       data-bv-notempty="true" 	data-bv-notempty-message="<?php  _e("%s is required",__("Category"));?>">
										            <?php
										            GetHTMLOption("", __("Select"));
										            GetHTMLOptionByArray($options_category,$product_cat->cat_id);
										            ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
					            <?php } ?>
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