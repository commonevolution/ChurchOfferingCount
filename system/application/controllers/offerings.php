<?php

class Offerings extends Controller {

	//Offerings Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_offerings');
		$data = array();
		$data['results'] = $this->model_offerings->lister();
		$this->load->view('offerings_list', $data);
	}
	
	//Offerings Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_offerings');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			// Add the offerings
			$c = 0;

			foreach($this->input->post('item') as $row) {
				
				if(!$row['offering_type_id'] || !$row['service_id'] || !$row['date']) {

				} else {
					$data = array();
					
					$data['offering_type_id'] = $row['offering_type_id'];
					$data['service_id'] = $row['service_id'];
					$data['date'] = $row['date'];
					$data['user_id'] = $this->tank_auth->get_user_id();
					
					$action = $this->model_offerings->add($data);

					if($action) { $c++; }
				}	
			}

			//Success/error message
			if ($c) {
				echo $c.' offering(s) added successfully';
			} else {
				echo 'No offerings were added';
			}
		}
 		
		//Show the add form
		$this->load->view('offerings_add');
    }


	//Offerings Controller: update
	function update() {
		$this->load->model('model_offerings');

		$post_id = $this->input->post('id');
		$explode_id = explode("_", $post_id);
		
		$value	= $this->input->post('value');
		$id		= $explode_id[1];
		$type	= $explode_id[0];
		
		$data = array();
		
		switch ($type){
			case 'date':
						$data['date'] = date('Y-m-d', strtotime($value));
						$result = $this->model_offerings->update($id, $data);
						echo date('D, M j', strtotime($data['date']));
						break;
			case 'service':
						$data['service_id'] = $value;
						$result = $this->model_offerings->update($id, $data);
						
						//get the service name
						$this->load->model('model_services');
						$service = $this->model_services->get_service_name( $value );

						echo $service;
						break;
			case 'offeringtype':
						$data['offering_type_id'] = $value;
						$result = $this->model_offerings->update($id, $data);
						
						//get the offering_type name
						$this->load->model('model_offering_types');
						$offering_type = $this->model_offering_types->get_offering_type_name( $value );

						echo $offering_type;
						break;
		}
	}


	//Offerings Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_offerings');
		$result_reports = $this->model_offerings->check_related_reports( $id );
		$result_records = $this->model_offerings->check_related_records( $id );
		
		if( $result_reports==FALSE && $result_records==FALSE ) {
			$this->model_offerings->delete($id);
			echo 'true';
		} elseif( $result_reports==FALSE && $result_records==TRUE ) {
			echo 'false - record';
		} elseif( $result_records==FALSE && $result_reports==TRUE ) {
			echo 'false - report';
		} else {
			echo 'false';
		}

	}


	
	//Offerings Controller: lister
	function lister() {
		//Load the list data
		$this->load->model('model_offerings');
		
		$filter = array();
		$filter['date'] = $this->uri->segment(3);
		
		$data = array();
		$data['results'] = $this->model_offerings->lister( $filter );

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('offerings_list', $data); //Load html view of search results
		}
	}
	
	
	
	//Reports Controller: view
	function view() {
		
		$this->load->library('tank_auth');
		$this->load->model('model_offerings');
		$this->load->model('model_records');
		
		//Get the Offering details
		$offering_id = $this->uri->segment(3);
		
		$data = array();
		$data['offering']			= $this->model_offerings->get( $offering_id );
		$data['offering']['id']		= $data['offering']['offering_id'];
		$data['offering']['date']	= date('D, M j', strtotime($data['offering']['offering_date'])); 	
		$data['offering']['name']	= $data['offering']['offering_name'];
		
		//Get records for this offering
		$data['records'] = $this->model_records->lister( $data['offering']['id'] );
		
		//Show the add form
		$this->load->view('offerings_view', $data);

    }



	//Offerings Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_offerings');
		$offerings = $this->model_offerings->list_autocomplete($keyword);
		
		if(count($offerings) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach( $offerings as $offering ) {
				
				$date = date('D, M j', strtotime($offering->offering_date));
				$offering_name = $offering->service_name . ' ' . $offering->offering_type_name;
				
				$data['message'][] = array(	'id' =>	$offering->offering_id,
											'value' =>	$offering_name,
											'date' => $date
											);
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('offerings_list', $data); //Load html view of search results
		}
	}
}