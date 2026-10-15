<?php
$title="Open Ticket";
$icon="fa fa-ticket";
$class="";
?>

	<div class="card p-3  " style="margin-bottom: 150px;">
		<div class="card-body text-center">
			<?php
			$userdata = GetUserData();

			$isDisableGuestPopup = Mapp_setting::GetSettingsValue("dgustpopup", "N") == "Y";
			if (!empty($userdata) || $isDisableGuestPopup) {

				?>

				<a class=" btn btn-lg btn-success" href="<?php echo site_url('ticket/open'); ?>">

					<?php if (!empty($icon)) { ?><i class="<?php echo $icon; ?>"></i>  <?php }
					_e($title); ?>

				</a>

				<?php

			} else {
				?>

				<a data-effect="mfp-move-from-top"
				   class="btn btn-lg btn-success popupformWR <?php echo $class; ?> open-ticket"
				   href="<?php echo site_url('user/login-register'); ?>">

					<i class="fa fa-ticket"></i> <?php _e($title); ?>

				</a>

				<?php


			}
			?>
		</div>
	</div>








