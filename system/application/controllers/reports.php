<?php

class Reports extends Controller {

	//Reports Controller: index
	function index() {
		
		//Check for user session
		$this->load->library('tank_auth');
		if (!$this->tank_auth->is_logged_in()) { redirect('/auth/login/'); }  
		
		//Load the lister data
		$this->load->model('model_reports');
		$data = array();
		$data['results'] = $this->model_reports->lister();
		$this->load->view('reports_list', $data);
	}
	
	//Reports Controller: add
	function add() {
		$this->load->library('tank_auth');
 		$this->load->model('model_reports');
		
		// Check if form is submitted
        if ($this->input->post('submit')) {
			
			//Add the Report with Offerings
			if(!$this->input->post('date')) {
				$r = '';
			} else {				
				//Add the Report record
				$data['date'] = $this->input->post('date');
				$report_id = $this->model_reports->add($data);
				
				//Add the Offerings
				if(!$this->input->post('item')) {
					$c = 0;
				} else {
					$c = 0;
					
					foreach($this->input->post('item') as $row) {
						
						if(!$row['offering_id']) {
							
						} else {
							$data = array();
							
							$data['offering_id'] = $row['offering_id'];
							$data['report_id'] = $report_id;
							$data['user_id'] = $this->tank_auth->get_user_id();
			
							$offering = $this->model_reports->add_offerings($data);
		
							if($offering) { $c++; }
						}
					}					
				}			
			}
			
			//Success/error message
			if (!$report_id) {
				$data['message'] = 'Report was added not added';
			} else {
				$data['message'] = 'Report added successfully with ' . $c . ' offering(s)';
			}
		
			//Show the list
			redirect('/reports/', $data);
		
		} else {
 		
			//Show the add form
			$this->load->view('reports_add');
		
		}
    }

	
	//Reports Controller: add_offerings
	function add_offerings() {
		$this->load->model('model_reports');
		$this->load->model('model_offerings');
		
		// Check if form is submitted
        if ( $this->input->post('submit') ) {
			
			$report_id = $this->input->post('report_id');
			
			//Add the Offerings
			if(!$this->input->post('item')) {
			
			} else {
				
				foreach($this->input->post('item') as $row) {
					
					if(!$row['offering_id']) {
						
					} else {
						$data = array();
						
						$data['offering_id'] = $row['offering_id'];
						$data['report_id'] = $report_id;
						$data['user_id'] = $this->tank_auth->get_user_id();
			
						$offering = $this->model_reports->add_offerings($data);
			
					}
				}					
			}
			
			redirect('reports/view/'.$report_id);
		
		} else {
				
			//Get the Report details
			$report_id = $this->uri->segment(3);
			$data = array();
			$data['report'] = $this->model_reports->get( $report_id );
			$data['report']['id'] = $data['report']['report_id']; 	
			$data['report']['date'] = date('D, M j', strtotime($data['report']['date'])); 	
			
			//Get offerings for this report
			$data['existing'] = $this->model_reports->get_report_offerings( $report_id );
			foreach( $data['existing'] as $key => $value ) {
	
				//Remap some variables
				$data['existing'][$key]['offering_id']		= $value['offering_id'];
				$data['existing'][$key]['offering_name']	= $value['offering_name'];
				$data['existing'][$key]['offering_date']	= date('D, M j', strtotime($value['offering_date']));
				
				$filter_available[] = $value['offering_id'];
			}
			
			//Get available offerings (excluding existing)
			$filter['date'] = $data['report']['date'];
			$filter['report_offerings'] = $filter_available;
			
			$data['available'] = $this->model_offerings->lister( $filter );
			
			if( count($data['available']) > 0 ) {
				foreach( $data['available'] as $key => $value ) {
		
					//Remap some variables
					$data['available'][$key]['offering_id']		= $value['offering_id'];
					$data['available'][$key]['offering_name']	= $value['offering_name'];
					$data['available'][$key]['offering_date']	= date('D, M j', strtotime($value['date']));
				}
			} else {
				$data['available'] = 0;
			}
			
			//Show the add offerings form
			$this->load->view('reports_add_offerings', $data);
		
		}
	}


