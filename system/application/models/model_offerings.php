<?php

class Model_offerings extends Model {

	function Model_offerings() {
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
		 *     - TRUE:  just the field names of the offerings table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	
	//FUNCTION: add
    function add($data) {
	    $this->db->insert('offerings', $data);
    	return $this->db->insert_id();
	}

	
	//FUNCTION: update
	function update($id, $data) {
		$this->db->where('offering_id', $id);
		$this->db->update('offerings', $data);
	}
	
	
	//FUNCTION: delete
	function delete($id) {
		$this->db->where('offering_id', $id);
		$this->db->delete('offerings');
	}


	//FUNCTION: get (for single record)


	
	//FUNCTION: lister (for multiple records)
	function lister( $filter = FALSE, $page = FALSE ) {
        
		$this->db->start_cache();
		$this->db->select('offerings.offering_id, offerings.date, offering_types.name AS offering_type_name, services.name AS service_name');
		$this->db->from('offerings');
		$this->db->join('services', 'services.service_id = offerings.service_id', 'left');
		$this->db->join('offering_types', 'offering_types.offering_type_id = offerings.offering_type_id', 'left');
		
		//Filter by date if necessary
		if( isset($filter['date']) ) {
			$date = $filter['date'];
			$date1 = date("Y-m-d", strtotime("$date -5 days"));
			$date2 = date("Y-m-d", strtotime("$date +5 days"));
			
			$this->db->where('date BETWEEN "'.$date1.'" and "'.$date2.'"', NULL, FALSE);
		}
		//Filter by existing report offerings if necessary
		if( isset($filter['report_offerings']) ) {
			$this->db->where_not_in('offering_id', $filter['report_offerings'], FALSE);
		}
		
		$this->db->order_by( 'offering_type_name', 'ASC' );

        /**
         *   PAGINATION
         */
        if( $this->pagination_enabled == TRUE ) {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('offerings');
            $config['base_url']    = '/offerings/';
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

		foreach ( $query->result_array() as $row ) {
			$temp_result[] = array( 
				'offering_id'			=> $row['offering_id'],
				'offering_name'			=> $row['service_name'].' '.$row['offering_type_name'],
				'service_name'			=> $row['service_name'],
				'offering_type_name'	=> $row['offering_type_name'],
				'date'					=> date('D, M j', strtotime($row['date']))		
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}
	
	
	
	//FUNCTION: get (for single record)
	function get( $offering_id, $get_one = false ) {

		$this->db->select('offerings.offering_id, offerings.date, offering_types.name AS offering_type_name, services.name AS service_name');
		$this->db->from('offerings');
		$this->db->join('services', 'services.service_id = offerings.service_id', 'left');
		$this->db->join('offering_types', 'offering_types.offering_type_id = offerings.offering_type_id', 'left');
		$this->db->where('offerings.offering_id', $offering_id);

		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			return array( 
						'offering_id'	=>	$row->offering_id,
						'offering_date'	=>	$row->date,
						'offering_name'	=>	$row->service_name . ' ' . $row->offering_type_name
						);
		} else {
			return '0';
		}
	}
	
	
	
	//FUNCTION: get_offering_name
	function get_offering_name( $id ) {
        
		$this->db->start_cache();
		$this->db->select('offerings.offering_id, offerings.date, offering_types.name AS offering_type_name, services.name AS service_name');
		$this->db->from('offerings');
		$this->db->join('services', 'services.service_id = offerings.service_id', 'left');
		$this->db->join('offering_types', 'offering_types.offering_type_id = offerings.offering_type_id', 'left');
		$this->db->where('offering_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->service_name . ' ' . $row->offering_type_name;
		} else {
			return 'Error';
		}
	}



	//FUNCTION: list_dropdown
	function list_dropdown() {
		$this->db->select('offering_id, name, date');
		$this->db->from('offerings');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		
		$data = array();
        
		$data[] = '- select offering -';
        foreach($query->result_array() as $row) {
			$data['offering_id'] = $row['name'].' '.$row['date'];
        }
        
        return $data;
	}
	


	//FUNCTION: list_autcomplete
	function list_autocomplete( $term ) {
		$this->db->select( 'offerings.offering_id, offerings.date AS offering_date, offering_types.name AS offering_type_name, services.name AS service_name' );
		$this->db->from( 'offerings' );
        $this->db->join( 'offering_types', 'offerings.offering_type_id = offering_types.offering_type_id', 'left' );
        $this->db->join( 'services', 'offerings.service_id = services.service_id', 'left' );
		$this->db->like( 'offering_types.name', $term );
		$this->db->or_like( 'services.name', $term );
		$query = $this->db->get()->result();
		
		return $query;
	}




	//FUNCTION: check_related_records
	function check_related_records( $id ) {
        
		$this->db->select('record_id');
		$this->db->from('records');
		$this->db->where('offering_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	//FUNCTION: check_related_reports
	function check_related_reports( $id ) {
        
		$this->db->select('report_id');
		$this->db->from('report_offerings');
		$this->db->where('offering_id', $id);
		
		// Get the results
		$query = $this->db->get();
		
		if ( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}







///Unused functions

	function search ( $keyword, $page = FALSE )
	{
	    $meta = $this->metadata();
	    $this->db->start_cache();
		$this->db->select( 'offering_id,offerings.name AS offering_type_id,name,date,user.user_name AS user_id');
		$this->db->from( 'offerings' );
        $this->db->join( 'offerings', 'offering_type_id = offering_type_id', 'left' );
		$this->db->join( 'user', 'user_id = id', 'left' );


		// Delete this line after setting up the search conditions 
        die('Please see models/model_offerings.php for setting up the search method.');
		
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
            $config['total_rows']  = $this->db->count_all_results('offerings');
            $config['base_url']    = '/offerings/search/'.$keyword.'/';
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
				'offering_id' => $row['offering_id'],
				'offering_type_id' => $row['offering_type_id'],
				'name' => $row['name'],
				'date' => date( 'Y-m-d', $row['date'] ),
				'user_id' => $row['user_id'],
			 );
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}

	function related_offering_types() {
		$this->db->select( 'offering_type_id AS offerings_id, name AS offerings_name' );
		$rel_data = $this->db->get( 'offerings' );
		return $rel_data->result_array();
    }


}