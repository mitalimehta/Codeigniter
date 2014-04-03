<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class User extends REST_Controller {

        // example http://api2.pricenista.com/user/getuserprofile/id/X
        public function getuserprofile_get(){
                if(!($this->get('id_type'))){
                   $id_type = 1;
                }else{
                   $id_type = $this->get('id_type');
                }
                $user = $this->datahandler->getUserById($this->get('user_id'), $id_type);   
                if($user){
                   log_message('info', $user);
                   $this->response($user, 200);
                }else{
                   log_message('error', "User not found!");
                   $this->response(NULL, 404);
                }
        }


        public function userlogin_post(){

              $userData = array(
                           "ext_id"=> $this->post('ext_id'),
                           "ext_type"=> $this->post('ext_type'),
                           "name"=> $this->post('name'),
                           "email"=> $this->post('email'),
                           "avatar"=> $this->post('avatar'),
                           "first_name"=> $this->post('first_name'),
                           "last_name"=> $this->post('last_name'),
                           "gender"=> $this->post('gender'),
                           "locale"=> $this->post('locale'),
                           "location"=> $this->post('location'),
                           "home_town"=> $this->post('home_town'),
                           "birthdate"=> $this->post('birthdate')
                );

              $user = $this->datahandler->userlogin($userData);  

              if($user){
               
                 //log_message('info', $user);
                 $this->response($user, 200);
              }else{
                
                 //log_message('error', "User not found!");
                 $this->response('something went wrong', 404);

              }

        }

        // example http://api2.pricenista.com/user/insertuser/user_id/xxx/ext_id/xxx/ext_type/xxx/created/xx-xx-xx xx:xx:xx/active/xxx/name/xxx/password/xxx/email/xxx@xxx.xxx/avatar/xxx.jpg/first_name/xxx/last_name/xxx/modified/xx-xx-xx xx:xx:xx/gender/{male|female}/locale/xxx/location/xxx/hometown/xxx/age/xxx
        public function insertuser_get(){
                $user = array(
                           "user_id"=> '',
                           "ext_id"=> $this->get('ext_id'),
                           "ext_type"=> $this->get('ext_type'),
                           "created"=> $this->get('created'),
                           "active"=> $this->get('active'),
                           "name"=> $this->get('name'),
                           "password"=> $this->get('password'),
                           "email"=> $this->get('email'),
                           "avatar"=> $this->get('avatar'),
                           "first_name"=> $this->get('first_name'),
                           "last_name"=> $this->get('last_name'),
                           "modified"=> $this->get('modified'),
                           "gender"=> $this->get('gender'),
                           "locale"=> $this->get('locale'),
                           "location"=> $this->get('location'),
                           "home_town"=> $this->get('home_town'),
                           "age"=> $this->get('age')
                );
                $newuser = $this->datahandler->putUser($user);
                if($newuser){
                   log_message('info', $newuser);
                   $this->response(array(
                                     'result:' => 'ok',
                   ));
                   //$this->response('result:ok', 200);
                }else{
                   log_message('error', "Error adding user!");
                   $this->response(NULL, 404);
                }
        }

        public function getusercategories_get(){

                $userID = $this->get('user_id');

                $categories['user_id'] = $userID;

                $categories['categories'] = $this->datahandler->getUsercategories($userID);   

                if($categories){
                   log_message('info', $categories);
                   $this->response($categories, 200);
                }else{
                   log_message('error', "empty table");
                   $this->response(NULL, 404);
                }
        }

        public function getuserfriends_post(){

              $userID = $this->post('user_id');
              $fb_friends = $this->post('fb_friends');

              //echo "<pre>";print_r($fb_friends);

              //$test = 'test string';

              $this->response($fb_friends, 200);
              //exit;

        }



/*
	function allusers_get(){
		
		// example http://api2.pricenista.com/user/allusers/
		
		$this->load->model("user_db");
		
		$users = $this->user_db->getAllusers();
      
		
        if($users)
        {
        	log_message('info', $users);
            $this->response($users, 200);
            
        }

        else
        {
        	log_message('error', "empty table");
            //$this->response(NULL, 404);
        }
	}
	
	function updateuser_get()
    {
    	$this->load->model("user_db");
    	
        if(!$this->get('id'))
        {
        	
        	//example http://api2.pricenista.com/user/updateuser/fb_id/mitali/fb_email/mitali%40gmail.com/fb_name/mitali/
        	
        	// insert record
        	$user = array(
        		"fb_id"=> $this->get('fb_id'),
        	    "fb_email"=>$this->get('fb_email'),
        	    "fb_name"=> $this->get('fb_name')
        	);
        	
        	//$this->user_db->insertuser($user);
        	
        	$this->response($user, 400);
        }
        
        else {
        	//update
        	
        	//example http://api2.pricenista.com/user/updateuser/id/1/fb_id/mitali/fb_email/mitali%40gmail.com/fb_name/mitali/
        	
        	
        	// insert record
        	$user = array( 
        		"fb_id"=> $this->get('fb_id'),
        	    "fb_email"=>$this->get('fb_email'),
        	    "fb_name"=> $this->get('fb_name')
        	);
        	
        	$this->user_db->updateuser($user,$this->get('id'));
        	
        	$this->response($user, 400);
        	}

        
        }
*/
    }
    
  
    


