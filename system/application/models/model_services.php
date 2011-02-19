<?php

class Model_services extends Model {

	function Model_services() {
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
		 *     - TRUE:  just the field names of the services table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('services', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('service_id', $id);
		$this->db->update('services', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('service_id', $id);
		$this->db->delete('services');
	}


	//FUNCTION: get (for single record)


	
	//FUNCTION: lister (for multiple records)
	function lister ($page = FALSE) {
        
	    $this->db->start_cache();
		$this->db->select( 'service_id, name' );
		$this->db->from( 'services' );
		$this->db->order_by( 'name', 'ASC' );
        

        /**
         *   PAGINATION
         */
        if($this->pagination_enabled == TRUE) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('services');
            $config['base_url']    = '/services/';
            $config['uri_segment'] = 3;
            $config['per_page']    = $this->pagination_per_page;
            $config['num_links']   = $this->pagination_num_links;
    
            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $this->pager = $this->pagination->create_links();
    
            $this->db->limit( $config['per_page'], $page );
        }

        // Get the results
		$query = $this->db->get();
		
		$temp_result = array();

		foreach ($query->result_array() as $row) {
			$temp_result[] = array( 
				'service_id'	=> $row['service_id'],
				'name'			=> $row['name']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}



	//FUNCTION: get_service_name
	function get_service_name( $id ) {
        
		$this->db->start_cache();
		$this->db->select('name');
		$this->db->from('services');
		$this->db->where('service_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->name;
		} else {
			return 'Error';
		}
	}



	//FUNCTION: list_dropdown
	function list_dropdown() {
		$this->db->select('service_id, name');
		$this->db->from('services');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get()->result();
		
		$data = array();
		
		foreach( $query as $row ){
			$data[$row->service_id] = $row->name;
		}
		
		return $data;
	}



	//FUNCTION: list_autocomplete
	function list_autocomplete($term) {
		$this->db->select('service_id, name');
		$this->db->from('services');
        $this->db->like('name', $term);
		$query = $this->db->get()->result();
		
		return $query;
	}





	//FUNCTION: check_related_offering=s
	function check_related_offerings( $id ) {
        
		$this->db->start_cache();
		$this->db->select('offering_id');
		$this->db->from('offerings');
		$this->db->where('service_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}







//unused functions
	function get ( $id, $get_one = false ) {
        
	    $select_statement = ( $this->raw_data ) ? 'service_id,name' : 'service_id,name';
		$this->db->select( $select_statement );
		$this->db->from('services');
        

		// Pick one record
		// Field order sample may be empty because no record is requested, eg. create/GET event
		if( $get_one ) {
            $this->db->limit(1,0);
        } else {// Select the desired record
            $this->db->where( 'service_id', $id );
        }

		$query = $this->db->get();

		if ( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return array( 
				'service_id' => $row['service_id'],
				'name' => $row['name']
			 );
		} else {
            return array();
        }
	}
}