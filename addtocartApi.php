<?php
namespace Oss\Testapi\Model;

class Testapi implements \Oss\Testapi\Api\TestapiInterface
{
	
	
	/**
 * @var \Magento\Customer\Model\CustomerFactory
 */
protected $_customerFactory;
 protected $_spareParts;
	protected $_customCart;
    protected $_customCartItem;
/**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;


   public function __construct(   \Magento\Customer\Model\CustomerFactory $customerFactory,
   \Magento\Framework\App\ResourceConnection $resourceConnection,
   \Escorts\SpareParts\Model\SparePartsFactory $sparePartsFactory,		
		
		\Escorts\SpareParts\Model\CustomCartFactory $customCartFactory,
        \Escorts\SpareParts\Model\CustomCartItemFactory $customCartItemFactory	
    
	){
		
		$this->_customerFactory = $customerFactory;
		$this->_resourceConnection = $resourceConnection;
		$this->_spareParts = $sparePartsFactory;
		$this->_customCart = $customCartFactory;
		$this->_customCartItem = $customCartItemFactory;
		// return true;
   }
   
	 public function getCustomval(){
		 
		 return "Hello getCustomval function, done";
    }
	
	
	  public function test1($param){
		  
		  
	     $customerId=4;
	     $partId=2;
	     $quantity=3;
		  
		  
		$response = [];
        $response[0]['status'] = 0;
        $response[0]['message'] = 'Something went wrong. Please try again later.';
		  
		$_sparePartsModel = $this->_spareParts->create()->load($partId);
		$_customCartCollection = $this->_customCart->create()->getCollection()
									 ->addFieldToFilter('customer_id', $customerId )			
			->addFieldToFilter('is_active', 1 );	

        /*customer cart is active*/							 
		if (!empty($_customCartCollection->getSize())) {	            		
			foreach ($_customCartCollection as $cartData) {		
			   $cartId= $cartData['cart_id'];
			}
			
			
				 $cartItemCollection = $this->_customCartItem->create()->getCollection()
									 ->addFieldToFilter('cart_id', $cartId )
									 ->addFieldToFilter('part_id', $partId);
									 
									 
				if(empty($cartItemCollection->getSize())){
				 /*case2-item new add in cart*/
                /*ADD Item to cart item*/				
				$itemDataArray['price'] = $_sparePartsModel->getPriceMrp();
				$itemDataArray['cgst'] = $_sparePartsModel->getCgst();
				$itemDataArray['sgst'] = $_sparePartsModel->getSgst();	
				$itemDataArray['part_id'] = $partId;
				$itemDataArray['qty'] = $quantity;
				$itemDataArray['cart_id']= $cartId;
				$modelCustomCartItem = $this->_customCartItem->create();				
				$modelCustomCartItem->setData( $itemDataArray);					  
				$modelCustomCartItem->save();
                
				 /*Update Cart*/
                $_customCartItemCollection = $this->_customCartItem->create()->getCollection()
									 ->addFieldToFilter('cart_id', $cartId );	
               	$itemSgst="";	$itemCgst=""; $subtotal=""; $gTotal ="";$gTotal ="";
				$grand_total=""; $total="";
				foreach ($_customCartItemCollection as $cartItemData) {		
						 $cartId= $cartItemData['cart_id'];
						$itemPriceMrp= $cartItemData['price'];
						$itemSgst= $itemSgst+$cartItemData['Sgst'];
						$itemCgst= $itemCgst+$cartItemData['Cgst'];
						$subtotal = $subtotal+$quantity*$itemPriceMrp;
						$grand_total = $grand_total+ $subtotal+$itemSgst+$itemCgst;
													
                }
				       
						$cartDataArray['sub_total'] = $subtotal;
						$cartDataArray['cgst'] = $itemCgst;
						$cartDataArray['sgst'] = $itemSgst;
						$cartDataArray['grand_total'] = $grand_total;
						$cartDataArray['cart_id'] = $cartId;
						
						
						$_customCartModel = $this->_customCart->create();
									 
                        //$_customCartModel = $this->_customCart->create()->getCollection()
							//	 ->addFieldToFilter('cart_id', $cartId );							
						$_customCartModel->setData($cartDataArray);				
				        $_customCartModel->save();
				 /*Update Cart*/
              
			
			}else{
				
				/*case1-item avialable in cart*/
				
				/*Item quantity upadte*/
			   
				foreach ($cartItemCollection as $cartItemData) {		
			       $cart_item_id= $cartItemData['cart_item_id'];
			    }
				
				
				$modelCustomCartItem = $this->_customCartItem->create()->load($cart_item_id);	
				if($modelCustomCartItem){
				$prevItemQty = $modelCustomCartItem->getQty();
				$newQty = $prevItemQty+$quantity;
				
				$modelCustomCartItem->setQty($newQty);					  
				$modelCustomCartItem->save();
				}
				
				
				/*cart upadte*/
				$_customCartItemCollection = $this->_customCartItem->create()->getCollection()
									 ->addFieldToFilter('cart_id', $cartId );	
               	$itemSgst="";	$itemCgst=""; $subtotal=""; $gTotal ="";$gTotal ="";
				$grand_total=""; $total="";
				foreach ($_customCartItemCollection as $cartItemData) {	
                $total="";				
						 $cartId= $cartItemData['cart_id'];
						$itemPriceMrp= $cartItemData['price'];
						$itemSgst= $itemSgst+$cartItemData['Sgst'];
						$itemCgst= $itemCgst+$cartItemData['Cgst'];
						$quantity = $cartItemData['qty'];
						$subtotal = $subtotal+$quantity*$itemPriceMrp;						
						$grand_total = $grand_total+ $subtotal+$itemSgst+$itemCgst;
						//$grand_total = $grand_total + $gTotal;								
                }
				       
				
				$_customCartModel = $this->_customCart->create()->load($cartId);	
				if($_customCartModel){								
					$_customCartModel->setCgst($itemCgst);
					$_customCartModel->setSgst($itemSgst);
					$_customCartModel->setGrandTotal($grand_total);
					$_customCartModel->setSubTotal($subtotal);				 
					$_customCartModel->save();				
				}
				/*Update Cart*/
				
				
				
			}
		}
		
