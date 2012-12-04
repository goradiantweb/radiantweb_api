<?php
class ApiAuthenticate Extends Model{

	var $error_message;

	public function checkToken($token=null){
		if(!$token){
			return array('error'=>'ERROR: this method requires a valid API token!');
		}
		$db = Loader::db();
		$uID = $db->getOne("SELECT uID FROM radiantwebApiAuth WHERE token='$token'");
		
		if($uID){
			return array('id'=>$uID,'error'=>null);
		}else{
			return array('error'=>'ERROR: this auth key appears to be invalid!');
		}
	}


	public function generateToken($username,$password){
	
		Loader::model('user');
		Loader::model('userinfo');
		$ip = Loader::helper('validation/ip');
		$vs = Loader::helper('validation/strings');
		
		if ((!$vs->notempty($username)) || (!$vs->notempty($password))) {
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
				$error = t('ERROR: An email address and password are required.');
			} else {
				$error = t('ERROR: A username and password are required.');
			}
			
			return $error;
		}
		
		$u = new User($username, $password);
		if ($u->isError()) {
			switch($u->getError()) {
				case USER_NON_VALIDATED:
					$error = t('ERROR: This account has not yet been validated. Please check the email associated with this account and follow the link it contains.');
					break;
				case USER_INVALID:
					if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
						$error = t('ERROR: Invalid email address or password.');
					} else {
						$error = t('ERROR: Invalid username or password.');						
					}
					break;
				case USER_INACTIVE:
					$error = t('ERROR: This user is inactive. Please contact us regarding this account.');
					break;
			}
			
			return $error;
			
		} else {
		
			Loader::model('userinfo');
			$ui = UserInfo::getByID($u->uID);
			$uo = $ui->getUserObject();
			//return $uo;
			$groups = $uo->uGroups;
			if(in_array('Administrators',$groups) || $uo->superUser == 1){
				$token = ApiAuthenticate::createUniqueID();
				$uID = $u->uID;
				$db = Loader::db();
				$db->Execute("INSERT INTO radiantwebApiAuth (token,uID) VALUES (?,?)",array($token,$uID));
				
				return $token;
			}else{
				$error = t('ERROR: This user does not have Admin privileges.');
				return $error;
			}
		}
		
	}
	
	
	public function createUniqueID(){
	
		$length = 32;
	    $characters = '123456789BCDFGHJKLMNPQRSTVWXZ';
	    $string = '';    
	
	    for ($p = 0; $p < $length; $p++) {
	        $string .= substr($characters, rand() % strlen($characters), 1);
	    }
	
		return $string;
	
	}

}

class ApiSpecialFunctions Extends Model{
	
	public function setMaintenanceMode($value){
		Config::save('SITE_MAINTENANCE_MODE',$value);
		return 'success';
	}
}
