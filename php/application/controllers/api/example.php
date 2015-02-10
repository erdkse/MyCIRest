<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
require APPPATH.'/libraries/REST_Controller.php';

class Example extends REST_Controller
{
	function __construct()
    {
        // Construct our parent class
        parent::__construct();
        
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        //$this->load->model(array('users_model','device_model','test_model'));
    }

    function registerUser_post()
    {
        $user = array('email' => $this->input->post('email'),
                        'password' => $this->input->post('password') );

        $user_meta = array('firstname' => $this->input->post('firstname'),
                            'lastname' => $this->input->post('lastname'),
                            'mobile_number' => $this->input->post('firstname') );

        $insertion = $this -> users_model -> registerUser($user,$user_meta);

        if ($insertion) {
            $this->response(array('code' => 200,
                                    'status' => true,
                                    'sonuc' => $sonuc), 200);
        } else {
            $this->response(array(  'code' => 201,
                                    'status' => true,
                                    'sonuc' => $sonuc), 200);
        }
        
    }


    function getUsers_post()
    {
        $userId = $this->input->post('user_id');
        $user = $this->users_model->checkUserId($userId);

        /*
        $message = array(
                            'id' => $this->get('id'), 
                            'name' => $this->post('name'), 
                            'email' => $this->post('email'), 
                            'message' => 'ADDED!');

        $this->getFeedsToArray($user);
        $this->response($this->getFeedsToArray($user),200);
        */
        $this->response($user, 200); // 200 being the HTTP response code
         
    }
    
}