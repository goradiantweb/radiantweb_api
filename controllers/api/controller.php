<?php 
defined('C5_EXECUTE') or die(_("Access Denied.")); 

Loader::model('radiantweb_api','radiantweb_api');


class ApiController extends Controller {

	private $request_vars;
	private $data;
	private $http_accept;
	private $method;
	
	public function on_page_view(){

	}
	
	public function view($method,$id = null){
		
		$request_method = strtolower($_REQUEST['rest']);

		if(!$request_method){
			$request_method = strtolower($_SERVER['REQUEST_METHOD']);
		}

		$by = ($id) ? 'ByID' : '';
		
		$this->data = $_REQUEST;
		$this->method = $request_method;
		
		$this->loadRequiredModels();

		switch($request_method){
			case 'get':
				if($this->allowedMethods($method)){			
					if(substr_count($method,'List') > 0){
						// The Page/User/File List methods can
						// recieve the following data:
						// - filters
						//   - column : string (ak_fist_name)
						//   - modifier : string ('LIKE')
						//   - value : string/num ('Chad')
						//
						// - num : number (12)
						$call = New $method();
						$func = $request_method;
						if(is_array($_POST['filters'])){
							foreach($_POST['filters'] as $filter){
								$column = $filter['column'];
								$modifier = $filter['modifier'];
								$value = $filter['value'];
								$string = "$column $modifier '$value'";
								$call->filter(false,"$string");
							}
						}
						$call_list = $call->$func();
						
						if(is_array($call_list)){
							foreach($call_list as $item){
								if(substr_count($method,'Page') > 0){
								
									$nh = Loader::helper('navigation');
									$atts = $item->getSetCollectionAttributes();
									$call_response['title'] = $item->getCollectionName();
									$call_response['description'] = $item->getCollectionDescription();
									$call_response['id'] = $item->getCollectionID();
									$call_response['path'] = $nh->getLinkToCollection($item);

									if(is_array($_POST['attributes'])){
										foreach($_POST['attributes'] as $label=>$attribute){
											$call_response[$label] = $item->getAttribute($attribute);
										}
									}
									
								}elseif(substr_count($method,'File') > 0){
								
									$item_version = $item->getVersion();
									$atts = $item_version->getAttributeList();
									
									
								}else{
									$call_response[] = $item;
								}
							}
						}
						
					}elseif(substr_count($method,'Info') > 0){
						// UserInfo object behaves differently than the other models
						// this function requires attributes input in order to have
						// any output
						$func = $request_method.$by;
						$call_object = $method::$func($id);
						if(is_array($_POST['attributes'])){
							$call_response = array();
							foreach($_POST['attributes'] as $label=>$attribute){
								$call_action = $request_method.$attribute;
								$call_response[$label] = $call_object->$call_action();
							}
						}
						
					}elseif(substr_count($method,'Custom') > 0){
	
						//Custom->model,Custom->method,Custom->package
						$auth = ApiAuthenticate::checkToken($_POST['token']);
						if($auth['id']) {
							$model = $_POST['model'];
							$package = $_POST['package'];
							$class = $_POST['class'];
							$func = $_POST['funct'];
							
							Loader::model($model,$package);
							
							$call_object = New $class();
							
							if($func){
								$call_response = $call_object->$func($_POST['value']);
							}else{
								$call_response = $call_object;
							}
						}else{
							$call_response = $auth['error'];
						}
						
					}else{
						// Page::getByID($id), User::getByUserID($id), File::getByID($id)
						$call = New $method();
						if($method == 'User' && $request_method == 'get'){
							$func = $request_method.'By'.ucfirst($method).'ID';
						}else{
							$func = $request_method.$by;
						}
						$call_response = $call->$func($id);
					}
					
					if($_POST['return'] == 'html'){
						print $call_response;
					}else{
						print @json_encode($call_response);
					}
					exit;
				}else{
					print json_encode('ERROR: this method is not allowed');
				}
				break;
				
			case 'update':
				$auth = ApiAuthenticate::checkToken($_POST['token']);
				if($auth['id']) {
					if($this->allowedMethods($method)){	
	
						$call = New $method($id);
						
						if(substr_count($method,'User') > 0){
							Loader::model('userinfo');
							$ui = UserInfo::getByID($id);
							
							if($ui->uID){
								if(is_array($_POST['attributes'])){
									$call_response = array();
									foreach($_POST['attributes'] as $label=>$attribute){
										$ak = UserAttributeKey::getByHandle(str_replace(' ','_',strtolower($label)));
										$ui->setAttribute($ak,$attribute);
									}
								}
								
								print json_encode('SUCCESS');
							}else{
								print json_encode('ERROR: no user found matching this ID');
							}
						}
					}
					
					exit;
				}else{
					print json_encode($auth['error']);
					exit;
				}
				
				break;
				
			case 'delete':
				
				break;
			
			case 'post':
				if($this->allowedMethods($method)){	
					if(substr_count($method,'Custom') > 0){
						//Custom->model,Custom->method,Custom->package
						$auth = ApiAuthenticate::checkToken($_POST['token']);
						if($auth['id']) {
							$model = $_POST['model'];
							$package = $_POST['package'];
							$class = $_POST['class'];
							$func = $_POST['funct'];
							
							Loader::model($model,$package);
							
							$call_object = New $class();
							
							if($func){
								$call_response = $call_object->$func($_POST['value']);
							}else{
								$call_response = $call_object;
							}
							
						}else{
							$call_response = $auth['error'];
						}
						
					}
					
					if($_POST['return'] == 'html'){
						print $call_response;
					}else{
						print @json_encode($call_response);
					}
				}
				break;
			
			case 'request':
				if(substr_count($method,'Authenticate') > 0){
					$auth = ApiAuthenticate::generateToken($_REQUEST['user'],$_REQUEST['pass']);
					print $auth;
				}
				break;
			}
		exit;
	}
	
	
	private function allowedMethods($method){
		
		$mothod_list = array(
			'Authenticate',
			'Custom',
			'User',
			'UserInfo',
			'Page',
			'File',
			'UserList',
			'PageList',
			'FileList',
			'Group'
		);
		
		if(in_array($method,$mothod_list)){
			return true;
		}else{
			return false;
		}
		
	}

	
	private function requestConnect(){
		
		
	}
	
	private function loadRequiredModels(){
	    Loader::model('single_page');
	    Loader::model('collection');
	    Loader::model('page');
	    loader::model('block');
	    Loader::model('collection_types');
	    Loader::model('/attribute/categories/collection');
	    Loader::model('/attribute/categories/user');
	    Loader::model('page_list');
	    Loader::model('user');
	    Loader::model('user_list');
	    Loader::model('userinfo');
	    Loader::model('groups');
	}

}

?>