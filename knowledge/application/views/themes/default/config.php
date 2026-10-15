<?php

	$appversion=$this->config->item('app_version');
add_css("plugins/bootstrap/3.3.7/css/bootstrap.min.css",0);
add_css("plugins/font-awesome/4.7.0/css/font-awesome.min.css",1);
add_css("plugins/ionicons/2.0.1/css/ionicons.min.css",1);
add_css("plugins/select2/css/select2.min.css",1);
add_css("plugins/select2/css/select2-bootstrap.min.css",1);
	if(Mapp_setting::GetSettingsValue("is_rtl_admin")=="Y"){
		
		add_css( "plugins/rtl/bootstrap-rtl.min.css", 1 );
		add_css( "plugins/rtl/adminlte/css/AdminLTE.min.css", 1 );
		add_css( "plugins/rtl/rtl-admin.css", 1 );
		add_css("css/main-style-rtl.css?v={$appversion}",4);
		add_css("css/app-responsive-rtl.css");
	}else{
		
		add_css("css/app-responsive.css");
		add_css("theme/default/css/AdminLTE.min.css",1);
		add_css("css/main-style.css?v={$appversion}",4);
	}
	$admin_color=Mapp_setting::GetSettingsValue( "admin_color","default" );
	if(empty($admin_color)){
		
	}
	if('_none'!=$admin_color) {

		add_css( "theme/admin/css/color/" . Mapp_setting::GetSettingsValue( "admin_color", "default" ) . ".css", 5 );
		add_css( "theme/admin/css/bss-up-23.css", 6 );
	}
add_css( "theme/default/css/custom.css", 10 );

add_js("plugins/jquery/3.3.1/jquery.min.js",0);
add_js("plugins/bootstrap/3.3.7/js/bootstrap.min.js",1);
add_js("plugins/select2/js/select2.full.min.js",1);
add_js("theme/default/js/app.min.js",10);

