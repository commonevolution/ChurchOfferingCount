<?php

class Model_offering_types extends Model {

	function Model_offering_types() {
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
		 *     - TRUE:  just the field names of the offering_types table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('offering_types', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('offering_type_id', $id);
		$this->db->update('offering_types', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('offering_type_id', $id);
		$this->db->delete('offering_types');
	}


	//FUNCTION: get (for single record)

	
	//FUNCTION: lister (for multiple records)
	function lister ($page = FALSE) {
        
	    $this->db->start_cache();
		$this->db->select( 'offering_type_id, name' );
		$this->db->from( 'offering_types' );
		$this->db->order_by( 'name', 'ASC' );
        

        /**
         *   PAGINATION
         */
        if($this->pagination_enabled == TRUE) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('offering_types');
            $config['base_url']    = '/offering_types/';
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
				'offering_type_id' => $row['offering_type_id'],
				'name' => $row['name']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}



	//FUNCTION: get_offering_type_name
	function get_offering_type_name( $id ) {
        
		$this->db->start_cache();
		$this->db->select('name');
		$this->db->from('offering_types');
		$this->db->where('offering_type_id', $id);
		
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
		$this->db->select('offering_type_id, name');
		$this->db->from('offering_types');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get()->result();
		
		$data = array();
		
		foreach( $query as $row ){
			$data[$row->offering_type_id] = $row->name;
		}
		
		return $data;
	}



	//FUNCTION: list_autocomplete
	function list_autocomplete($term) {
		$this->db->select('offering_type_id, name');
		$this->db->from('offering_types');
        $this->db->like('name', $term);
		$query = $this->db->get()->result();
		
		return $query;
	}




	//FUNCTION: check_related_offering=s
	function check_related_offerings( $id ) {
        
		$this->db->start_cache();
		$this->db->select('offering_id');
		$this->db->from('offerings');
		$this->db->where('offering_type_id', $id);
		
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
        
	    $select_statement = ( $this->raw_data ) ? 'offering_type_id,name' : 'offering_type_id,name';
		$this->db->select( $select_statement );
		$this->db->from('offering_types');
        

		// Pick one record
		// Field order sample may be empty because no record is requested, eg. create/GET event
		if( $get_one )
        {
            $this->db->limit(1,0);
        }
		else // Select the desired record
        {
            $this->db->where( 'offering_type_id', $id );
        }

		$query = $this->db->get();

		if ( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return array( 
				'offering_type_id' => $row['offering_type_id'],
				'offering_type_name' => $row['name']
			 );
		} else {
            return array();
        }
	}
}