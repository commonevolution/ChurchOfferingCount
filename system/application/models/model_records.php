<?php

class Model_records extends Model {
 
    
	//FUNCTION: Base model function
	function Model_records() {
		parent::Model(); 
		$this->load->database();
		
		// Pagination defaults
		$this->pagination_enabled = FALSE;
		$this->pagination_per_page = 10;
		$this->pagination_num_links = 5;
		$this->pager = '';
		
		/**
		 *    bool $this->raw_data		
		 *    Used to decide what data should the SQL queries retrieve if tables are joined
		 *     - TRUE:  just the field names of the records table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
		$this->raw_data = FALSE;  
	}

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('records', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('record_id', $id);
		$this->db->update('records', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('record_id', $id);
		$this->db->delete('records');
	}


	//FUNCTION: get (for single record)
	function get($id) {
		
		$this->db->select('people.first_name, people.last_name, methods.name, offerings.service_id, offerings.offering_type_id, offerings.date, records.amount');
		$this->db->from('records', 'people', 'methods', 'offerings');
		$this->db->join('people', 'people.people_id = records.people_id', 'left');
		$this->db->join('methods', 'methods.method_id = records.method_id', 'left');
		$this->db->join('offerings', 'offerings.offering_id = records.offering_id', 'left');
		$this->db->where('record_id', $id);
		$query = $this->db->get();
		
		foreach ($query->result() as $row) {
			$records[] = array(
				'record_id' => $id,
				'first_name' => $row->first_name,
				'last_name' => $row->last_name,
				'method_name' => $row->name,
				'service_id' => $row->service_id,
				'offering_type_id' => $row->offering_type_id,
				'offering_date' => $row->date,
				'amount' => $row->amount
			);
		}
		return $records;
	}

	
	//FUNCTION: lister (for multiple records)
	function lister( $offering_id = FALSE ) {
		
		$this->db->select('records.record_id,people.first_name, people.last_name, methods.name, offerings.service_id, offerings.offering_type_id, offerings.date, records.amount, offering_types.name AS offering_type_name, services.name AS service_name');
		$this->db->from('records');
		$this->db->join('people', 'people.people_id = records.people_id', 'left');
		$this->db->join('methods', 'methods.method_id = records.method_id', 'left');
		$this->db->join('offerings', 'offerings.offering_id = records.offering_id', 'left');
		$this->db->join('services', 'services.service_id = offerings.service_id', 'left');
		$this->db->join('offering_types', 'offering_types.offering_type_id = offerings.offering_type_id', 'left');
		if( $offering_id ) {
			$this->db->where( 'offerings.offering_id', $offering_id );
		}
		$this->db->order_by( 'record_id', 'ASC' );
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$results[] = array(
					'record_id' => $row['record_id'],
					'first_name' => $row['first_name'],
					'last_name' => $row['last_name'],
					'method_name' => $row['name'],
					'service_name' => $row['service_name'],
					'offering_type_name' => $row['offering_type_name'],
					'offering_date' => date('D, M j', strtotime($row['date'])),
					'amount' => number_format($row['amount'],2)
				);
			}
			return $results;
		} else {
			return array();
		}
	}
	


	//FUNCTION: get_offering_total
	function get_offering_total( $offering_id, $filter = FALSE ) {
		
		//Had to do this because the queries were stacking on the previous!
		$this->db->flush_cache();
		
		if($filter==FALSE) {
			$this->db->select_sum('amount');
			$this->db->from('records');
			$this->db->where('records.offering_id', $offering_id);
		} else {
			$this->db->select_sum('amount');
			$this->db->from('records');
			$this->db->join('offerings', 'offerings.offering_id = records.offering_id', 'left');
			$this->db->where('records.offering_id', $offering_id);
		}
		
		//Check the filter
		if(isset($filter['method'])) {
			$this->db->where('records.method_id', $filter['method']);
		} elseif(isset($filter['offering_type'])) {
			$this->db->where('offerings.offering_type_id', $filter['offering_type']);
		} elseif(isset($filter['service'])) {
			$this->db->where('offerings.service_id', $filter['service']);
		}

		// Get the results
		$query = $this->db->get();
		$row = $query->row();
		$amount = $row->amount;
		
		return $amount;
	}

}