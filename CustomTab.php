<?php

namespace Escorts\ImageCatalog\Block\Adminhtml\ImageCatalog\Renderer;


use Magento\Framework\DataObject;

class CustomTab  extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_backendUrl;
    protected $request;
   
  
    public function __construct( 
        \Magento\Backend\Model\UrlInterface $backendUrl,       
        \Magento\Framework\App\Request\Http $request
         
    ) {      
        $this->request = $request;
        $this->_backendUrl = $backendUrl;
        
    }



   
    public function getAfterElementHtml(){
     // $poId = $this->request->getParam('id');
     
       
         
                    $resultData = "<div class='input_fields_wrap'>
	<button class='add_field_button'>Add More Fields</button>
	<div>
		<table class='demo admin__dynamic-rows admin__control-table'>
			<thead>
				<tr><th>Service Title</th>
					<th>Hours</th>
					<th>Day(s)</th>
					<th>Reimburshment</th>
					
					<th>Sort By</th>
					<th></th>
				</tr>
			</thead>
			<tbody>";
			
			$resultData =  $resultData."<tr>
						<td>
							<select name='service_type[]' required='required'>
							<option value=''>Please select</option>							
								<option value='2'>test345</option>
								<option value='1'>Tdrsfg</option>
							
						</select>
						</td>
						<td><input class='text-field' type='text' name='hours[]' required='required'/></td>
						<td><input class='text-field' type='text' name='days[]' required='required'/></td>
						<td><input class='text-field' type='text' name='reimburshment[]' required='required'/></td>
						<td><input type='text' name='sort_order[]' class='sort_order'/></td>
						<td><button href='#' class=' action-delete'></button></td>
					</tr>";
			$resultData =  $resultData."</tbody></table>";
					
					return $resultData;
                  
       
             
       
    }

} 

$myvalues = array();
for ($i = 0; $i < 2; ++$i) {
    $myvalues[] = "Element ".$i;
}

?>

<script type="text/javascript">
    require(['jquery'],function($){
$(document).ready(function() {
    var max_fields      = 9; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
   
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment

            $(wrapper).append('<div class="div'+x+'"><table class="demo"><tbody><tr><td><select name="service_type[]" required="required" id="service_type"><option value="">Please select</option>    </select> </td><td><input class="text-field" type="text" name="hours[]" required="required"></td><td><input class="text-field" type="text" name="days[]" required="required"></td><td><input class="text-field" type="text" name="reimburshment[]" required="required"></td><td><input class="text-field" type="text" name="conveyance_charges[]" required="required"/></td><td><input type="text" name="sort_order[]" class="sort_order"></td><td><button href="#" class="remove_field action-delete"></button></td></tr></tbody></table><div>');
			//add input box
			
				var jqueryarray = <?php echo json_encode($myvalues ); ?>;
        for (var i = 0; i < jqueryarray.length; i++) {
            console.log(jqueryarray[i]);
			$('#service_type')
         .append($("<option></option>")
         .attr("value",jqueryarray[i])
         .text(jqueryarray[i]));
		 
        }
        }
    });
	
	
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); 
        //$(this).parent('div').remove(); 
        $(this).closest('div').remove(); 
        x--;
    });

    $(wrapper).on("click",".remove_field_updated", function(e){ //user click on remove text
        e.preventDefault(); 
        //$(this).parent('div').remove(); 
        $(this).closest('tr').remove(); 
        x--;
    });
    
});
    });
</script>
<style>
	.demo {
		border:1px solid #C0C0C0;
		border-collapse:collapse;
		padding:5px;
	}
	.demo th {
		border:1px solid #C0C0C0;
		padding:5px;
		background:#F0F0F0;
	}
	.demo td {
		border:1px solid #C0C0C0;
		padding:5px;
	}
	input.sort_order {
    	width: 50px;
	}
	input.text-field {
		width: 100%
	}

</style>
