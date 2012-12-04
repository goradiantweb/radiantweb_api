<?php  

defined('C5_EXECUTE') or die(_("Access Denied."));

class RadiantwebApiPackage extends Package {

	protected $pkgHandle = 'radiantweb_api';
	protected $appVersionRequired = '5.4.1';
	protected $pkgVersion = '1.0.0';
	
	public function getPackageDescription() {
		return t("RESTfull C5 API");
	}
	
	public function getPackageName() {
		return t("RadiantWeb C5 API");
	}
	
	public function install() {
	
		$pkg = parent::install();
		
		Loader::model('single_page');
		$api = SinglePage::add('/api/', $pkg);
		$api->setAttribute('exclude_nav',1);
		$api->setAttribute('exclude_page_list',1);
		$api->setAttribute('exclude_sitemapxml',1);

        
		$textt = AttributeType::getByHandle('text'); 
		$apikey = UserAttributeKey::getByHandle('c5_api_key'); 
		if( !is_object($apikey) ) {
		 	UserAttributeKey::add($textt, 
		 	array('akHandle' => 'c5_api_key', 
		 	'akName' => t('API Key'), 
		 	'akIsSearchable' => false, 
		 	'uakProfileEdit' => false, 
		 	'uakProfileEditRequired'=> false, 
		 	'uakRegisterEdit' => false, 
		 	'uakProfileEditRequired'=>false
		 	),$pkg);
		 }
        
        Cache::flush();
	}
	
	public function on_start(){
		//Config::save('SITE_MAINTENANCE_MODE', 0);
		if($_POST['persist']){
			$tk = Loader::helper('validation/token');
			$token = $tk->generate();
			$_REQUEST['ccm_token'] = $token;
		}
	}
}