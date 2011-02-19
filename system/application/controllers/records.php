<?php

class Records extends Controller {

	//Records Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }
		
		//Load the lister data
		$this->load->model('model_records');
		$data = array();
		$data['results'] = $this->model_records->lister();
		$this->load->view('records_list', $data);
	}
	
	//Records Controller: add
	function add() {
		$this->load->library('tank_auth');
		$this->load->model('model_offerings');
		
		//Is there an offering filter set?
		if( $this->uri->segment(3) ) {
		
			$offering_id = $this->uri->segment(3);
			$set_offering_id = $this->uri->segment(3);
		
		} elseif( $this->input->post('offering_id') ) {
		
			$offering_id = $this->input->post('offering_id');
			$set_offering_id = $this->input->post('offering_id');
		
		} else {
			$offering_id = '';
			$set_offering_id = '';
		}
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
	 		// Add the records
			$this->load->model('model_records');
			$c = 0;
			
			foreach($this->input->post('item') as $row) {
				
				if( !$offering_id ) {
					$offering_id = $row['offering_id'];
				}
				
				if((!$row['person_id'] && !$row['person_name']) || !$offering_id || !$row['method_id'] || !$row['amount']) {

				} else {
					
					if( !$offering_id ) {
						$offering_id = $row['offering_id'];
					}
					
					//Check if person exists
					if(!$row['person_id'] && $row['person_name']) {
					
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
						
					} else {
						
						$person_id = $row['person_id'];
					
					}
					
					//Setup record data for model
					$data['people_id'] = $person_id;
					$data['offering_id'] = $offering_id;
					$data['method_id'] = $row['method_id'];
					$data['amount'] = sprintf("%.2f", ltrim($row['amount'],0));
					$data['added'] = date('Y-m-d h:i:s');
					$data['user_id'] = $this->tank_auth->get_user_id();
					$action = $this->model_records->add($data);

					if($action) { $c++; }
				}	
			}

			//Success/error message
			if ($c) {
				echo $c.' record(s) added successfully';
			} else {
				echo 'No record(s) were added';
			}
			
		}
 		
		//Get the offering details
		if( $set_offering_id > 0 ) {
			$data['offering']						= $this->model_offerings->get( $set_offering_id );
			$data['offering']['id']					= $data['offering']['offering_id'];
			$data['offering']['date']				= date('D, M j', strtotime($data['offering']['offering_date'])); 	
			$data['offering']['name']				= $data['offering']['offering_name'];
			$data['offering']['set_offering_id']	= $set_offering_id;
		} else {
			$data['offering'] = array();
		}
		
		//////Using autocomplete instead of dropdowns	
		//Pull the people list for the dropdown
		//$this->load->model('model_people');
		//$data['people'] = $this->model_people->list_dropdown();

		//Show the add form
		$this->load->view('records_add', $data);
    }


	//Records Controller: update
	function update() {
		$this->load->model('model_records');

		$post_id = $this->input->post('id');
		$explode_id = explode("_", $post_id);
		
		$value	= $this->input->post('value');
		$id		= $explode_id[1];
		$type	= $explode_id[0];
		
		$data = array();
		
		switch ($type){
			case 'amount':
						$data['amount'] = sprintf("%.2f", ltrim($value,0));
						$data['modified'] = date('Y-m-d h:i:s');
						$result = $this->model_records->update($id, $data);
						echo $data['amount'];
						break;
			case 'method':
						$data['method_id'] = $value;
						$data['modified'] = date('Y-m-d h:i:s');
						$result = $this->model_records->update($id, $data);
						
						//get the method name
						$this->load->model('model_methods');
						$method = $this->model_methods->get_method_name( $value );

						echo $method;
						break;
		}
	}

	//Records Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_records');
		//$result = $this->model_records->check_related_reports( $id );
		$result = FALSE;
		
		if( $result==FALSE ) {
			$this->model_records->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//Records Controller: get_offering_total
	function get_offering_total($offering) {
		$this->load->model('model_records');
		$data = array();
		$data['results'] = $this->model_records->get_offering_total();
	}
	

	//Records Controller: get_method_total
	function get_method_total($method) {
		$this->load->model('model_records');
		$data = array();
		$data['results'] = $this->model_records->get_method_total();
	}
}