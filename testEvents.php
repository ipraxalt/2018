<?php
namespace Escorts\Events\Block\Adminhtml\Events\Edit\Tab;
class Events extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
    * @var \Magento\Customer\Model\CustomerFactory $customerFactory
    */
    protected $customerFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Escorts\Events\Model\TemplatesFactory $templatesFactory,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->customerFactory = $customerFactory;
        $this->templatesFactory = $templatesFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
    /* @var $model \Magento\Cms\Model\Page */
    $model = $this->_coreRegistry->registry('events_events');
    $isElementDisabled = false;
    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create();
    $form->setHtmlIdPrefix('page_');
    $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('EVENTS')));
    $eventRate="";
    $isDisabled = false;

    if($model->getId()) {
        $isDisabled = true;
        $fieldset->addField('id', 'hidden', array('name' => 'id'));
        $eventRate = $model->getEventRate();
    } 

      
     
      $fieldset->addField(
            'template_id', 'select', array(
            'name' => 'template_id',
            'label' => __('Event Template'),
            'title' => __('Event Template'),
            'disabled' => $isDisabled,
             'required' => true,
            'options' => $this->getEventTemplateOptionArray(),
            'onchange' => 'doReload(this)',
          )
        )->setAfterElementHtml("<script type=\"text/javascript\">
            function doReload(ev){ 
                       var templateId = ev.selectedOptions[0].value;                        
                       var url = new URL(window.location.href);
                       url.searchParams.set('template_id',templateId);
                       window.location.href = url.href;
            }
    </script>");

		$fieldset->addField(
            'event_name',
            'text',
            array(
                'name' => 'event_name',
                'label' => __('Event Name'),
                'title' => __('event name'),
                'required' => true,
        ));
		$fieldset->addField(
            'short_description',
            'textarea',
            array(
                'name' => 'short_description',
                'label' => __('Short Desciption'),
                'title' => __('short desciption'),
                'required' => true,   
                 'readonly' => true,             
            )
        );
		$fieldset->addField(
            'start_date',
            'date',
            array(
                'name' => 'start_date',
                'label' => __('Start Date'),
                'title' => __('start date'),
				        'date_format' => 'yyyy-M-dd',
                'time_format' => 'HH:mm:ss',
                'required' => true,
            )
    );
		$fieldset->addField(
            'end_date',
            'date',
            array(
                'name' => 'end_date',
                'label' => __('End Date'),
                'title' => __('end date'),
				        'date_format' => 'yyyy-M-dd',
                'time_format' => 'HH:mm:ss',
                'required' => true,
            )
    );       

    $fieldset->addField('event_rate', 'checkbox', array(
        'label'     => __('Need To Rate Between 0 To 10'),
        'onclick'   => 'this.value = this.checked ? 1 : 0;',
        'name'      => 'event_rate',
        'checked'   => $eventRate==1 ? 'checked' :'',
    ));

    $fieldset->addField('assign_to', 'multiselect', 
            array(
                'name' => 'assign_to',
                'label' => __('Assign To'),
                'title' => __('assign_to'),
                'values' => $this->getCustomerGroupArray(),  
                'required' => true,
                'onchange' => 'checkSelectedItem(this)',
    )
            )->setAfterElementHtml("<script type=\"text/javascript\">
                     function checkSelectedItem(ev){ 
                        var clickedOption = jQuery(ev.target); 
                        var selectedValue = ev.selectedOptions[0].value;  
                        var selected = jQuery(':selected', ev);
                       
                          /*if(selectedValue == 'all_sd' ||  selectedValue == 'all_po' ||  selectedValue == 'all_ma' ||  selectedValue == 'all_sp' ){  
                                selected.closest('optgroup').on('click', function() {
                                var currentOptGroup = selected.closest('optgroup').attr('label'); 
                                jQuery(this).children('option').attr('selected', 'selected');
                                jQuery(this).children('option').prop('selected', 'selected');
                                jQuery(this).next().children('option').prop('selected', false);
                                jQuery(this).prev().children('option').prop('selected', false);
                                  return;
                             });

                          }else{
                           
                            selected.closest('optgroup').on('click', function() { 
                                  jQuery(this).children('option').removeAttr('selected');
                                  jQuery(this).next().children('option').removeAttr('selected');
                                  jQuery(this).prev().children('option').removeAttr('selected');
                                  jQuery('#page_assign_to option[value='+selectedValue+']').prop('selected', 'selected');
                                  jQuery('#page_assign_to option[value='+selectedValue+']').attr('selected', 'selected');
                                  return;
                            });
                          }*/

                        }
                    </script>");
	   /*{{CedAddFormField}}*/

      $fieldset->addField(
            'status', 'select', array(
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('status'),
            'options' => $this->getStatusOptionArray(),
                )
      );
      
      if (!$model->getId()){ 

          /*custom Value Set based on template Id when form is not in edit form */
          $templateId="";
          $paramTemplateId = $this->getRequest()->getParam('template_id');
          if(!$paramTemplateId){             
              $templateCollection = $this->templatesFactory->create()
                                        ->getCollection()
                                        ->setOrder('id','ASC');
              $templateCollection->getSelect()->limit(1);                       
              if(!empty($templateCollection->getSize())){ 
                 foreach($templateCollection as $templateData){
                       $paramTemplateId=$templateData->getId();
                 }
              } 
        }

          $templateModel = $this->templatesFactory->create()
                                ->load($paramTemplateId);        
         
          if(!empty( $templateModel->getId())){
             $templateTitle = $templateModel->getTitle();
             $templateDesc =  $templateModel->getShortDescription();
              $model->setData('template_id', $paramTemplateId);
             $model->setData('event_name', $templateTitle);
             $model->setData('short_description', $templateDesc);                  
          }

                               



        /*custom Value Set based on template Id when form is not in edit form */ 
        $model->setData('status', $isElementDisabled ? '2' : '1');
      }
      $form->setValues($model->getData());
      $this->setForm($form);
      return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     * @return string
     */
    public function getTabLabel()
    {
        return __('Events');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('EVENTS');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }


    public function getCustomerGroupArray() {        
        $service_dealer_collection = $this->customerFactory->create()
                                            ->getCollection()
                                            ->addAttributeToSelect("entity_id","firstname","lastname")
                                            ->addAttributeToFilter("group_id", array("in" => SD_GROUP_ID))
                                            //->addAttributeToFilter("group_id", array("eq" => PO_GROUP_ID))
                                            ->load();       
        $option = [];
        $child = [];

       // $option[] = ['label' => 'Self', 'value' => 'self'];
        if(!empty($service_dealer_collection->getSize())){  
             //$child[] = ['value' => 'all_sd', 'label' => 'All SD'];
                foreach ($service_dealer_collection as $collection) {
                    $fullName = $collection->getFirstname().' '.$collection->getLastname();
                    $child[] = ['value' => $collection->getEntityId(), 'label' => $fullName];
                }
                $option[] = ['label' => 'Service Dealer', 'value' => $child];
        }

        $po_collection = $this->customerFactory->create()
                              ->getCollection()
                              ->addAttributeToSelect("entity_id","firstname","lastname")
                              ->addAttributeToFilter("group_id", array("eq" => PO_GROUP_ID))
                              ->load();
        if(!empty($po_collection->getSize())){  
             $pochild=[];
             //$pochild[] = ['value' => 'all_po', 'label' => 'All PO', 'class'=>'po'];
                foreach($po_collection as $collection) {
                       $fullName = $collection->getFirstname().' '.$collection->getLastname();
                       $pochild[] = ['value' => $collection->getEntityId(), 'label' => $fullName];
                }
                $option[] = ['label' => 'PO', 'value' => $pochild];
        }




         $sp_collection = $this->customerFactory->create()
                              ->getCollection()
                              ->addAttributeToSelect("entity_id","firstname","lastname")
                              ->addAttributeToFilter("group_id", array("eq" => SP_GROUP_ID))
                              ->load();
        if(!empty($sp_collection->getSize())){  
             $spchild=[];
            // $spchild[] = ['value' => 'all_sp', 'label' => 'All SP', 'class'=>'sp'];
                foreach($sp_collection as $collection) {
                       $fullName = $collection->getFirstname().' '.$collection->getLastname();
                       $spchild[] = ['value' => $collection->getEntityId(), 'label' => $fullName];
                }
                $option[] = ['label' => 'Sales Person', 'value' => $spchild];
        }



         $momanmol_collection = $this->customerFactory->create()
                              ->getCollection()
                              ->addAttributeToSelect("entity_id","firstname","lastname")
                              ->addAttributeToFilter("group_id", array("eq" => MOL_ANMOL_GROUP_ID))
                              ->load();
        if(!empty($momanmol_collection->getSize())){  
             $molChild=[];
            // $molChild[] = ['value' => 'all_ma', 'label' => 'All MOL ANMOL', 'class'=>'ma'];
                foreach($momanmol_collection as $collection) {
                       $fullName = $collection->getFirstname().' '.$collection->getLastname();
                       $molChild[] = ['value' => $collection->getEntityId(), 'label' => $fullName];
                }
                $option[] = ['label' => 'MOL ANMOL', 'value' => $molChild];
        }
        return $option;
    }

    public function getStatusOptionArray() {
        $_options = [0 => 'Disabled', 1 => 'Enabled'];
        foreach ($_options as $key => $_option) {
            $option[$key] = $_option;
        }
        return $option;
    }

    
    public function getEventTemplateOptionArray(){
        $templateOptions=[];       
        $collection = $this->templatesFactory->create()
                           ->getCollection()
                           ->setOrder('id','ASC'); 
       if(!empty($collection->getSize())){
             foreach($collection as $template){
                   $id=$template->getId(); 
                   $templateOptions[$id] =  $template->getTitle();
             }
       }                 
       return $templateOptions;
    }

    
}