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
		SinglePage::add('/api/', $pkg);
		
		// install pages
		$iak = CollectionAttributeKey::getByHandle('icon_dashboard');
		
		$cp = SinglePage::add('/dashboard/radiantweb_api/', $pkg);
        $cp->update(array('cName'=>t('RESTfull C5 API'), 'cDescription'=>t('RadiantWeb C5 API')));
        $cp->setAttribute($iak,'icon-list-alt');
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