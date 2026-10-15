<?php
$ai_studio = new Mapp_settings_api_advance();
$ai_studio->SetAPIName( "ai_studio" );
$app_ai_list        = [
	
	"C"   => 'ChatGPT',
	"G" => 'Gimini',
];

?>
<form id="app-fbc-form" method="post" action="<?php echo admin_url( "admin-setting-confirm/ai-studio" ); ?>"
      data-beforesend="on_beforesend" enctype="multipart/form-data" data-on-complete="on_complete" data-multipart="true"
      class="form app-ajax-form form-horizontal">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="ap ap-chat3 a-c"
                                     style="font-size: 21px;vertical-align: -4px;"></i> <?php _e( "Ai Settings" ); ?>
            </h3>
            <div class="box-tools pull-right">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> <?php _e( "Save" ); ?>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-6 ">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <div class="form-group">
                                <label class="control-label col-md-4 label-required"
                                       for="wc_is_active"><?php _e( "Enable Gimini" ); ?></label>
                                <div class="col-md-8">
                                    <div class="togglebutton ">
                                        <input name="config[ai_is_active]" value="N" type="hidden">
                                        <label>
                                            <input type="checkbox" <?php echo $ai_studio->GetPostValue( "ai_is_active","N" ) == "Y" ? ' checked="checked"' : ''; ?> value="Y" class="has_depend_fld"
                                                   id="ai_is_active" name="config[ai_is_active]">
                                        </label>
                                        <span class="form-group-help-block"><?php _e( "To Enable Google AI Studio (Gimini)" ); ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="box-body fld-config-ai-is-active fld-config-ai-is-active-y">
                            <div class="form-group">
                                <label class="control-label col-md-4 label-required text-right p-10"
                                       for="g_ai_model"><?php _e( "Model" ); ?></label>
                                <div class="col-md-8">
                                    <div class="">
						                <?php
						                $app_ai_model_selected = $ai_studio->GetPostValue( "ai_model",
							                "gemini-1.5-flash" );
						                $app_ai_models        = [
							                "gemini-1.5-flash"   => 'Gemini 1.5 flash',
							                "gemini-1.5-pro"   => 'Gemini 1.5 Pro',
							                "gemini-pro" => 'Gimini 1.0 Pro',
						                ];
						                GetHTMLRadioBoxByArray( "Choose chat button icon", "config[ai_model]",
							                "g_ai_model", true, $app_ai_models,
							                $app_ai_model_selected, false,'#ffffff; width:unset !important; height:unset !important; padding:5px 25px','' );
						                ?>
                                    </div>
                                    <span class="form-group-help-block"> <a href="https://ai.google.dev/pricing"><?php _e( "See AI Studio Price Plan" ); ?></a></span>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="control-label col-md-4 label-required"
                                       for="fb_ai_studio_api"><?php _e( "API Key" ); ?></label>
                                <div class="col-md-8">
                                    <input type="text" maxlength="255"
                                           value="<?php echo $ai_studio->GetPostValue( "ai_api_key" ) ?>"
                                           class="form-control" id="fb_ai_studio_api" name="config[ai_api_key]"
                                           placeholder="<?php _e( "AI Studio API KEY" ); ?>" data-bv-notempty="true"
                                           data-bv-notempty-message="<?php _e( "%s is required",
						                       __( "AI Studio API KEY" ) ); ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <div class="form-group">
                                <label class="control-label col-md-4 label-required"
                                       for="wc_is_active"><?php _e( "Enable ChatGPT" ); ?></label>
                                <div class="col-md-8">
                                    <div class="togglebutton ">
                                        <input name="config[gpt_is_active]" value="N" type="hidden">
                                        <label>
                                            <input type="checkbox" <?php echo $ai_studio->GetPostValue( "gpt_is_active","N" ) == "Y" ? ' checked="checked"' : ''; ?> value="Y" class="has_depend_fld"
                                                   id="gpt_is_active" name="config[gpt_is_active]">
                                        </label>
                                        <span class="form-group-help-block"><?php _e( "To AI Studio" ); ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="box-body fld-config-gpt-is-active fld-config-gpt-is-active-y">

                            <div class="form-group">
                                <label class="control-label col-md-4 label-required text-right p-10"
                                       for="g_ai_model"><?php _e( "Model" ); ?></label>
                                <div class="col-md-8">
                                    <div class="">
						                <?php
						                $app_gpt_model_selected = $ai_studio->GetPostValue( "gpt_ai_model",
							                "gpt-4o-mini" );
						                $app_ai_models        = [
							                
							                "gpt-4o-mini"   => 'GPT 4o Mini',
							                "gpt-4o" => 'GPT 4o',
						                ];
						                GetHTMLRadioBoxByArray( "Choose chat button icon", "config[gpt_ai_model]",
							                "g_ai_model", true, $app_ai_models,
							                $app_gpt_model_selected, false,'#ffffff; width:unset !important; height:unset !important; padding:5px 25px','' );
						                ?>
                                    </div>
                                    <span class="form-group-help-block"> <a href="https://ai.google.dev/pricing"><?php _e( "See AI Studio Price Plan" ); ?></a></span>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="control-label col-md-4 label-required"
                                       for="fb_ai_studio_api"><?php _e( "API Key" ); ?></label>
                                <div class="col-md-8">
                                    <textarea type="text"
                                           class="form-control" id="fb_ai_studio_api" name="config[gpt_api_key]"
                                           placeholder="<?php _e( "ChatGPT API KEY" ); ?>" data-bv-notempty="true"
                                           data-bv-notempty-message="<?php _e( "%s is required",
                                               __( "ChatGPT API KEY" ) ); ?>"><?php echo $ai_studio->GetPostValue( "gpt_api_key" ) ?></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 md-p-l-0">

                    <div class="box box-primary">
                        <div class="box-header with-border">

                                <label class="control-label label-required"
                                       for="wc_is_active"><?php _e( "AI Configuration" ); ?></label>


                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <label class="control-label col-md-5 label-required" for="ai_langs"><?php _e("Select Translation Languages"); ?></label>
                                <div class="col-md-7">

                                    <select multiple="multiple" id="ai_langs" name="config[ai_langs][]" class="form-control form-control-sm select2">
				                        <?php
				                        $selected_lang= $ai_studio->GetPostValue( "ai_langs" ,'');
				                        if(empty($selected_lang)){
					                        $selected_lang=[];
				                        }else {
					                        $selected_lang = explode( ',', $selected_lang );
				                        }
				                        $languageList=app_get_languages();
				                        foreach ($languageList as $language){
					                        ?>
                                            <option <?php echo in_array($language->iso_code,$selected_lang)?' selected ':'' ?> value="<?php echo $language->iso_code; ?>"><?php echo $language->title; ?></option>
					                        <?php
				                        }
				                        ?>
                                    </select>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-5 label-required" for="ai_rewriter"><?php _e("Select AI for Rewrite"); ?></label>
                                <div class="col-md-7">


                                        <div class="">
			                                <?php
			                                $app_gpt_model_selected = $ai_studio->GetPostValue( "ai_rewriter",
				                                "C" );

			                                GetHTMLRadioBoxByArray( "Choose chat button icon", "config[ai_rewriter]",
				                                "ai_rewriter", true, $app_ai_list,
				                                $app_gpt_model_selected, false,'#ffffff; width:unset !important; height:unset !important; padding:5px 25px','' );
			                                ?>
                                        </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-5 label-required" for="ai_translator"><?php _e("Select AI for Translate"); ?></label>
                                <div class="col-md-7">
                                    <div class="">
				                        <?php
				                        $app_gpt_model_selected = $ai_studio->GetPostValue( "ai_translator",
					                        "G" );

				                        GetHTMLRadioBoxByArray( "Choose chat button icon", "config[ai_translator]",
					                        "ai_translator", true, $app_ai_list,
					                        $app_gpt_model_selected, false,'#ffffff; width:unset !important; height:unset !important; padding:5px 25px','' );
				                        ?>
                                    </div>
                                    <span class="form-text text-warning"><?php _e("N.B: The AI API must be enabled according to your selection.") ; ?></span>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>



        </div>
        <!-- /.box-body -->
        <div class="box-footer text-right">
            <button id="color-submit-btn" type="submit" class="btn btn-sm btn-success"><i
                        class="fa fa-save"></i> <?php _e( "Save" ); ?></button>
        </div>
        <!-- /.footer -->
    </div>
    <!-- /.box -->
</form> <!-- CSS & JS -->