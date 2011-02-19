<?php

class Offering_types extends Controller {

	//Offering Types Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_offering_types');
		$data = array();
		$data['results'] = $this->model_offering_types->lister();
		$this->load->view('offering_types_list', $data);
	}
	
	//Offering Types Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_offering_types');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			// Add the methods
			$data = array();
			if(!$this->input->post('name')) {
				$action = '';
			} else {
				$data['name'] = $this->input->post('name');
				$action = $this->model_offering_types->add($data);
			}
			
			//Success/error message
			if (!$action) {
				echo 'Hmmm...that didn\'t work so well.';
			} else {
				echo 'Offering Type added successfully!';
			}
		}
 		
		//Show the add form
		$this->load->view('offering_types_add');
    }


	//Offering Types Controller: update
	function update() {
		$this->load->model('model_offering_types');

		$id = $this->uri->segment(3);
	 
		if ($this->input->post('submit')) {
			$data['offering_type_id'] = $this->input->post('offering_type_id');
			$data['name'] = $this->input->post('name');
	 
			$this->model_offering_types->update($data);
	 
			$data['records'] = $this->model_offering_types->get();
			$this->load->view('offering_types_list', $data);
		} else {
			$data = array('offering_type_id' => $id);
			$this->load->view('offering_types_update', $data);
		}
	}


	//Offering Types Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_offering_types');
		$result = $this->model_offering_types->check_related_offerings( $id );
		
		if( $result==FALSE ) {
			$this->model_offering_types->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//Offering Types Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_offering_types');
		$offering_types = $this->model_offering_types->list_autocomplete($keyword);
		
		if(count($offering_types) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($offering_types as $offering_type){
				$data['message'][] = array(	'id' =>	$offering_type->offering_type_id,
											'value' =>	$offering_type->name );
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('offering_types_list', $data); //Load html view of search results
		}
	}


	//Offering Types Controller: list_dropdown
	function list_dropdown() {
		
		//Pull the list for the dropdown
		$this->load->model('model_offering_types');
		$offering_types = $this->model_offering_types->list_dropdown();
		
		echo json_encode($offering_types);
	}
}