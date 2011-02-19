<?php
class People extends Controller {

	//People Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_people');
		$data = array();
		$data['results'] = $this->model_people->lister();
		$this->load->view('people_list', $data);
	}
	

	//Methods Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_people');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			// Add the people
			$c = 0;

			foreach($this->input->post('item') as $row) {
				
				if(!$row['person_name']) {

				} else {
					//Parse person name
					$this->load->library('nameparser');
					$this->nameparser->setFullName($row['person_name']);
					$this->nameparser->parse();
						
					//Setup person data for model
					$name_data = array();
					$name_data['first_name'] = $this->nameparser->getFirstName($row['person_name']);
					$name_data['last_name'] = $this->nameparser->getLastName($row['person_name']);
					$name_data['added'] = date('Y-m-d h:i:s');
					$name_data['user_id'] = $this->tank_auth->get_user_id();
					
					//Add the person
					$this->load->model('model_people');
					$person_id = $this->model_people->add($name_data);

					if($action) { $c++; }
				}	
			}

			//Success/error message
			if ($c) {
				echo $c.' people added successfully';
			} else {
				echo 'No people were added';
			}
		}
 		
		//Show the add form
		$this->load->view('people_add');
    }


	//People Controller: update
	function update() {
		$this->load->model('model_people');

		$post_id = $this->input->post('id');
		$explode_id = explode("_", $post_id);
		
		$value	= $this->input->post('value');
		$id		= $explode_id[1];
		$type	= $explode_id[0];
		
		$data = array();
		
		switch ($type){
			case 'name':
				//Parse person name
				$this->load->library('nameparser');
				$this->nameparser->setFullName($value);
				$this->nameparser->parse();
					
				//Setup person data for model
				$data = array();
				$data['first_name']	= $this->nameparser->getFirstName($value);
				$data['last_name']	= $this->nameparser->getLastName($value);
				$data['modified']	= date('Y-m-d h:i:s');
				$data['user_id']	= $this->tank_auth->get_user_id();
				
				//Add the person
				$this->load->model('model_people');
				$this->model_people->update( $id, $data );

				echo $value;
				break;
		}
	}


	//People Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_people');
		$result = $this->model_people->check_related_records( $id );
		
		if( $result==FALSE ) {
			$this->model_people->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//People Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_people');
		$people = $this->model_people->list_autocomplete($keyword);
		
		if(count($people) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($people as $person){
				$data['message'][] = array(	'id' =>	$person->people_id,
											'value' =>	$person->first_name . ' ' . $person->last_name );
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}
		
		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('methods_list',$data); //Load html view of search results
		}
	}
}