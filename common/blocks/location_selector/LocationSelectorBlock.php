<?php

/**
 * Class for render Location Selector * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.location_selector */

class LocationSelectorBlock extends CWidget
{
    
    //Do not delete these attr block, page and errors
    public $id='location_selector';
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
       			//Start working with Location Selector here
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