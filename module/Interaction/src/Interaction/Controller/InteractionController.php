<?php

namespace Interaction\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Interaction\Form\InteractionForm;

class InteractionController extends AbstractActionController
{
    protected $interactionModel;
    protected $ticketModel;
    protected $resourceModel;

    public function indexAction()
    {
        $interactionModel = $this->getInteractionModel();
        $interaction=$interactionModel->getInteractions($this->zfcUserAuthentication()->getIdentity()->getId());
        return new ViewModel(array(
            'interaction' =>$interaction
        ));

    }

    public function addAction() {
        $sendUuid= $this->getEvent()->getRouteMatch()->getParam('id');
        $interactionModel = $this->getInteractionModel();
        $listProposalData=$interactionModel->getListProposalData($sendUuid,$this->zfcUserAuthentication()->getIdentity()->getId());

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Interaction\Entity\Interaction');

        $intForm=new InteractionForm();

        $form=$intForm->fillInteraction($form,$listProposalData);

        return new ViewModel(array(
            'form' => $form,
'uuid'=>$sendUuid
        ));

    }

    public function addInteractionAction() {
        $receiveUuid= $this->getEvent()->getRouteMatch()->getParam('id');
        $post=get_object_vars($this->getRequest()->getPost());

        $interactionModel = $this->getInteractionModel();
        $interactionModel->addInteraction($post['sendItemId'],$receiveUuid,$this->zfcUserAuthentication()->getIdentity()->getId());
        return $this->redirect()->toUrl('/interactions');
    }

    public function getInteractionModel()
    {
        if (!$this->interactionModel) {
            $sm = $this->getServiceLocator();
            $this->interactionModel = $sm->get('Interaction\Model\InteractionModel');
        }
        return $this->interactionModel;
    }


    public function getResourceModel()
    {
        if (!$this->resourceModel) {
            $sm = $this->getServiceLocator();
            $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->resourceModel;
    }



    public function getTicketModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
        }
        return $this->ticketModel;
    }
}
