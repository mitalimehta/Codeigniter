<?php

class Message_db extends CI_Model{

	private $_limit = 3;
	
	function getLatestConversation($userID)
	{
	
			$this->db->select("message.message_id as message_id, message.sender_id as friend_id, user.name as friend_name, user.first_name as friend_first_name, user.last_name AS friend_last_name , user.avatar as friend_picture_url , message.message as lastmessage,message.created as date");
			$this->db->from('user');
			$this->db->where('message.receiver_id', $userID);
			$this->db->join('message', 'user.user_id = message.sender_id');
			
			$this->db->order_by("message.created", "desc"); 

			//$this->db->distinct('message.sender_id');	
			//$this->db->group_by('message.sender_id');


			$this->db->limit($this->_limit);
			
			$query = $this->db->get();
			
			return $query->result();
			
	}

	function sendmessage($message){

		 //$this->db->set('created',now());	
		 //$message['created'] = now();
 		 return $this->db->insert('message', $message); 
		 
	}
}