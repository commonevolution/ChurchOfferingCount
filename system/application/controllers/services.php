<?php

class Services extends Controller {

	//Services Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_services');
		$data = array();
		$data['results'] = $this->model_services->lister();
		$this->load->view('services_list', $data);
	}
	
	//Services Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_services');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			// Add the methods
			$data = array();
			if(!$this->input->post('name')) {
				$action = '';
			} else {
				$data['name'] = $this->input->post('name');
				$action = $this->model_services->add($data);
			}
			
			//Success/error message
			if (!$action) {
				echo 'Hmmm...that didn\'t work so well.';
			} else {
				echo 'Service added successfully!';
			}
		}
 		
		//Show the add form
		$this->load->view('services_add');
    }


	//Services Controller: update
	function update() {
		$this->load->model('model_services');

		$id = $this->uri->segment(3);
	 
		if ($this->input->post('submit')) {
			$data['service_id'] = $this->input->post('service_id');
			$data['name'] = $this->input->post('name');
	 
			$this->model_services->update($data);
	 
			$data['records'] = $this->model_services->lister();
			$this->load->view('services_list', $data);
		} else {
			$data = array('service_id' => $id);
			$this->load->view('services_update', $data);
		}
	}


	//Services Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_services');
		$result = $this->model_services->check_related_offerings( $id );
		
		if( $result==FALSE ) {
			$this->model_services->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//Services Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_services');
		$services = $this->model_services->list_autocomplete($keyword);
		
		if(count($services) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($services as $service){
				$data['message'][] = array(	'id' =>	$service->service_id,
											'value' =>	$service->name );
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('services_list', $data); //Load html view of search results
		}
	}
	
	
	//Services Controller: list_dropdown
	function list_dropdown() {
		
		//Pull the list for the dropdown
		$this->load->model('model_services');
		$services = $this->model_services->list_dropdown();
		
		echo json_encode($services);
	}
}