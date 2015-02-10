	<?php 

	class Users_Model extends CI_Model
	{

		function __construct()
		{
			parent::__construct();
		}

		function registerUser($user,$user_meta)

		{
			//insert register
			$user_insert = array(

				"ip_address"=> (int) sprintf("%u\n", ip2long(long2ip(ip2long($this->input->ip_address())))),
				"username"=> random_string('alnum',10),
				"password"=> md5($user['password']),
				"email"=>$user['email'],
				"created_on" => time(),
				"last_login" => time(),
				"state"=> '1'
				);

			$query = $this->db->insert('users',$user_insert);
			$user_id = $this->db->insert_id();			

			if ($query)
			{

				$insert = array(
					"merchant_id" => '0',
					"user_type"=> 'personal',
					"firstname"=> $user_meta['firstname'],
					"lastname"=> $user_meta['lastname'],
					"earned_bonus"=> '0',
					"expendable_bonus"=> '0',
					"mobile_number"=> $user_meta['mobile_number'],
					"current_latitude"=> $user_meta['lat'],
					"current_longitude"=> $user_meta['lng'],
					"referanskodu"=>random_string('unique',32),
					"referans"=>$user_meta['referans']?$user_meta['referans']:"",
					"user_id"=> $user_id,
					"environment"=> $user_meta['environment']);
				
				$query = $this->db->insert('meta',$insert);
				
				return true;
			}

			return false;
		}

		function registerSocialUser($user,$user_meta)
		{
			
			//insert register
			$user_insert = array(
				"ip_address"=> (int) sprintf("%u\n", ip2long(long2ip(ip2long($this->input->ip_address())))),
				"username"=> random_string('alnum',10),
				"password"=> md5($user['password']?$user['password']:random_string('alnum',10)),
				"email"=> $user['email'],
				"created_on" => time(),
				"last_login" => time(),
				"state"=> '1');
			
			$query = $this->db->insert('users',$user_insert);
			$user_id = $this->db->insert_id();			

			if ($query)
			{
				$insert = array(
					"merchant_id" => '0',
					"user_type"=> 'personal',
					"firstname"=> $user_meta['firstname'],
					"lastname"=> $user_meta['lastname'],
					"earned_bonus"=> '0',
					"expendable_bonus"=> '0',
					"mobile_number"=> $user_meta['mobile_number']?$user_meta['mobile_number']:'',
					"birthdate"=> $user_meta['birthdate'],
					"gender"=> $user_meta['gender'],
					"current_latitude"=> $user_meta['lat'],
					"current_longitude"=> $user_meta['lng'],
					"user_id"=> $user_id,
					"referanskodu"=>random_string('unique',32),
					"oauth_uid"=> $user_meta['facebook_id'],
					"oauth_location"=> $user_meta['location'],
					"profile_picture"=> $user_meta['profile_picture'],
					"environment"=> $user_meta['environment']);
				
				$query = $this->db->insert('meta',$insert);
			//	return $this->checkUser($user['email']);

				return true;
			}

			return false;
		}

		function updateSocialInfo($user_id, $user_meta)
		{
			
			$user_meta = array(
				"firstname"=> $user_meta['firstname'],
				"lastname"=> $user_meta['lastname'],
				"birthdate"=> $user_meta['birthdate'],
				"gender"=> $user_meta['gender'],
				"current_latitude"=> $user_meta['lat'],
				"current_longitude"=> $user_meta['lng'],
				"oauth_location"=> $user_meta['location'],
				"oauth_uid"=> $user_meta['facebook_id'],
				"profile_picture"=> $user_meta['profile_picture']
				);
			
			//update event_user state
			$query = $this->db->set($user_meta)
			->where('user_id', $user_id)
			->update('meta');	

			if ($query)
			{
				$this->db->set('last_login',time())
				->where('id', $user_id)
				->update('users');	
				return true;
			}
			return false;
		}

		function updateLinkedSocialInfo($user_id, $user_meta)
		{
			//update event_user state
			$query = $this->db->set($user_meta)
			->where('user_id', $user_id)
			->update('meta');	

			if ($query)
			{
				return true;
			}

			return false;

		}

		public function checkUser($email)
		{
			$this->db->select('u.id, u.email, m.firstname, m.lastname, u.group_id, u.password, m.earned_bonus, m.expendable_bonus, m.profile_picture, m.oauth_uid as facebook_id, m.merchant_id, m.mobile_number')
			->from('users u')
			->join('meta m', 'u.id = m.user_id')		           
			->where('u.email', $email);
			$query = $this->db->get();
			if ( $query->num_rows > 0 )
			{
				$this->db->set('last_login',time())
				->where('email', $email)
				->update('users');	

				$result = $query->result();
				$query->free_result();
				return $result;
			}
			return false;
			
		}
		
		public function checkUserId($id)
		{
			$this->db->select('u.id, u.email, m.firstname, m.lastname, u.password, m.earned_bonus, m.expendable_bonus, m.profile_picture, m.oauth_uid, m.city_id, m.county_id, m.district_id')
			->from('users u')
			->join('meta m', 'u.id = m.user_id')		           
			->where('u.id', $id);
			$query = $this->db->get();
			if ( $query->num_rows > 0 )
			{
				$result = $query->result();
				$query->free_result();
				return $result;
			}
			return false;
		}
		
		
		public function checkUserSocial($facebook_id)
		{
			$this->db->select('u.id, u.email, um.firstname, um.lastname, um.earned_bonus, um.expendable_bonus, um.profile_picture, um.oauth_uid')
			->from('meta um')	
			->join('users u','um.user_id = u.id')	           
			->where('um.oauth_uid', $facebook_id);
			$query = $this->db->get();
			if ( $query->num_rows > 0 )
			{
				$result = $query->result();
				$query->free_result();
				
				return $result;
			}
			return false;
		}

		public function checkUserLinkedSocial($facebook_id, $email)
		{
			$this->db->select('u.*')
			->from('meta um')	
			->join('users u','um.user_id = u.id')	           
			->where('um.oauth_uid', $facebook_id);
			$query = $this->db->get();
			if ( $query->num_rows > 0 )
			{
				$result = $query->result();
				$query->free_result();
				
				return $result;
			}
			$this->db->select('u.*')
			->from('users u')	           
			->where('u.email', $email);
			$query = $this->db->get();
			if ( $query->num_rows > 0 )
			{
				$result = $query->result();
				$query->free_result();
				return $result;
			}
			return 2;
			
		}

         // 1-Public , 0-Protected
		function getUserPrivacyState($user_id)
		{    
            $this->db->select('state');
			$this->db->where('id',$user_id); 
			$query = $this->db->get('users');  
			$result = $query->row(); 

			return $result->state;

		}

	}
	?>
