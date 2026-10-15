<?php 			
/** 
 * @since: 06/Aug/2020
 * @author: Sarwar Hasan 
 * @version 1.0.0
 * @property:id,name,entry_date,status		
 * @property Mfaq_list[] $questions
 */
class Mfaq_category extends APP_Model{	
	public $id;
	public $name;
	public $entry_date;
	public $status;
    public $questions=[];

	    /**
	     *@property id,name,entry_date,status
		 */
		function __construct() {
			parent::__construct ();
			$this->SetValidation();	
			$this->tableName="faq_category";
			$this->primaryKey="id";
			$this->uniqueKey=array();	
			$this->multiKey=array();
			$this->autoIncField=array('id');
			
		}
			

	function SetValidation(){
		$this->validations=array(
			"id"=>array("Text"=>"Id", "Rule"=>"max_length[10]|integer"),
			"name"=>array("Text"=>"Name", "Rule"=>"max_length[255]"),
			"entry_date"=>array("Text"=>"Entry Date", "Rule"=>"max_length[20]"),
			"status"=>array("Text"=>"Status", "Rule"=>"max_length[1]")
			
		);
	}

	public function GetPropertyRawOptions($property,$isWithSelect=false){
	    $returnObj=array();
		switch ($property) {
	      case "status":        
	         $returnObj=array("A"=>"Active","I"=>"Inactive");
	         break;
	      default:
	    }	        	   
        if($isWithSelect){
            return array_merge(array(""=>"Select"),$returnObj);
        }
        return $returnObj;
		
	}
			
    static function DeleteByID($id)
    {
        return parent::DeleteByKeyValue("id", $id);
    }


    
	/**
	 * @param string $status
	 *
	 * @return self[]
	 */
	public static function getCategoriesWithQuestions($status='A'){
	    $ctgs=self::FindAllBy('status',$status);
	    $response=[];
	    if($ctgs) {
		    foreach ( $ctgs as $ctg ) {
			    $questions=Mfaq_list::FindAllBy('cat_id',$ctg->id,['status'=>$status]);
			    
			    if(!empty($questions) && count($questions)>0){
				    $ctg->questions=$questions;
			        $response[]=$ctg;
                }
		    }
	    }
	    return $response;
    }

	 function GetAddForm($label_col=5,$input_col=7,$mainobj=null,$except=array(),$disabled=array()){
		
				if(!$mainobj){
				$mainobj=$this;
				}
		        ?>
			
			
			<?php if(!in_array("name",$except)){ ?>
			 <div class="form-group">
		      	<label class="control-label col-md-<?php echo $label_col;?>" for="name"><?php _e("Name"); ?></label>
		      	<div class="col-md-<?php echo $input_col;?>">                   			     	
		      		<input type="text" maxlength="255"   value="<?php echo  $mainobj->GetPostValue("name");?>" class="form-control" id="name" <?php echo in_array("name", $disabled)?' disabled="disabled" ':' name="name" ';?>     placeholder="<?php _e("Name");?>" data-bv-notempty="true" 	data-bv-notempty-message="<?php  _e("%s is required",__("Name"));?>">
		      	</div>
		      </div> 
		     <?php } ?>
			
			<?php if(!in_array("status",$except)){ ?>
			 <div class="form-group">
		      	<label class="control-label col-md-<?php echo $label_col;?>" for="status"><?php _e("Status"); ?></label>
		      	<div class="col-md-<?php echo $input_col;?>">                   			     	
		      		
			     <div class="togglebutton ">
				    <input  name="status" value="I" type="hidden">
					<label> 
					<input  type="checkbox" <?php echo $mainobj->GetPostValue("status","'A'") == "A" ? "checked" : ""?>  value="A" class="" id="status" <?php echo in_array("status", $disabled)?' disabled="disabled" ':' name="status" ';?>   >
						 
					</label>
				</div>
			         
		      	</div>
		      </div> 
		     <?php } ?>
			
			<?php 
	}


}
?>