	//Reports Controller: update
	function update() {
		$this->load->model('model_reports');

		$post_id = $this->input->post('id');
		$explode_id = explode("_", $post_id);
		
		$value	= $this->input->post('value');
		$id		= $explode_id[1];
		$type	= $explode_id[0];
		
		$data = array();
		
		switch ($type){
			case 'date':
						$data['date'] = date('Y-m-d', strtotime($value));
						$result = $this->model_reports->update($id, $data);
						echo date('D, M j', strtotime($data['date']));
						break;
		}
	}


	//Reports Controller: delete
	function delete() {
		$id = $this->uri->segment(3);
		$this->load->model('model_reports');
		$result = $this->model_reports->check_related_offerings( $id );
		
		if( $result==FALSE ) {
			$this->model_reports->delete($id);
			echo 'true';
		} else {
			echo 'false';
		}
	}


	//Reports Controller: remove_offerings
	function remove_offerings() {
		$this->load->model('model_reports');

		$id = $this->uri->segment(3);
		$this->model_reports->remove_offerings($id);
		
		//need to remove related objects
	}


	//Reports Controller: view
	function view() {
		
		$this->load->library('tank_auth');
		$this->load->model('model_reports');
		$this->load->model('model_records');
		
		//Get the Report details
		$report_id = $this->uri->segment(3);
		$data = array();
		$data['report'] = $this->model_reports->get( $report_id );
		$data['report']['date'] = date('D, M j', strtotime($data['report']['date'])); 	
		
		//Get offerings for this report
		$data['offerings'] = $this->model_reports->get_report_offerings( $report_id );
		
		//Setup grand total variables
		$credit_grand_total = 0;
		$deposit_grand_total = 0;
		$report_grand_total = 0;
		
		$love_grand_total = 0;
		$tithe_grand_total = 0;
		$kb_grand_total = 0;
		
		$cash_grand_total = 0;
		$checks_grand_total = 0;
		$amex_grand_total = 0;
		$visa_grand_total = 0;
		$mastercard_grand_total = 0;
		$discover_grand_total = 0;
		
		//Get totals by offering
		foreach( $data['offerings'] as $key => $value ) {
		
			//Remap some variables
			$data['offerings'][$key]['offering_date'] = date('D, M j', strtotime($value['offering_date']));
			
			//Set method filters
			$cash_filter = array( 'method' => '5' );
			$checks_filter = array( 'method' => '6' );
			$amex_filter = array( 'method' => '3' );
			$visa_filter = array( 'method' => '1' );
			$mastercard_filter = array( 'method' => '2' );
			$discover_filter = array( 'method' => '4' );
			
			//Get totals for each giving method
			$offering_cash = $this->model_records->get_offering_total( $value['offering_id'], $cash_filter);
			$offering_checks = $this->model_records->get_offering_total( $value['offering_id'], $checks_filter);
			$offering_amex = $this->model_records->get_offering_total( $value['offering_id'], $amex_filter);
			$offering_visa = $this->model_records->get_offering_total( $value['offering_id'], $visa_filter);
			$offering_mastercard = $this->model_records->get_offering_total( $value['offering_id'], $mastercard_filter);
			$offering_discover = $this->model_records->get_offering_total( $value['offering_id'], $discover_filter);
			
			//Set offering type filters
			$love_filter = array( 'offering_type' => '2' );
			$tithe_filter = array( 'offering_type' => '1' );
			$kb_filter = array( 'offering_type' => '6' );
			
			//Get totals by offering type
			$offering_love = $this->model_records->get_offering_total( $value['offering_id'], $love_filter );
			$offering_tithe = $this->model_records->get_offering_total( $value['offering_id'], $tithe_filter );
			$offering_kb = $this->model_records->get_offering_total( $value['offering_id'], $kb_filter );
			
			//Calculate totals for the offering
			$offering_credit_total = $offering_discover + $offering_amex + $offering_visa + $offering_mastercard;
			$offering_deposit_total = $offering_cash + $offering_checks;
			$offering_total = $offering_credit_total + $offering_deposit_total;
			
			
			//Set array values for the offering
			$data['offerings'][$key]['cash'] = number_format($offering_cash, 2);
			$data['offerings'][$key]['checks'] = number_format($offering_checks, 2);
			$data['offerings'][$key]['amex'] = number_format($offering_amex, 2);
			$data['offerings'][$key]['visa'] = number_format($offering_visa, 2);
			$data['offerings'][$key]['mastercard'] = number_format($offering_mastercard, 2);
			$data['offerings'][$key]['discover'] = number_format($offering_discover, 2);
			
			$data['offerings'][$key]['love'] = number_format($offering_love, 2);
			$data['offerings'][$key]['tithe'] = number_format($offering_tithe, 2);
			$data['offerings'][$key]['kb'] = number_format($offering_kb, 2);
			
			$data['offerings'][$key]['credit_total'] = number_format($offering_credit_total, 2);
			$data['offerings'][$key]['deposit_total'] = number_format($offering_deposit_total, 2);
			$data['offerings'][$key]['offering_total'] = number_format($offering_total, 2);
			
			//Add together offering totals for the report grand totals
			$cash_grand_total += $offering_cash;
			$checks_grand_total += $offering_checks;
			$amex_grand_total += $offering_amex;
			$visa_grand_total += $offering_visa;
			$mastercard_grand_total += $offering_mastercard;
			$discover_grand_total += $offering_discover;
			
			$love_grand_total += $offering_love;
			$tithe_grand_total += $offering_tithe;
			$kb_grand_total += $offering_kb;
			
			$credit_grand_total += $offering_credit_total;
			$deposit_grand_total += $offering_deposit_total;
			$report_grand_total += $offering_total;
		}
		
		//Set array values for the report
		$data['totals']['credit_grand_total'] = number_format($credit_grand_total,2);
		$data['totals']['deposit_grand_total'] = number_format($deposit_grand_total,2);
		
		$data['totals']['love_grand_total'] = number_format($love_grand_total,2);
		$data['totals']['tithe_grand_total'] = number_format($tithe_grand_total,2);
		$data['totals']['kb_grand_total'] = number_format($kb_grand_total,2);
		
		$data['totals']['cash_grand_total'] = number_format($cash_grand_total,2);
		$data['totals']['checks_grand_total'] = number_format($checks_grand_total,2);
		$data['totals']['amex_grand_total'] = number_format($amex_grand_total,2);
		$data['totals']['visa_grand_total'] = number_format($visa_grand_total,2);
		$data['totals']['mastercard_grand_total'] = number_format($mastercard_grand_total,2);
		$data['totals']['discover_grand_total'] = number_format($discover_grand_total,2);
		
		$data['totals']['report_grand_total'] = number_format($report_grand_total,2);
		
		//Show the add form
		$this->load->view('reports_view', $data);
	
    }


	//Reports Controller: list_autocomplete
	function list_autocomplete() {
		//Pull the list for the autocomplete
		$keyword = $this->input->post('term');

		$this->load->model('model_reports');
		$reports = $this->model_reports->list_autocomplete($keyword);
		
		if(count($reports) > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($reports as $offering_type){
				$data['message'][] = array(	'id' =>	$report_id->report_id,
											'value' =>	$date->date );
            }
        } else {
			$data['response'] = 'false'; //Set default response
		}

		if('IS_AJAX') {
			echo json_encode($data); //echo json string if ajax request
		} else {
			$this->load->view('reports_list', $data); //Load html view of search results
		}
	}

}