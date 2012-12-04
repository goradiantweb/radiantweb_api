<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('radiantweb_api','radiantweb_api');
class ApiList Extends Model{
	
	var $key_list;

	public function __construct(){
		$this->setAPIkeyList();
	}
	
	private function setAPIkeyList(){
		$db = Loader::db();
		$q = "SELECT * from radiantwebApiAuth";
		$r = $db->execute($q);
		while($row = $r->fetchrow()){
			$APIconnect = New ApiConnect($row['token']);
			$this->key_list[] = $APIconnect;
		}
		
	}
	
	public function getApiList(){
		return $this->key_list;
	}

}