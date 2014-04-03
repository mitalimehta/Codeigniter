<?php

class User_db extends CI_Model{


	function getAllusers(){
		 $query = $this->db->query("select * from user");
		 return $query->result();
	}

        function getUser($param, $id_type = 1){
                 if($id_type <> 1){
                    $query = $this->db->get_where('user', array('ext_id =' => $param));
                 }else{
                    $query = $this->db->get_where('user', array('user_id =' => $param));
                 }
                 return $query->result();
        }
	
	function insertUser($user){
		 $this->db->insert("user",$user);
	}
	
	function updateUser($user,$id){
		 $this->db->where('id', $id);
		 $this->db->update('user', $user); 
	}

	function getUserCategories($userID){
		 $query = $this->db->select('category.name as name, category.category_id as category_id, category.icon as icon ,ranking.points as points')
		                   ->from('ranking')
		                   ->where('user_id', $userID)
		                   ->join('category', 'category.category_id = ranking.category_id')
		                   ->order_by('ranking.modified', 'desc')
		                   ->get();
		 return $query->result();

	}

	function userlogin($userData){

		if($userData){

			if($userData['ext_id']){

				$data = array();

				foreach($userData as $u=>$v){

					if($u != 'ext_id' && $v != ''){
						
						$data[$u] = $v;	
					}
				}

				$this->db->where('ext_id', $userData['ext_id']);
				$this->db->update('user', $data); 

				$select = $this->db->query("select user_id,ext_id,name,email,avatar,first_name,last_name,gender,locale,location,home_town,birthdate from user where ext_id = ".$userData['ext_id']);
				$user = $select->result();

				if(count($user) > 0){
					return $user;
				}
				else{
					
					$this->db->insert("user",$userData);
					$select = $this->db->query("select user_id,ext_id,name,email,avatar,first_name,last_name,gender,locale,location,home_town,birthdate from user where ext_id = ".$userData['ext_id']);
					$user = $select->result();

					if(count($user) > 0){
						return $user;
					}
				}
			}

		}
	}
}
