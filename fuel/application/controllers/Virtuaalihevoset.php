<?php
class Virtuaalihevoset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->pipari();
	}
	
	
}
?>





