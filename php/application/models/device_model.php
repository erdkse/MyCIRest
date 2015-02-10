<?php

  class Device_Model extends CI_Model
  {

     function __construct()
     {
       parent::__construct();
     }
    
     public function checkIosDevice($user_id,$token)
	 {
		$this->db->select('id')
				->from('device_token')		           
				->where('user_id', $user_id)
				->where('token', $token);
		$query = $this->db->get();
		if ( $query->num_rows > 0 )
		  return true;

		return false;
	}
	
	function loginIosDevice($user_id,$token)
	{
		//update event_user state
		$query = $this->db->set('state', '1')
					->where('user_id', $user_id)
					->where('token',  $token)
					->update('device_token');	
		if ($query)
		  return true;

		return false;
	}
	
	function loginAndroidDevice($user_id,$deviceId)
	{
		//update event_user state
		$query = $this->db->set('state', '1')
					->where('user_id', $user_id)
					->where('deviceId',  $deviceId)
					->update('device_token');	
		if ($query)
		  return true;

		return false;
	}
	
	function logoutDevice($user_id,$token)
	{
		//update event_user state
		$query = $this->db->set('state', '0')
					->where('user_id', $user_id)
					->where('token',  $token)
					->update('device_token');	
		if ($query)
			return true;
	     
		 return false;
	}
	
	function logoutAllDevice($token)
	{
		//update event_user state
		$query = $this->db->set('state', '0')
					->where('token',  $token)
					->update('device_token');	
		if ($query)
		  return true;

		return false;
	}
	
	
	function insertDevice($user_id,$device)
	{
		//userid push to device, state 1 push to device
		//insert comment
		$this->logoutAllDevice($device['token']);
		
		$insert = array(
				 "user_id" => $user_id,
          		 "osType"=> $device['osType'],
	             "osVersion"=> $device['osVersion'],
	             "device"=> $device['device'],
	             "deviceId"=> $device['deviceId'],
	             "token"=> $device['token'],
	             "state"=> '1');
				 
		$query = $this->db->insert('device_token',$insert);
		if ($query)
		  return true;

		return false;
	}
	
	function insertAndroidDevice($user_id,$device)
	{
		//userid push to device, state 1 push to device
		//insert comment
		$this->logoutAllDevice($device->token);
		
		$insert = array(
				 "user_id" => $user_id,
          		 "osType"=> $device->osType,
	             "osVersion"=> $device->osVersion,
	             "device"=> $device->device,
	             "deviceId"=> $device->deviceId,
	             "token"=> $device->token,
	             "state"=> '1');
				 
		$query = $this->db->insert('device_token',$insert);
		if ($query)
		   return true;

		return false;
	}
	
	function checkAndroidDevice($user_id,$deviceId)
	{
		//userid push to device, state 1 push to device
		//insert comment
		$this->db->select('id')
				->from('device_token')		           
				->where('user_id', $user_id)
				->where('deviceId', $deviceId);
		$query = $this->db->get();
		if ( $query->num_rows > 0 )
			return true;
		
		return false;
	}
	
	function checkAndroidDeviceToken($user_id,$deviceId,$deviceToken)
	{
		//userid push to device, state 1 push to device
		//insert comment
		$this->db->select('id')
				->from('device_token')		           
				->where('user_id', $user_id)
				->where('deviceId', $deviceId)
				->where('token', $deviceToken);
	
		$query = $this->db->get();
		if ( $query->num_rows > 0 )
		   return true; 

		return false;
	}
	
	function updateAndroidDeviceToken($user_id,$deviceId,$deviceToken)
	{
		$query = $this->db->set('token', $deviceToken)
					->where('user_id', $user_id)
					->where('deviceId', $deviceId)
					->update('device_token');	
		
		if ($query)
		   return true;

		return false;
	}
	
	function getOnlineDevice($user_id)
	{
		//userid push to device, state 1 push to device
		//insert comment
		$this->db->select('*')
				->from('device_token')		           
				->where('user_id', $user_id)
				->where('state', '1');
		$query = $this->db->get();
		if ( $query->num_rows > 0 )
		{
			$result = $query->result();
			$query->free_result();
			return $result;
		}
		  return false;
	}
  }

  ?>