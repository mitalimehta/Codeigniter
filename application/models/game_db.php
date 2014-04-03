<?php

class Game_db extends CI_Model{

	function getallcategories($category_id = 0){

		if($category_id){

			$query = $this->db->query("select * from category where parent_category = ".$category_id);
		}
		else{

			$query = $this->db->query("select * from category where parent_category = 0");
		}
			
		return $query->result();
	}

	function getCategoryName($category_id){

		$query = $this->db->query("select name from category where category_id = ".$category_id);
		
			
		return $query->result();
	}

	function storepoints($user_id,$category_id,$points){

	
		$select = $this->db->query("select points from ranking where category_id = ".$category_id." and user_id = ".$user_id);
		
			
		$ranking = $select->result();

		//if data exists then update current point with currentpoints+10
		if( count($ranking) > 0 ){

			
			$updatearray = array('points' => $ranking[0]->points+$points);

			$this->db->where('category_id', $category_id);
			$this->db->where('user_id', $user_id);
			$this->db->update('ranking', $updatearray); 

		
		}

		//else insert points
		else{

			$rankingarray = array(

								'category_id' => $category_id,
								'user_id'    => $user_id,
								'points'     => $points

							);
			
			return $this->db->insert('ranking', $rankingarray); 
		}

	}

	function addTotalQuestion($user_id){

		$total_question = $this->db->query("select lifetime_total_questions from user where user_id = ".$user_id)->result();

		if( count($total_question) > 0 ){

			$updatearray = array('lifetime_total_questions' => $total_question[0]->lifetime_total_questions+1);

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $updatearray); 

		}
	}

	function addCorrectAnswer($user_id){

		$correct_answers = $this->db->query("select lifetime_correct_answers from user where user_id = ".$user_id)->result();

		if( count($correct_answers) > 0 ){

			$updatearray = array('lifetime_correct_answers' => $correct_answers[0]->lifetime_correct_answers+1);

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $updatearray); 

		}
	}

	function setreward($user_id,$key){
			$updatearray = array('last_reward' => $key);

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $updatearray); 
	}

	function setmessage($user_id,$key){
			$updatearray = array('last_message' => $key);

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $updatearray); 
	}
	

	function resetuserpoints($user_id){

		if($user_id){
			$this->db->where('user_id', $user_id);
			$this->db->delete('ranking'); 


			$updatearray = array('lifetime_total_questions' => 0,
								 'lifetime_correct_answers'=>0,
								 'last_reward'=>0,
								 'last_message'=>0);

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $updatearray); 
		}

	}

	function resetalluserspoints(){

			$this->db->truncate('ranking'); 


			$updatearray = array('lifetime_total_questions' => 0,
								 'lifetime_correct_answers'=>0,
								 'last_reward'=>0,
								 'last_message'=>0);

			
			$this->db->update('user', $updatearray);
	}

}
