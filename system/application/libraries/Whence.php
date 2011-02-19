<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Whence
{
    protected $maxwhence=10;
    protected $homepage='';
    protected $_ci=null;
 
    function __construct($config = FALSE)
    {
        $this->_ci =& get_instance();
        // there will always be exactly maxwhence elements in the array
        $this->maxwhence = $config['maxwhence'];
        $this->homepage = $config['homepage'];
        if(!isset($this->_ci->session->userdata['whence']))
        {
            $this->clearwhence();
        }
    }
    
    function Whence($config)
    {
        $this->__construct($config);
    }
    
    function clearwhence()
    {
        $defaultwhence = strtolower($this->homepage);
        $whencearray = array();
        for($i=0;$i<$this->maxwhence;$i++)
        {
            $whencearray[]=$defaultwhence;
        }
        $this->_ci->session->set_userdata(array("whence"=>$whencearray));
    }
 
     function isajax() 
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest");
    }
    
    function push($uri='')
    {
        if($uri=='')
        {
            $uri =strtolower(substr(stristr($_SERVER['REQUEST_URI'],'index.php?/'),11));
            if($uri=='')
            {
                $uri = strtolower(substr(stristr($_SERVER['REQUEST_URI'],'index.php/'),10));
                if($uri=='')
                {
                    $uri = $this->homepage;
                }
            }
        }
        if($this->isajax() || $uri==$this->_ci->session->userdata['whence'][$this->maxwhence-1] )
        {
            // ajax requests and page reloads do not count
            return;
        }            
        // shift
        $whencearray = $this->_ci->session->userdata['whence'];
        array_shift($whencearray);
        array_push($whencearray,$uri);
        $this->_ci->session->set_userdata('whence',$whencearray);
    }
    
    function popwhence($n=1)
    {
        if(!isset($this->_ci->session->userdata['whence']))
        {
            // if the session timed out then start over
            $this->clearwhence();
        }
        $n++;  // last element is where we are NOW,not where we came from
        $whencearray = $this->_ci->session->userdata['whence'];
        $defaulturi = $this->homepage;
        $whence=$defaulturi;
        for($j=0;$j<$n;$j++)
        {
            // shift the array
            $whence=array_pop($whencearray);
            array_unshift($whencearray,$defaulturi);
        }
        $this->_ci->session->set_userdata('whence',$whencearray);
        redirect($whence);
    }
    
    function pop($n=1)
    {
        if(!isset($this->_ci->session->userdata['whence']))
        {
            // if the session timed out then start over
            $this->clearwhence();
        }
        $n++;  // last element is where are NOW,not where we came from
        $whencearray = $this->_ci->session->userdata['whence'];
        $defaulturi = $this->homepage;
        $whence=$defaulturi;
        for($j=0;$j<$n;$j++)
        {
            // shift the array
            $whence=array_pop($whencearray);
            array_unshift($whencearray,$defaulturi);
        }
        $this->_ci->session->set_userdata('whence',$whencearray);
    }
    
    function dump()
    {
        print_r($this->_ci->session->userdata['whence']);
    }
 } 
?>