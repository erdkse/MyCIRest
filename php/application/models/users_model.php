<?php

class Users_Model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function registerUser($password, $email, $additional_data = array(), $group) {
        //insert register
        //$ip_address = $this->_prepare_ip($this->input->ip_address());

        $user_insert = array(
            "username" => strtolower($additional_data['first_name']) . strtolower($additional_data['last_name']),
            "password" => md5($password),
            "email" => $email,
            "ip_address" => $this->_prepare_ip($additional_data['ip_address']),
            "created_on" => time(),
            "last_login" => time(),
            "first_name" => $additional_data['first_name'],
            "last_name" => $additional_data['last_name'],
            "phone" => $additional_data['mobile_number'],
            "active" => '1'
        );

        if ($this->email_check($email)) {
            //$this->set_error('account_creation_duplicate_email');
            // -1
            return -1;
        } elseif ($this->username_check($username)) {
            //$this->set_error('account_creation_duplicate_username');
            // -2
            return -2;
        } else {
            $query = $this->db->insert('users', $user_insert);
            $user_id = $this->db->insert_id();

            if ($query) {

                $insert = array(
                    "user_id" => $user_id,
                    "group_id" => $group);

                $this->db->insert('users_groups', $insert);
                $location_insert = array('current_latitude' => $additional_data['current_latitude'],
                                        'current_longitude' => $additional_data['current_longitude'],
                                        'driver_state' => $additional_data['driver_state'],
                                        'available_state' => $additional_data['available_state']);
                $this->insertLocation($user_id, $location_insert);
                return $user_id;
            } else {
                return -3;
            }
        }
    }

    // 1-Public , 0-Protected
    function getUserPrivacyState($user_id) {
        $this->db->select('state');
        $this->db->where('id', $user_id);
        $query = $this->db->get('users');
        $result = $query->row();

        return $result->state;
    }

    public function email_check($email = '') {

        if (empty($email)) {
            return 0;
        }

        $query = $this->db->where('email', $email)
                ->order_by("id", "ASC")
                ->limit(1)
                ->from('users')
                ->count_all_results();

        if ($query > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function username_check($username = '') {

        if (empty($username)) {
            return FALSE;
        }

        $query = $this->db->where('username', $username)
                ->order_by("id", "ASC")
                ->limit(1)
                ->from('users')
                ->count_all_results();

        if ($query > FALSE) {
            return TRUE;
        }

        return FALSE;
    }

    protected function _prepare_ip($ip_address) {
        //just return the string IP address now for better compatibility
        return $ip_address;
    }

    public function loginUser($email, $password) {
        $password = md5($password);

        $query = $this->db->select('id,username,email,first_name,last_name,last_login')
                ->where('email', $email)
                ->where('password', $password)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');

        if ($query->num_rows() === 1) {
            $updateData = array('last_login' => time(),
                'active' => 1);

            $user = $query->row();

            $this->db->where('id', $user->id);
            $this->db->update('users', $updateData);

            return $user;
        } else {
            //There is no user, or password not matched
            return NULL;
        }
    }

    public function logoutUser($id) {

        $updateData = array('active' => 0);

        $query = $this->db->where('id', $id)
                ->update('users', $updateData);

        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getUser($id) {
        $query = $this->db->select('id,username,email,first_name,last_name,last_login,company,phone,active')->where('email', $email)
                ->where('password', $password)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');
        if ($query->num_rows() === 1) {
            $updateData = array('last_login' => time(),
                'active' => 1);

            $user = $query->row();

            $this->db->where('id', $user->id);
            $this->db->update('users', $updateData);

            return $user;
        } else {
            //There is no user, or password not matched
            return NULL;
        }
    }

    public function updateUser($user_id, $user_data) {

        $query = $this ->db ->where('id', $user_id)
                            ->update('user', $user_data);

        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function insertLocation($user_id, $location_data) {
        $insert = array(
            "user_id" => $user_id,
            "current_latitude" => $location_data['current_latitude'],
            "current_longitude" => $location_data['current_longitude'],
            "driver_state" => $location_data['driver_state'],
            "available_state" => $location_data['available_state']);

        $query = $this->db->insert('locations', $insert);

        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateLocation($user_id, $location_data) {
        
        $query = $this ->db ->where('id', $user_id)
                            ->update('locations', $location_data);

        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
