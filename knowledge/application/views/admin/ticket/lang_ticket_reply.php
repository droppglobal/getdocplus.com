<?php
/**
 * @var Mticket $ticketObj ;
 * @var Mticket_reply $reply_object ;
 * @var int $ticket_user_id ;
 */
$reply_id = 0;
if ( ! empty( $reply_object ) ) {
	$reply_id = $reply_object->reply_id;
}
$is_ai_enabled=Mapp_settings_api_advance::GetSettingsValue( 'ai_studio', 'ai_is_active' ,'N')=='Y' || Mapp_settings_api_advance::GetSettingsValue( 'ai_studio', 'gpt_is_active' ,'N')=='Y';
if(!empty($ticketObj)){
?>

<div class="ticket-reply-lang m-t-10 m-b-0">
    <div class="panel-group m-b-0" id="tr_accordion_<?php echo $reply_id; ?>" role="tablist" aria-multiselectable="true">
		<?php
		if ( $is_ai_enabled ) {
			$client_lang = Mapp_setting::GetSettingsValue( 'app_clang', '' );
			if ( empty( $client_lang ) ) {
				$client_lang = Mapp_setting::GetSettingsValue( 'app_lang', '' );
			}
			$ai_languages = Mapp_settings_api_advance::GetSettingsValue( "ai_studio", 'ai_langs', '' );
			if ( ! empty( $ai_languages ) ) {
				$ai_languages = explode( ',', $ai_languages );
				foreach ( $ai_languages as $ai_lang ) {
					

					$app_language = app_get_language_details( $ai_lang );
					if ( $app_language ) {
						?>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse"
                                       data-parent="#tr_accordion_<?php echo $reply_id; ?>"
                                       href="#tr_<?php echo $reply_id; ?>_<?php echo $app_language->iso_code; ?>"
                                       aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fa fa-language m-r-10"></i> <?php echo $app_language->title; ?>

                                    </a>
                                </h4>
                            </div>
                            <div id="tr_<?php echo $reply_id; ?>_<?php echo $app_language->iso_code; ?>"
                                 class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div
                                     class="panel-body">
                                    <div id="tr_<?php echo $reply_id; ?>_<?php echo $app_language->iso_code; ?>_data" class="p-15">
									<?php
									$meta_key = 'tr_' . $app_language->iso_code;
                                    if(empty($reply_id)){
	                                    $tr_data  = $ticketObj->get_ticket_meta($meta_key );
                                    }else{
	                                    $tr_data  = $ticketObj->get_reply_meta( $reply_id, $meta_key );
                                    }

									if ( ! empty( $tr_data ) ) {
                                        ?>

	                                        <?php
	                                        echo $tr_data;
	                                        ?>

                                        <?php
									} else {
										?>
                                        <div class="text-center">
                                            <p><?php _e( "No translation processed yet, press the button below to translate in %s",
													$app_language->title ); ?></p>
                                            <button
                                                    data-target="#tr_<?php echo $reply_id; ?>_<?php echo $app_language->iso_code; ?>_data"
                                                    data-reply-id="<?php echo $reply_id; ?>"
                                                    data-lang="<?php echo $app_language->iso_code ?>"
                                                    class="translate_old_reply btn btn-xs btn-secondary m-b-5 m-r-5"
                                                    type="button">
                                                <i class="fa fa-language"></i>
												<?php _e( "Translate to %s", $app_language->title ); ?>
                                            </button>
                                        </div>
									<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>


					<?php }
				}
			}
		}
		?>
    </div>
</div>
<?php   }




