<?php 			
/**
 * Version 1.0.0
 * Creation date: 10/Dec/2017
 * @Written By: S.M. Sarwar Hasan
 * Sarwar Hasan
 * DB Properties:key,date		
 */						
class Mexpired_info extends APP_Model{	
	public $key;
	public $date;


		function __construct() {
			parent::__construct ();
			$this->SetValidation();	
			$this->tableName="expired_info";
			$this->primaryKey="key";
			$this->uniqueKey=array();	
			$this->multiKey=array();
			$this->autoIncField=array();	
		}
			

	function SetValidation(){
		$this->validations=array(
			"key"=>array("Text"=>"Key", "Rule"=>"max_length[32]"),
			"date"=>array("Text"=>"Date", "Rule"=>"max_length[20]")
			
		);
	}

	
    function Save(){
	    if(!$this->IsSetPrperty("key")){
	        $key=$this->GetNewIncId("key","AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA");
	        $this->key($key);
	    }
	    return parent::Save();
	}

	

	 function GetAddForm($label_col=5,$input_col=7,$mainobj=null,$except=array(),$disabled=array()){
		
				if(!$mainobj){
				$mainobj=$this;
				}
					?>
			
			<?php if(!in_array("date",$except)){ ?>
			 <div class="form-group">
		      	<label class="control-label col-md-<?php echo $label_col;?>" for="date"><?php _e("Date"); ?></label>
		      	<div class="col-md-<?php echo $input_col;?>">                   			     	
		      		<input type="text" maxlength="20"   value="<?php echo  $mainobj->GetPostValue("date");?>" class="form-control" id="date" <?php echo in_array("date", $disabled)?' disabled="disabled" ':' name="date" ';?>     placeholder="<?php _e("Date"); ?>" data-bv-notempty="true" 	data-bv-notempty-message="<?php  _e("%s is required",__("Date"));?>">
		      	</div>
		      </div> 
		     <?php } ?>
			
			<?php 
	}


}
?>