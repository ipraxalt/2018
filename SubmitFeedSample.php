<?php

//$to ="surya@onsinteractive.com"; //set the emailid here on which you want to send the email
 $filename=$_SERVER["DOCUMENT_ROOT"]."/cronfiles/test.csv";// set csv file name and location here
$data="";
$t=0;
$count=1;
$file = @fopen($filename,"r");

if (($handle=@fopen($filename, "r")) == False)//csv file not found  
   {              
				 
				     $message = 'csv file not found';
				     $subject = "Notification email";
				     $header="Content-type:text/html \r\n";
				     $header.="From: From Amazon csv upload.";
				     mail($to,$subject,$message,$header);	
		                     echo "csv file not found on the given location";
				     exit;

   }
else
 {                      //csv file available in the given location 
			$csv     = file($filename);
			$columns = explode(",", $csv[0]);
            while (($filedata = fgetcsv($handle)) !== FALSE)
				{      
                  if((strtolower($columns[1])=='sku')&&(strtolower($columns[5])=='qa'))
					      {
						    if(($filedata[5]>0)&&($filedata[5]<>''))//inventory value must be grater than zero othervise inventory value not updated                             
							{
								
								if($columns[8]==""){
									$handling_time=5;
									}else{
									$handling_time=$filedata[8];
								}
								
                                                       	
                            	                              $data.="<Message>
								<MessageID>".$count."</MessageID>
								<OperationType>Update</OperationType>
								<Inventory>
								<SKU>".$filedata[1]."</SKU>
								<Quantity>".$filedata[5]."</Quantity>
								<FulfillmentLatency>".$handling_time."</FulfillmentLatency>
								</Inventory>
								</Message>";
								$count++;
						
                                       	}
	
					    }
					 else
					   { //if inventory sku and qa column name is wrong
					     $message = 'column sequance error in csv';
					     $subject = "Notification email";
					     $header="Content-type:text/html \r\n";
					     $header.="From: From Amazon csv upload.";
					  //   mail($to,$subject,$message,$header);	
					     echo "Inventory not update because of given csv format is wrong.";
					     exit;
					  }


                            }
}

	// create xml from the get data by csv

 echo $feed = <<<EOD
<?xml version="1.0" encoding="utf-8" ?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>A381LYB9ZTOZB8</MerchantIdentifier>
</Header>
<MessageType>Inventory</MessageType>$data
</AmazonEnvelope>
EOD;

exit;
?>