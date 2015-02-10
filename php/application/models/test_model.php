	<?php 

	class Test_Model extends CI_Model
	{

		function __construct()
		{
			parent::__construct();
			$this->load->database();
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

		function insert($isim)
		{   
			$this->db->set('isim', $isim); 
            $query = $this->db->insert('test');
			echo $query;
			if ($query) {
				return true;
			} else {
				return false;
			}

		}

	}
	?>
