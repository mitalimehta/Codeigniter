<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Message extends REST_Controller {

	
	/*** 
		Get Conversations for  user id 
		example http://api2.pricenista.com/message/getconversations/user_id/1/

	****/
	public function getconversations_get(){

		$userID = $this->get('user_id');
		
		if($userID){
			
			$conversations['user_id'] = $userID;
		
			$conversations['conversations'] = $this->datahandler->getUserconversations($userID);
			
			$this->response($conversations, 200);
		}
		else{
			
			$this->response(NULL, 404);
		
		
		}
	}

	/*** 
		send message to a friend using GET method
		example api2.pricenista.com/message/sendmessage/sender_id/1/receiver_id/2/message/test/

	****/
	public function sendmessage_get(){

		$message['sender_id']    = $this->get('sender_id');
		$message['receiver_id']  = $this->get('receiver_id');
		$message['message']      = $this->get('message');
		$message['chat_id']      = $message['sender_id'] . '_' . $message['receiver_id'];
		//$message['created']      = $this->datahandler->getCurrentDateTime();

		if(!empty($message['sender_id']) && !empty($message['receiver_id']) && !empty($message['message']))
		{
			if($message['sender_id'] === $message['receiver_id']){
				$this->response('sender id and receiver id are same', 200);
			}

			if($this->datahandler->sendmessage($message))
			{	
				$this->response('message sent', 200);
			}

		}
		else{
			
			$this->response('missing data', 200);
		}
	}

	/*** 
		send message to a friend using POST method
		example api2.pricenista.com/message/sendmessagepost/

	****/
	public function sendmessagepost_post(){

		$message['sender_id']    = $this->post('sender_id');
		$message['receiver_id']  = $this->post('receiver_id');
		$message['message']      = $this->post('message');
		$message['chat_id']      = $message['sender_id'] . '_' . $message['receiver_id'];
		//$message['created']      = $this->datahandler->getCurrentDateTime();

		if(!empty($message['sender_id']) && !empty($message['receiver_id']) && !empty($message['message']))
		{
			if($message['sender_id'] === $message['receiver_id']){
				$this->response('sender id and receiver id are same', 200);
			}

			if($this->datahandler->sendmessage($message))
			{	
				$this->response('message sent', 200);
			}

		}
		else{
			
			$this->response('missing data', 200);
		}
	}


}