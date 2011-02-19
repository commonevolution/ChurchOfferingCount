<?php

class Model_methods extends Model {

	function Model_methods() {
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
		 *     - TRUE:  just the field names of the methods table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('methods', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('method_id', $id);
		$this->db->update('methods', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('method_id', $id);
		$this->db->delete('methods');
	}


	//FUNCTION: get (for single record)


	//FUNCTION: lister (for multiple records)
	function lister ($page = FALSE) {
        
	    $this->db->start_cache();
		$this->db->select( 'method_id, name' );
		$this->db->from( 'methods' );
		$this->db->order_by( 'name', 'ASC' );
        

        /**
         *   PAGINATION
         */
        if($this->pagination_enabled == TRUE) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('methods');
            $config['base_url']    = '/methods/';
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
				'method_id' => $row['method_id'],
				'name' => $row['name']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}




	//FUNCTION: list_dropdown
	function list_dropdown() {
		$this->db->select('method_id, name');
		$this->db->from('methods');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get()->result();
		
		$data = array();
		
		foreach( $query as $row ){
			$data[$row->method_id] = $row->name;
		}
		
		return $data;
	}
	


	//FUNCTION: list_autocomplete
	function list_autocomplete($term) {
		$this->db->select('method_id, name');
		$this->db->from('methods');
        $this->db->like('name', $term);
		$query = $this->db->get()->result();
		
		return $query;
	}


	//FUNCTION: get_method_name
	function get_method_name( $id ) {
        
		$this->db->start_cache();
		$this->db->select('name');
		$this->db->from('methods');
		$this->db->where('method_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->name;
		} else {
			return 'Error';
		}
	}


	//FUNCTION: check_related_records
	function check_related_records( $id ) {
        
		$this->db->start_cache();
		$this->db->select('record_id');
		$this->db->from('records');
		$this->db->where('method_id', $id);
		
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
        
	    $select_statement = ( $this->raw_data ) ? 'method_id,name' : 'method_id,name';
		$this->db->select( $select_statement );
		$this->db->from('methods');
        

		// Pick one record
		// Field order sample may be empty because no record is requested, eg. create/GET event
		if( $get_one )
        {
            $this->db->limit(1,0);
        }
		else // Select the desired record
        {
            $this->db->where( 'method_id', $id );
        }

		$query = $this->db->get();

		if ( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return array( 
				'method_id' => $row['method_id'],
				'method_name' => $row['name']
			 );
		} else {
            return array();
        }
	}


	function search ( $keyword, $page = FALSE )
	{
	    $meta = $this->metadata();
	    $this->db->start_cache();
		$this->db->select( 'method_id,name');
		$this->db->from( 'methods' );
        

		// Delete this line after setting up the search conditions 
        die('Please see models/model_methods.php for setting up the search method.');
		
        /**
         *  Rename field_name_to_search to the field you wish to search 
         *  or create advanced search conditions here
		 */
        $this->db->where( 'field_name_to_search LIKE "%'.$keyword.'%"' );

        /**
         *   PAGINATION
         */
        if( $this->pagination_enabled == TRUE )
        {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('methods');
            $config['base_url']    = '/methods/search/'.$keyword.'/';
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

		foreach ( $query->result_array() as $row )
		{
			$temp_result[] = array( 
				'method_id' => $row['method_id'],
				'name' => $row['name']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}



    /**
     *  Some utility methods
     */
    function fields( $withID = FALSE )
    {
        $fs = array(
			'method_id' => lang('method_id'),
			'name' => lang('name')
		);

        if( $withID == FALSE )
        {
            unset( $fs[0] );
        }
        return $fs;
    }  
    
    function pagination( $bool )
    {
        $this->pagination_enabled = ( $bool === TRUE ) ? TRUE : FALSE;
    }

    /**
     *  Parses the table data and look for enum values, to match them with language variables
     */             
    function metadata()
    {
        $this->load->library('explain_table');

        $metadata = $this->explain_table->parse( 'methods' );

        foreach( $metadata as $k => $md )
        {
            if( !empty( $md['enum_values'] ) )
            {
                $metadata[ $k ]['enum_names'] = array_map( 'lang', $md['enum_values'] );                
            } 
        }
        return $metadata; 
    }


}