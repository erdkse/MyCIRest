<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
 */
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Example extends REST_Controller {

    function __construct() {
        // Construct our parent class
        parent::__construct();
    }

    function registerUser_post() {

        //$username = $this->input->post('username')
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $additional_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'company' => $this->input->post('company'),
            'phone' => $this->input->post('phone'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'current_latitude' => $this->input->post('current_latitude'),
            'current_longitude' => $this->input->post('current_longitude'),
            'driver_state' => $this->input->post('driver_state'),
            'available_state' => $this->input->post('available_state')
        );

        // 1 for passengers
        // 2 for drivers
        $group = $this->input->post('group');

        // -1 : account_creation_duplicate_email
        // -2 : account_creation_duplicate_username
        // -3 : Unknown reason
        // id : SUCCESS!

        $return = $this->users_model->registerUser($password, $email, $additional_data, $group);
        $user = (object) array('id' => $return);
        $response = (object) array('user' => $user);
        /**/
        if ($return > 0) {
            $this->response(array('code' => 200,
                'status' => true,
                'message' => "Succes",
                'response' => $response), 200);
        } else {
            switch ($return) {
                
                case -1:
                    //$user = (object) array('foo' => 'bar', 'property' => 'value');
                    $this->response(array('code' => 201,
                        'status' => false,
                        'message' => "account_creation_duplicate_email",
                        'response' => $response), 200);
                    break;
                case -2:
                    $this->response(array('code' => 202,
                        'status' => false,
                        'message' => "account_creation_duplicate_username",
                        'response' => $response), 200);
                    break;
                case -3:
                    $this->response(array('code' => 205,
                        'status' => false,
                        'message' => "Unknown reason",
                        'response' => $response), 200);
                    break;
            }
        }
    }

    function loginUser_post() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $query = $this->users_model->loginUser($email, $password);
        $response = (object) array('user' => $query);
        if ($query) {
            $this->response(array('code' => 200,
                                    'status' => TRUE,
                                    'message' => "login is successed",
                                    'response' => $response), 200);
        } else {
            $this->response(array('code' => 201,
                                    'status' => FALSE,
                                    'message' => "login is failed",
                                    'response' => $response), 200);
        }
    }

    function logoutUser_post() {
        $id = $this->input->post('id');
        $query = $this->users_model->logoutUser($id);
        //$response = (object) array();
        if ($query) {
            $this->response(array('code' => 200,
                                    'status' => TRUE,
                                    'message' => "logout is successed",
                                    'response' => NULL), 200);
        } else {
            $this->response(array('code' => 201,
                                    'status' => FALSE,
                                    'message' => "logout is failed",
                                    'response' => NULL), 200);
        }
    }
    
    function updateUserLocation_post() {
        $id = $this->input->post('id');
        $locationData = array(
            'current_latitude' => $this->input->post('current_latitude'),
            'current_longitude' => $this->input->post('current_longitude'),
            'driver_state' => $this->input->post('driver_state'),
            'available_state' => $this->input->post('available_state'));
        
        $query = $this->users_model->updateLocation($id,$locationData);
        
        if ($query) {
            $this->response(array('code' => 200,
                                    'status' => TRUE,
                                    'message' => "User location is updated",
                                    'response' => NULL), 200);
        } else {
            $this->response(array('code' => 201,
                                    'status' => FALSE,
                                    'message' => "User location is failed",
                                    'response' => NULL), 200);
        }
    }
}

?>