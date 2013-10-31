<?php

namespace Contract\Controller;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Contract\Form\ContractForm;
use AddList\Form\AddListForm;

class ContractController extends AbstractActionController
{
    protected $contractModel;
    protected $ticketModel;
    protected $resourceModel;
    protected $addListModel;
    protected $companyUserModel;

    public function indexAction()
    {
        $contractModel = $this->getContractModel();
        $contract = $contractModel->getContracts(
            array(
                'accepted' => null,
                'ownerUserId' => new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())
            )
        );
        return new ViewModel(array(
            'contract' => $contract
        ));

    }

    public function myAction()
    {
        $contractModel = $this->getContractModel();
        $contract = $contractModel->getContracts(
            array(
                'accepted' => null,
                'receiveUserId' => new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())
            )
        );
        return new ViewModel(array(
            'contract' => $contract
        ));

    }

    public function workAction()
    {
        $contractModel = $this->getContractModel();
        $contract = $contractModel->getTicketsInWork(
            $this->zfcUserAuthentication()->getIdentity()->getCurrentCom()
        );
        return new ViewModel(array(
            'contract' => $contract
        ));

    }

    public function acceptAction()
    {
        $sendUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $contractModel = $this->getContractModel();
        $contractModel->acceptContract($sendUuid);
        return $this->redirect()->toUrl('/contracts/work');

    }

    public function addAction()
    {
        $sendUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $contractModel = $this->getContractModel();
        $listProposalData = $contractModel->getListProposalData(
            $sendUuid,
            $this->zfcUserAuthentication()->getIdentity()->getCurrentCom()
        );

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Contract\Entity\Contract');

        $intForm = new ContractForm();

        $form = $intForm->fillContract($form, $listProposalData);

        return new ViewModel(array(
            'form' => $form,
            'uuid' => $sendUuid
        ));

    }

    public function addProposalAction()
    {
        $proposalUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $post = $this->getRequest()->getPost();
        $contractModel = $this->getContractModel();
        $contractModel->addProposal($proposalUuid, $post);
        return $this->redirect()->toUrl('/contracts/proposal/' . $proposalUuid);
    }

    public function proposalAction()
    {
        $proposalUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Contract\Entity\ContractNote');

        $addListModel = $this->getAddListModel();

        $comListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();


        $formData = $addListModel->returnDataArray(array(), 'contractNote', $accListId, $comListId);

        $fillFrom = new AddListForm();
        $form = $fillFrom->fillFrom($form, $formData);

        $contractModel = $this->getContractModel();
        $proposal = $contractModel->getProposal($proposalUuid);

        return new ViewModel(array(
            'form' => $form,
            'uuid' => $proposalUuid,
            'proposal' => $proposal
        ));


    }

    public function addContractAction()
    {
        $receiveUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $post = get_object_vars($this->getRequest()->getPost());

        $contractModel = $this->getContractModel();
        $contractModel->addContract(
            $post['sendItemId'],
            $receiveUuid,
            $this->zfcUserAuthentication()->getIdentity()->getCurrentCom()
        );
        return $this->redirect()->toUrl('/contracts');
    }

    public function getContractModel()
    {
        if (!$this->contractModel) {
            $sm = $this->getServiceLocator();
            $this->contractModel = $sm->get('Contract\Model\ContractModel');
        }
        return $this->contractModel;
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
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
}
