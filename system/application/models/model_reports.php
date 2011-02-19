<?php

class Model_reports extends Model {
    function Model_reports() {
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
		 *     - TRUE:  just the field names of the reports table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('reports', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('report_id', $id);
		$this->db->update('reports', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('report_id', $id);
		$this->db->delete('reports');
	}


	//FUNCTION: get (for single record)
	function get( $report_id, $get_one = false ) {
        
	    $this->db->select('report_id, date');
		$this->db->from('reports');
        $this->db->where('report_id', $report_id);

		$query = $this->db->get();

		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			return array( 
				'report_id'	=>	$row->report_id,
				'date'		=>	$row->date
			);
		} else {
			return array();
		}
	}
	
	
	//FUNCTION: get_report_offerings
	function get_report_offerings( $report_id ) {

		$this->db->start_cache();
		$this->db->select('report_offerings.report_offering_id, offerings.offering_id, offerings.date AS offering_date, offering_types.offering_type_id, offering_types.name AS offering_type_name, services.service_id, services.name AS service_name');
		$this->db->from('report_offerings');
		$this->db->join('offerings', 'report_offerings.offering_id = offerings.offering_id', 'left');
		$this->db->join('services', 'services.service_id = offerings.service_id', 'left');
		$this->db->join('offering_types', 'offering_types.offering_type_id = offerings.offering_type_id', 'left');
		$this->db->where('report_offerings.report_id', $report_id);
		$this->db->order_by( 'offering_date, service_name, offering_type_name', 'ASC' );

		// Get the results
		$query = $this->db->get();
		
		$temp_result = array();

		foreach ( $query->result() as $row ) {

		$temp_result[] = array( 
				'report_offering_id'	=>	$row->report_offering_id,
				'offering_date'			=>	$row->offering_date,
				'offering_id'			=>	$row->offering_id,
				'service_id'			=>	$row->service_id,
				'offering_type_id'		=>	$row->offering_type_id,
				'offering_name'			=>	$row->service_name . " " . $row->offering_type_name
			 );
		}

		$this->db->flush_cache(); 
		return $temp_result;
	}

	
	//FUNCTION: lister (for multiple records)
	function lister( $page = FALSE ) {
        
	    $this->db->start_cache();
		$this->db->select('report_id, date');
		$this->db->from('reports');
		$this->db->order_by( 'date', 'ASC' );

        // Get the results
		$query = $this->db->get();
		
		$temp_result = array();

		foreach ( $query->result_array() as $row ) {
			$temp_result[] = array( 
				'report_id'	=>	$row['report_id'],
				'date'		=>	date('D, M j', strtotime($row['date']))
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}


	//FUNCTION: add_offerings
    function add_offerings($data) {
	    $this->db->insert('report_offerings', $data);
    	return $this->db->insert_id();
	}


	//FUNCTION: remove_offerings
    function remove_offerings($id) {
		$this->db->where('report_offering_id', $id);
		$this->db->delete('report_offering');
	}


	//FUNCTION: list_dropdown
	function list_dropdown() {
		$this->db->select('report_id, date');
		$this->db->from('reports');
		$this->db->order_by('date', 'ASC');
		$query = $this->db->get();
		
		$data = array();
        
		$data[] = '- select report -';
        foreach($query->result_array() as $row) {
			$data['report_id'] = $row['report_id'];
        	$data['date'] = date('D, M j', strtotime($row['date']));
        }
        
        return $data;
	}
	


	//FUNCTION: list_autcomplete
	function list_autocomplete($term) {
		$this->db->select('report_id, date');
		$this->db->from('reports');
		$this->db->like('date', $term);
		$query = $this->db->get()->result();
		
		return $query;
	}




	//FUNCTION: check_related_offerings
	function check_related_offerings( $id ) {
        
		$this->db->start_cache();
		$this->db->select('offering_id');
		$this->db->from('report_offerings');
		$this->db->where('report_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}









///Unused functions
	function search ( $keyword, $page = FALSE ) {
	    $meta = $this->metadata();
	    $this->db->start_cache();
		$this->db->select( 'report_id,reports.name AS report_type_id,name,date,user.user_name AS user_id');
		$this->db->from( 'reports' );
        $this->db->join( 'reports', 'report_type_id = report_type_id', 'left' );
		$this->db->join( 'user', 'user_id = id', 'left' );


		// Delete this line after setting up the search conditions 
        die('Please see models/model_reports.php for setting up the search method.');
		
        /**
         *  Rename field_name_to_search to the field you wish to search 
         *  or create advanced search conditions here
		 */
        $this->db->where( 'field_name_to_search LIKE "%'.$keyword.'%"' );

        /**
         *   PAGINATION
         */
        if( $this->pagination_enabled == TRUE ) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('reports');
            $config['base_url']    = '/reports/search/'.$keyword.'/';
            $config['uri_segment'] = 4;
            $config['per_page']    = $this->pagination_per_page;
            $config['num_links']   = $this->pagination_num_links;
    
            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $this->pager = $this->pagination->create_links();
    
            $this->db->limit( $config['per_page'], $page );
        }

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row ) {
			$temp_result[] = array( 
				'report_id' => $row['report_id'],
				'report_type_id' => $row['report_type_id'],
				'name' => $row['name'],
				'date' => date( 'Y-m-d', $row['date'] ),
				'user_id' => $row['user_id']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}

	function related_report_types() {
		$this->db->select( 'report_type_id AS reports_id, name AS reports_name' );
		$rel_data = $this->db->get( 'reports' );
		return $rel_data->result_array();
    }


}