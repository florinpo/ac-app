<?php

/**
 * Class for render Company Listing Sorter * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.company_listing_sorter */

class CompanyListingSorterBlock extends CWidget
{
    
    //Do not delete these attr block, page and errors
    public $id='company_listing_sorter';
    public $block=null;     
    public $errors=array();
    public $page=null;
    public $layout_asset='';
        
    
    public function setParams($params){
          return; 
    }
    
    public function run()
    {                 
           $this->renderContent();         
    }       
 
 
    protected function renderContent()
    {     
       if(isset($this->block) && ($this->block!=null)){
       			//Start working with Company Listing Sorter here
				$params=b64_unserialize($this->block['params']);
	    		$this->setParams($params);                            
            	$this->render(BlockRenderWidget::setRenderOutput($this),array());                                                          	       		     
		} else {
			echo '';
		}		  
       
    }
    
    public function validate(){	
		return true ;
    }
    
    public function params()
    {
         return array();
    }
    
    public function beforeBlockSave(){
	return true;
    }
    
    public function afterBlockSave(){
	return true;
    }
	
	
}

?>