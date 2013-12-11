<?php
namespace Application\Controller;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toUrl('/');
        }

        $user = $this->zfcUserAuthentication()->getIdentity();
        $tokenEntity = $this->getServiceLocator()->get('AuthToken\Model\AuthToken')->create($user);

        $cookie = new SetCookie('token', $tokenEntity->getToken());
        $response = $this->getResponse()->getHeaders();
        $response->addHeader($cookie);

        $viewModel = new ViewModel();
        return $viewModel;
    }

    public function greetingsAction()
    {
        /** @var \ZfcUser\Controller\Plugin\ZfcUserAuthentication $zfcUserAuth */
        $zfcUserAuth = $this->zfcUserAuthentication();
        if (!$zfcUserAuth->hasIdentity() || 1 !== $zfcUserAuth->getIdentity()->getState()) {
            return $this->redirect()->toUrl('/');
        }
        return new ViewModel();
    }
}
