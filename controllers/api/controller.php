<?php 
defined('C5_EXECUTE') or die(_("Access Denied.")); 


class ApiController extends Controller {

	private $request_vars;
	private $data;
	private $http_accept;
	private $method;
	
	public function on_page_view(){

	}
	
	public function view($method,$id = null){
		
		$request_method = strtolower($_SERVER['REQUEST_METHOD']);
		
		if($request_method == 'put' || $request_method == 'delete'){
			// Make a blank array called $_PUT
			$_REQUEST = array();
			// Read contents from the standard input buffer
			//covert to $_PUT
			parse_str(file_get_contents("php://input"),$_REQUEST);
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
						$call = new $method();
						$func = $request_method;
						if(is_array($_REQUEST['filters'])){
							foreach($_REQUEST['filters'] as $filter){
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

									if(is_array($_REQUEST['attributes'])){
										foreach($_REQUEST['attributes'] as $label=>$attribute){
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
						if(is_array($_REQUEST['attributes'])){
							$call_response = array();
							foreach($_REQUEST['attributes'] as $label=>$attribute){
								$call_action = $request_method.$attribute;
								$call_response[$label] = $call_object->$call_action();
							}
						}
						
					}elseif(substr_count($method,'Custom') > 0){
	
						//Custom->model,Custom->method,Custom->package
						$auth = ApiAuthenticate::checkToken($_REQUEST['token']);
						if($auth['id']) {
							$model = $_REQUEST['model'];
							$package = $_REQUEST['package'];
							$class = $_REQUEST['class'];
							$func = $_REQUEST['funct'];
							
							Loader::model($model,$package);
							
							$call_object = new $class();
							
							if(is_array($_REQUEST['filters'])){
								foreach($_REQUEST['filters'] as $filter){
									$column = $filter['column'];
									$modifier = $filter['modifier'];
									$value = $filter['value'];
									$string = "$column $modifier '$value'";
									$call_object->filter(false,"$string");
								}
							}
							
							if($func){
								$call_response = $call_object->$func($_REQUEST['value']);
							}else{
								$call_response = $call_object;
							}
						}else{
							$this->getResponse('401',$auth['error']);
							exit;
						}
						
					}elseif(substr_count($method,'Authenticate') > 0){
					
						$call_response = ApiAuthenticate::generateToken($_REQUEST['user'],$_REQUEST['pass'],$_REQUEST['group']);
						print $call_response;
						
					}else{
						// Page::getByID($id), User::getByUserID($id), File::getByID($id)
						$call = new $method();
						if($method == 'User' && $request_method == 'get'){
							$func = $request_method.'By'.ucfirst($method).'ID';
						}else{
							$func = $request_method.$by;
						}
						$call_response = $call->$func($id);
					}
					
					$this->getResponse('200',$call_response);
					exit;
				}else{
					$this->getResponse('405','ERROR: this method is not allowed');
					exit;
				}
				break;
				
			case 'put':

				if($this->allowedMethods($method)){	
					$auth = ApiAuthenticate::checkToken($_REQUEST['token']);
					if($auth['id']) {
						if(substr_count($method,'Custom') > 0){
				
							$model = $_REQUEST['model'];
							$package = $_REQUEST['package'];
							$class = $_REQUEST['class'];
							$func = $_REQUEST['funct'];
							
							Loader::model($model,$package);
							
							$call_object = new $class();
							
							if($func){
								$call_response = $call_object->$func($_REQUEST['value']);
							}else{
								$call_response = $call_object;
							}

						}elseif(substr_count($method,'User') > 0){
						
							Loader::model('userinfo');
							$ui = UserInfo::getByID($id);
							if($ui->uID){
								if(is_array($_REQUEST['attributes'])){
									$call_response = array();
									foreach($_REQUEST['attributes'] as $label=>$attribute){
										$ak = UserAttributeKey::getByHandle(str_replace(' ','_',strtolower($label)));
										if($ak){
											$ui->setAttribute($ak,$attribute);
										}
									}
								}
								
								$call_response = 'SUCCESS';
							}else{
								$call_response = 'ERROR: no user found matching this ID';
								$this->getResponse('400',$call_response);
								exit;
 							}

						}elseif(substr_count($method,'Page') > 0){
							
							$p = Page::getByID($id);
							if($p->getCollectionID()){
								if(is_array($_REQUEST['attributes'])){
									$call_response = array();
									foreach($_REQUEST['attributes'] as $label=>$attribute){
										$ak = UserAttributeKey::getByHandle(str_replace(' ','_',strtolower($label)));
										if($ak){
											$p->setAttribute($ak,$attribute);
										}
									}
								}
								
								$call_response = 'SUCCESS';
							}else{
								$call_response = 'ERROR: no Page found matching this ID';
								$this->getResponse('400',$call_response);
								exit;	
 							}
 						}
 						
 						$this->getResponse('200',$call_response);

					}else{
						$this->getResponse('401',$auth['error']);
						exit;
					}
					

				}else{
					$this->getResponse('405','ERROR: this method is not allowed');
					exit;
				}
				
				break;
				
			case 'delete':
				if(substr_count($method,'Custom') > 0){
	
					//Custom->model,Custom->method,Custom->package
					$auth = ApiAuthenticate::checkToken($_REQUEST['token']);
					if($auth['id']) {
						$model = $_REQUEST['model'];
						$package = $_REQUEST['package'];
						$class = $_REQUEST['class'];
						$func = $_REQUEST['funct'];
						
						Loader::model($model,$package);
						
						$call_object = new $class();
						
						if($func){
							$call_response = $call_object->$func($_REQUEST['value']);
						}else{
							$call_response = $call_object;
						}
						
						$this->getResponse('200',$call_response);
					}else{
						$this->getResponse('401',$auth['error']);
						exit;
					}
					
				}
				
				break;
			
			case 'post':
				if($this->allowedMethods($method)){	
					if(substr_count($method,'Custom') > 0){
						//Custom->model,Custom->method,Custom->package
						$auth = ApiAuthenticate::checkToken($_REQUEST['token']);
						if($auth['id']) {
							$model = $_REQUEST['model'];
							$package = $_REQUEST['package'];
							$class = $_REQUEST['class'];
							$func = $_REQUEST['funct'];
							
							Loader::model($model,$package);
							
							$call_object = new $class();
							
							if($func){
								$call_response = $call_object->$func($_REQUEST['value']);
							}else{
								$call_response = $call_object;
							}
							
							$this->getResponse('200',$call_response);
							
						}else{
							$this->getResponse('401',$auth['error']);
							exit;
						}
						
					}
				}else{
					$this->getResponse('405','ERROR: this method is not allowed');
					exit;
				}
				
				break;
			}
			
		exit;
	}
	
	private function getResponse($code=null,$response=null){
	
		switch($code){
			case '405':
				header('HTTP/1.1 405 Method Not Allowed'); 
				break;
			case '401':
				header('HTTP/1.1 401 Unauthorized'); 
				break;
			case '400':
				header('HTTP/1.1 400 Bad Request'); 
				break;
			case '230':
				header('HTTP/1.1 230 Authentication Successful'); 
				break;
			case '202':
				header('HTTP/1.1 202 Accepted'); 
				break;
			case '201':
				header('HTTP/1.1 201 Created'); 
				break;
			case '200':
				header('HTTP/1.1 200 OK'); 
				break;
		}
		
		if($_REQUEST['return'] == 'html'){
			print $response;
		}else{
			print @json_encode($response);
		}
		
	}
	
	
	private function allowedMethods($method){
		
		$method_list = array(
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
		
		if(in_array($method,$method_list)){
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
	    Loader::model('radiantweb_api','radiantweb_api');
	}

}

?>