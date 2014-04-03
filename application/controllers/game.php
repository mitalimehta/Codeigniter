<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Game extends REST_Controller {

	
	/***** 
		send message to a friend using POST method
		example http://api2.pricenista.com/game/getallcategories/
		http://api2.pricenista.com/game/getallcategories/category_id/4/
	****/

	public function getallcategories_get(){

		//echo $this->config->item('sitename');

		$categories['categories'] = $this->datahandler->getAllCategories($this->get('category_id'));
		
		/*if($this->get('category_id'))
		{
			$categories_caption = $this->datahandler->getCategoryName($this->get('category_id'));

			$categories['categories_caption'] = $categories_caption[0]->name;
		}
		else{
			$categories['categories_caption'] = 'Category';
		}*/

		foreach($categories['categories'] as $count=>$category){
			
			$subCategory = 	$this->datahandler->getAllCategories($category->category_id);

			if(count($subCategory) > 0 )
			{
				$category->isSubcategory = 1;
				$category->subcategories = $subCategory;
			}
			else{
					$category->isSubcategory = 0;
				}	
			
		}

        $this->pricenistalog->write_log(2,$categories);

		$this->response($categories, 200);
	}

    public function resetuserpoints_get(){

        $user_id        =  $this->get('user_id');  

        $this->datahandler->resetuserpoints($user_id);

        $this->response("DONE", 200);
        exit;

    }

    public function resetalluserspoints_get(){
        $this->datahandler->resetalluserspoints();

        $this->response("DONE", 200);
        exit;
    }

	public function getnextquestion_get(){

		#products array, to be removed after products API integration
               // include APPPATH.'third_party/questions.inc.php';

        $user_id        =  $this->get('user_id');   
        $category_id    =  $this->get('category_id');    

        if(empty($user_id) || empty($category_id)){
        	
        	$this->response("Mandatory parameters user_id or category_id missing", 200);
        	exit;
        }

        $user = $this->datahandler->getUserById($user_id,1);

        if(count($user) == 0){
            $this->response("Invalid USER ID", 200);
            exit;
        }

        $response = array('showRecap' => 0 , 'showMessage'=> 0, 'showReward' => 0, 'showQuestion' => 1);
		$response['user_id'] = $user_id;

	
     	$islastanswercorrect = $this->get('islastanswercorrect');
     	$lastanswerpoints    = $this->get('lastanswer_points');

     	$totalQuestions      = $this->get('total_questions');
     	$totalCorrectAnswers = $this->get('total_correct_answers');

        $recap_threshhold_total_questions = $this->config->item('recap_threshhold_total_questions');
        $recap_threshold_correct_answer   = $this->config->item('recap_threshold_correct_answer');

        /**************************************************/
        /********   Store Points and  counters    *********/
        /**************************************************/

        if($islastanswercorrect == 1 && $lastanswerpoints > 0){
        	$this->datahandler->storepoints($user_id,$category_id,$lastanswerpoints);
			$this->datahandler->addCorrectAnswer($user_id);
        }

        if($totalQuestions > 0){
        	$this->datahandler->addTotalQuestion($user_id);	
        }

        $category_points = $this->datahandler->getUsercategories($user_id);   
        $totalpoints = 0;

        foreach($category_points as $categorypoints){
            $totalpoints += $categorypoints->points;    
        }
        
        $response['totalpoints'] = $totalpoints;
        
        /**************************************************/
        /********   First check - Reward          *********/
        /**************************************************/
       

        $rewards_threshold = $this->config->item('rewards_threshold');


        $messages_threshold = $this->config->item('messages_threshold');

        
        $user_last_reward   = $user[0]->last_reward;
        $user_last_message  = $user[0]->last_message;

       foreach($rewards_threshold as $key=>$maxpoints){

        	if($totalpoints >= $maxpoints && $key > $user_last_reward){ // and also check if this user already received reward
        		$response['showReward'] = 1;
        		$response['rewards_text'] = $this->config->item('rewards_text');

        		// set reward for this user
                $this->datahandler->setreward($user_id,$key);

        		// generate response
                $response['question'] = $this->datahandler->getcontent($category_id); //$question_data[$this->get('counter')];

                $response['question']['correct_answer_points']         = $this->config->item('correct_answer_points');
                $response['question']['bonus_points']                  = $this->config->item('bonus_points');
                $response['question']['max_time_to_answer']            = $this->config->item('max_time_to_answer');
                $response['question']['prompt_answer_timer']           = $this->config->item('prompt_answer_timer');

        		$this->response($response, 200);

        	}
        }
        
        /**************************************************/
        /********   Second check - Message        *********/
        /**************************************************/


        foreach($messages_threshold as $key=>$maxpoints){

            if($totalpoints >= $maxpoints && $key > $user_last_message){ // and also check if this user already received reward
                $response['showMessage'] = 1;
                $response['messages_text'] = $this->config->item('messages_text');

                // set message for this user
                $this->datahandler->setmessage($user_id,$key);

                // generate response
                $response['question'] = $this->datahandler->getcontent($category_id); //$question_data[$this->get('counter')];

                $response['question']['correct_answer_points']         = $this->config->item('correct_answer_points');
                $response['question']['bonus_points']                  = $this->config->item('bonus_points');
                $response['question']['max_time_to_answer']            = $this->config->item('max_time_to_answer');
                $response['question']['prompt_answer_timer']           = $this->config->item('prompt_answer_timer');

                $this->response($response, 200);

            }
        }    

        
		/**************************************************/
        /********   Third check - Recap           *********/
        /**************************************************/

        if($totalCorrectAnswers >= $recap_threshold_correct_answer || $totalQuestions >=  $recap_threshhold_total_questions ){
        	$response['showRecap']       = 1;
        	$response['showQuestion']    = 0;

        	$this->response($response, 200);
        }


        
        /**************************************************/
        /********   else - Simple Question        *********/
        /**************************************************/
        else{

        	$response['question'] =  $this->datahandler->getcontent($category_id); //$question_data[$this->get('counter')]; 

        	$response['question']['correct_answer_points']         = $this->config->item('correct_answer_points');
			$response['question']['bonus_points']                  = $this->config->item('bonus_points');
			$response['question']['max_time_to_answer']            = $this->config->item('max_time_to_answer');
			$response['question']['prompt_answer_timer']           = $this->config->item('prompt_answer_timer');


        	$this->response($response, 200);

        }



	}

	

}