
    /**
     *  Selects, deselects all the checkboxes inside a given DOM reference
     *  Used in the admin edit/create view      
     */         
	function chk_selector( way, obj )
	{
        var block = obj.parentNode;
        var chks = block.getElementsByTagName('input');
        
        for ( i=0; i<chks.length; i++ )
        {
            if( chks[i].type == 'checkbox' )
            {
                if( way == 'all' )
                {
                    chks[i].checked = true;
                }
                else if( way == 'none' )
                {
                    chks[i].checked = false;
                }
            }
        }		
	}
	
	