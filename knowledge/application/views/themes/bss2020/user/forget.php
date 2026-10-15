<?php
if(empty($mainobj)){
	$mainobj=new Msite_user();
	AddError("Main object has not initialized in controller");
}
?>

<div class="register-form col">

        <div class="row-wrap p-b-30">
            <div class="form-group">
                <label><?php _e("E-mail"); ?></label>
                <input type="text" maxlength="100"
                       data-bv-remote-url="<?php echo site_url("user/email-check");?>"
                       value="<?php echo  $mainobj->GetPostValue("email");?>" class="form-control" id="email"  name="email" placeholder="<?php _e("Email"); ?>" data-bv-notempty="true" 	data-bv-notempty-message="<?php  _e("%s is required",__("Email"));?>">

            </div>

        </div>

    <div class="btn-group-sm popup-footer p-3">
        <div class="d-flex justify-content-between align-items-center">

                <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i> <?php _e("Submit")?></button>


                <a data-effect="mfp-move-from-top"	href="<?php echo site_url("user/login")?>"	class="popupform  pull-right text-decoration-none">
                    <?php _e("Go to Login");?> <i class="fa fa-angle-double-right"></i>
                </a>

        </div>
    </div>
</div>


