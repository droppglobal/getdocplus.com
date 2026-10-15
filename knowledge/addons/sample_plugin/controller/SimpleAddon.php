<?php
class SimpleAddon extends APPAddOns{
	public function OnInit() {
		$this->RegisterAdminAction("settings",[$this,'settings']);
		$this->RegisterAdminAction("other",[$this,'other']);
		$this->RegisterAdminAction("another",[$this,'another']);

		$group=$this->AddAdminMenuGroup("Simple Plugin",'fa fa-star');
		$group->AddSubMenu("Settings","fa fa-star","settings");
		$group->AddSubMenu("Other settings","fa fa-star","other");
		$group->AddSubMenu("Another Settings","fa fa-star","another");


	}
	public function OnActivate() {
		
	}
	public function OnDeactivate() {
		
	}

	public function getPluginSlug() {
		return 'simple-plugin';
	}

	public function settings(){
		$this->SetTitle("Simple Admin");
		$this->Display('settings');

	}

	public function other(){
		if(IsPostBack) {
			AddInfo( "We got your post request" );
			AddInfo( print_r( $_POST, true ) );
		}
		$this->SetTitle("Simple Admin");
		$this->Display('other');
	}
	public function another(){
		$this->SetTitle("Simple Admin");
		$this->Display('test');
	}
}