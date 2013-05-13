<?php

namespace Auction\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuctionController extends AbstractActionController
{
    protected $auctionModel;
    protected $ticketModel;
    protected $resourceModel;

    public function indexAction()
    {
        $auc = $this->getAuctionModel();
        return new ViewModel(array(
            'auc' => $auc->getAuctions()
        ));

    }

    public function addAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $auc = $this->getAuctionModel();
        $price = $auc->getPrice($id);
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Auction\Entity\Auction');
        return new ViewModel(array(
            'form' => $form,
            'price' => $price,
            'uuid' => $id
        ));
    }

    public function addAuctionAction()
    {
        $auc = $this->getAuctionModel();
        $auc->addAuction(
            $this->getRequest()->getPost(),
            $auc->getItemId($this->getEvent()->getRouteMatch()->getParam('id'))
        );
        return $this->redirect()->toUrl('/auctions');
    }

    public function addBidAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Auction\Entity\AuctionBid');
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        return new ViewModel(array(
            'form' => $form,
            'uuid' => $uuid
        ));
    }

    public function listAction()
    {
        $auc = $this->getAuctionModel();
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $uuid_item = $auc->getItemUUID($uuid);
        if ($uuid_item['item'] == 'tick') {
            $tick = $this->getTicketModel();
            $list = $tick->listTicket($uuid_item['uuid']);
        } else {
            $res = $this->getResourceModel();
            $list = $res->listResource($uuid_item['uuid']);
        }

        $bids = $auc->getBids($uuid);

        return new ViewModel(array(
            'list' => $list,
            'bids' => $bids
        ));
    }

    public function addBidEngineAction()
    {
        $auc = $this->getAuctionModel();
        $id = $auc->getItemId($this->getEvent()->getRouteMatch()->getParam('id'));
        $auc->addBidEngine($id, $this->zfcUserAuthentication()->getIdentity()->getId(), $this->getRequest()->getPost());
        return $this->redirect()->toUrl('/auctions');

    }

    public function getAuctionModel()
    {
        if (!$this->auctionModel) {
            $sm = $this->getServiceLocator();
            $this->auctionModel = $sm->get('Auction\Model\AuctionModel');
        }
        return $this->auctionModel;
    }

    public function addResourceAction()
    {
        $res = $this->getResourceModel();
        $res->addResource($this->getRequest()->getPost(), $this->zfcUserAuthentication()->getIdentity()->getId());
        return $this->redirect()->toUrl('/resources/my');
    }

    public function getResourceModel()
    {
        if (!$this->resourceModel) {
            $sm = $this->getServiceLocator();
            $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->resourceModel;
    }

    public function addTicketAction()
    {
        $res = $this->getTicketModel();
        $res->addTicket($this->getRequest()->getPost(), $this->zfcUserAuthentication()->getIdentity()->getId());
        return $this->redirect()->toUrl('/tickets/my');
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
