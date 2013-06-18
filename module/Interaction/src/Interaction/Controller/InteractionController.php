<?php

namespace Interaction\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Interaction\Form\InteractionForm;
use AddList\Form\AddListForm;

class InteractionController extends AbstractActionController
{
    protected $interactionModel;
    protected $ticketModel;
    protected $resourceModel;
    protected $addListModel;
    protected $companyUserModel;

    public function indexAction()
    {
        $interactionModel = $this->getInteractionModel();
        $interaction=$interactionModel->getInteractions($this->zfcUserAuthentication()->getIdentity()->getId(),'sent');
        return new ViewModel(array(
            'interaction' =>$interaction
        ));

    }

    public function myAction()
    {
        $interactionModel = $this->getInteractionModel();
        $interaction=$interactionModel->getInteractions($this->zfcUserAuthentication()->getIdentity()->getId(),'receive');
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

    public function addProposalAction() {
        $proposalUuid= $this->getEvent()->getRouteMatch()->getParam('id');
        $post=$this->getRequest()->getPost();
        $interactionModel = $this->getInteractionModel();
        $interactionModel->addProposal($proposalUuid,$post);
        return $this->redirect()->toUrl('/interactions/proposal/'.$proposalUuid);
    }

    public function proposalAction() {
        $proposalUuid= $this->getEvent()->getRouteMatch()->getParam('id');
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Interaction\Entity\InteractionNote');

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray(array(),'interactionNote',$orgListId);

        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);

        $interactionModel = $this->getInteractionModel();
        $proposal=$interactionModel->getProposal($proposalUuid);

        return new ViewModel(array(
            'form' => $form,
            'uuid'=>$proposalUuid,
            'proposal'=>$proposal
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
    public function getAddListModel()
    {
        if (!$this->addListModel) {
            $sm = $this->getServiceLocator();
            $this->addListModel = $sm->get('AddList\Model\AddListModel');
        }
        return $this->addListModel;
    }
    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
}
