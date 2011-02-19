<?php

class Model_people extends Model {

	//FUNCTION: Base model function
    function Model_people() {
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
		 *     - TRUE:  just the field names of the people table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('people', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('people_id', $id);
		$this->db->update('people', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('people_id', $id);
		$this->db->delete('people');
	}


	//FUNCTION: get (for single record)


	
	//FUNCTION: lister (for multiple records)
	function lister ($page = FALSE) {
        
	    $this->db->start_cache();
		$this->db->select( 'people_id, first_name, last_name' );
		$this->db->from( 'people' );
		$this->db->order_by( 'first_name, last_name', 'ASC' );
        

        /**
         *   PAGINATION
         */
        if($this->pagination_enabled == TRUE) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('people');
            $config['base_url']    = '/people/';
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
				'people_id' => $row['people_id'],
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name']
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}


	//FUNCTION: list_dropdown
	function list_dropdown() {
		$this->db->select('people_id, first_name, last_name');
		$this->db->from('people');
		$this->db->order_by('first_name, last_name', 'ASC');
		$query = $this->db->get();
		
		$data = array();
        
		$data[] = '- select person -';
		foreach($query->result_array() as $row) {
			$data['people_id'] = $row['people_id'];
			$data['person_name'] = $row['first_name']." ".$row['last_name'];
		}
		
		return $data;
	}


	//FUNCTION: list_autocomplete
	function list_autocomplete($term) {
		$this->db->select('people_id, first_name, last_name');
		$this->db->from('people');
		$this->db->like('first_name', $term);
		$this->db->or_like('last_name', $term);
		$query = $this->db->get()->result();
		
		return $query;
	}




	//FUNCTION: check_related_records
	function check_related_records( $id ) {
        
		$this->db->start_cache();
		$this->db->select('record_id');
		$this->db->from('records');
		$this->db->where('people_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}






//unused functions
	function search ( $keyword, $page = FALSE )
	{
	    $meta = $this->metadata();
	    $this->db->start_cache();
		$this->db->select( 'people_id,first_name,last_name,phone,email_address,added,user.user_name AS user_id');
		$this->db->from( 'people' );
        $this->db->join( 'user', 'user_id = id', 'left' );


		// Delete this line after setting up the search conditions 
        die('Please see models/model_people.php for setting up the search method.');
		
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
            $config['total_rows']  = $this->db->count_all_results('people');
            $config['base_url']    = '/people/search/'.$keyword.'/';
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
				'people_id' => $row['people_id'],
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name'],
				'phone' => $row['phone'],
				'email_address' => $row['email_address'],
				'added' => date( 'Y-m-d', $row['added'] ),
				'user_id' => $row['user_id'],
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}

	function related_user()
    {
        $this->db->select( 'id AS user_id, user_name AS user_name' );
        $rel_data = $this->db->get( 'user' );
        return $rel_data->result_array();
    }



	function get ( $id, $get_one = false )
	{
        
	    $select_statement = ( $this->raw_data ) ? 'people_id,first_name,last_name,phone,email_address,added,user_id' : 'people_id,first_name,last_name,phone,email_address,added,user.user_name AS user_id';
		$this->db->select( $select_statement );
		$this->db->from('people');
        $this->db->join( 'user', 'user_id = id', 'left' );
		$this->db->where( 'people_id', $id);

		// Pick one record
		// Field order sample may be empty because no record is requested, eg. create/GET event
		if( $get_one )
        {
            $this->db->limit(1,0);
        }
		else // Select the desired record
        {
            $this->db->where( 'people_id', $id );
        }

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return array( 
				'people_id' => $row['people_id'],
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name'],
				'phone' => $row['phone'],
				'email_address' => $row['email_address'],
				'added' => $row['added'],
				'user_id' => $row['user_id']
			);
		}
        else
        {
            return array();
        }
	}




    /**
     *  Some utility methods
     */
    function fields( $withID = FALSE )
    {
        $fs = array(
			'people_id' => lang('people_id'),
			'first_name' => lang('first_name'),
			'last_name' => lang('last_name'),
			'phone' => lang('phone'),
			'email_address' => lang('email_address'),
			'user_id' => lang('user_id')
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

        $metadata = $this->explain_table->parse( 'people' );

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