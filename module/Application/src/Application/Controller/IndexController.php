<?php
namespace Application\Controller;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        if (empty($user)) {
            return $this->redirect()->toUrl('/');
        }

        $tokenEntity = $this->getServiceLocator()->get('AuthToken\Model\AuthToken')->create($user);

        $cookie = new SetCookie('token', $tokenEntity->getToken());
        $response = $this->getResponse()->getHeaders();
        $response->addHeader($cookie);

        return new ViewModel();
    }

    public function greetingsAction()
    {
        /** @var \User\Entity\User $user */
        $user = $this->zfcUserAuthentication()->getIdentity();
        if (empty($user) || 1 !== $user->getState()) {
            return $this->redirect()->toUrl('/');
        }
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