		else{
			    /*customer cart is not active*/	
			    /*add entry in cart table*/
				 $quantity=1;
				$itemPriceMrp= $_sparePartsModel->getPriceMrp();
				$itemSgst= $_sparePartsModel->getSgst();
				$itemCgst= $_sparePartsModel->getCgst();		
				$itemDataArray['price'] = $_sparePartsModel->getPriceMrp();
				$itemDataArray['cgst'] = $_sparePartsModel->getCgst();
				$itemDataArray['sgst'] = $_sparePartsModel->getSgst();	
				$itemDataArray['part_id'] = $partId;
				$itemDataArray['qty'] = $quantity;
				$subtotal = $quantity*$itemPriceMrp;
				$grand_total =  $subtotal+$itemSgst+$itemCgst;
				$cartDataArray['sub_total'] = $subtotal;
				$cartDataArray['cgst'] = $itemCgst;
				$cartDataArray['sgst'] = $itemSgst;
				$cartDataArray['grand_total'] = $grand_total;				
				
				
			    $modelCustomCart = $this->_customCart->create();				
				$cartDataArray['customer_id'] = $customerId;							
				$modelCustomCart->setData($cartDataArray);				
				$modelCustomCart->save();	
				if ($modelCustomCart->getId()) {
                      /*Add product to cart item table*/					
				      $cartId = $modelCustomCart->getId();
                      $modelCustomCartItem = $this->_customCartItem->create();
					  $itemDataArray['cart_id']= $modelCustomCart->getId();
					  $modelCustomCartItem->setData( $itemDataArray);					  
                      $modelCustomCartItem->save();					 
                }
			
			$response[0]['case2'] = 'case2-';		
		}
		     
			 
		   
			
			
			

			$response[0]['status'] = 1;
			$response[0]['message'] = 'add to cart successfully';           
     

        return $response;
		  
		  
		  
	
	   
      }
	  
	 /**
     * Sum an array of numbers.
     *
     * @api
     * @param int[] $nums The array of numbers to sum.
     * @return int The sum of the numbers.
     */
    public function sum($nums) {
        $total = 0;
        foreach ($nums as $num) {
            $total += $num;
        }
        return $total;
    }
	
}