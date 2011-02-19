<?php

class Methods extends Controller {

	//Methods Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_methods');
		$data = array();
		$data['results'] = $this->model_methods->lister();
		$this->load->view('methods_list', $data);
	}


	//Methods Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_methods');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			// Add the methods
			$data = array();
			if(!$this->input->post('name')) {
				$action = '';
			} else {
				$data['name'] = $this->input->post('name');
				$action = $this->model_methods->add($data);
			}
			
			//Success/error message
			if (!$action) {
				echo 'Hmmm...that didn\'t work so well.';
			} else {
				echo 'Giving method added successfully!';
			}
		}
 		
		//Show the list
		$this->load->view('methods_add');
    }


	//Methods Controller: update
	function update() {
		$this->load->model('model_methods');

		$id = $this->uri->segment(3);
	 
		if ($this->input->post('submit')) {
			$data['method_id'] = $this->input->post('method_id');
			$data['name'] = $this->input->post('name');
	 
			$this->model_methods->update($data);
	 
			$data['methods'] = $this->model_methods->lister();
			$this->load->view('methods_list', $data);
		} else {
			$data = array('method_id' => $id);
			$this->load->view('methods_update', $data);
		}
	}


	//Methods Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_methods');
		$result = $this->model_methods->check_related_records( $id );
		
		if( $result==FALSE ) {
			$this->model_methods->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//Methods Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_methods');
		$methods = $this->model_methods->list_autocomplete($keyword);
		
		if(count($methods) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($methods as $method){
				$data['message'][] = array(	'id' =>	$method->method_id,
											'value' =>	$method->name
											);
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('methods_list', $data); //Load html view of search results
		}
	}
	
	
	//Methods Controller: list_dropdown
	function list_dropdown() {
		
		//Pull the list for the dropdown
		$this->load->model('model_methods');
		$methods = $this->model_methods->list_dropdown();
		
		echo json_encode($methods);
	}
}