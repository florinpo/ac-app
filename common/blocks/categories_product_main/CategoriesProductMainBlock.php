<?php

/**
 * Class for render Categories Product Main * 
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package common.blocks.categories_product_main */

class CategoriesProductMainBlock extends CWidget
{
    
    //Do not delete these attr block, page and errors
    public $id='categories_product_main';
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
                //Start working with Categories Product Main here
                $params=b64_unserialize($this->block['params']);
                $this->setParams($params);
                $categories = ProductSaleCategoryList::model()->findAll(array('condition'=>'level=1'));
            	$this->render(BlockRenderWidget::setRenderOutput($this),array('categories'=>$categories));                                                          	       		     
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