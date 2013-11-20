<?php
namespace Application\Controller;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $cookie = new SetCookie('Auth-Token', '2a00a56c3763253a21188619997b82877ba177dbe789ca4bfe9c3a557ed1efce', 0);
        $response = $this->getResponse()->getHeaders();
        $response->addHeader($cookie);

        $viewModel = new ViewModel();
        return $viewModel;
    }
}
