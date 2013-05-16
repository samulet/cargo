<?php
namespace User\View;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Guard\Controller;
use BjyAuthorize\Guard\Route;
use BjyAuthorize\View\RedirectionStrategy as BaseRedirectionStrategy;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class RedirectionStrategy extends BaseRedirectionStrategy
{
    /**
     * Handles redirects in case of dispatch errors caused by unauthorized access
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onDispatchError(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result = $event->getResult();
        $routeMatch = $event->getRouteMatch();
        $response = $event->getResponse();
        $router = $event->getRouter();
        $error = $event->getError();
        $url = $this->redirectUri;

        if (
            $result instanceof Response
            || !$routeMatch
            || ($response && !$response instanceof Response)
            || !(
                Route::ERROR === $error
                || Controller::ERROR === $error
                || (
                    Application::ERROR_EXCEPTION === $error
                    && ($event->getParam('exception') instanceof UnAuthorizedException)
                )
            )
        ) {
            return;
        }

        if (null !== $routeMatch && (('zfcuser/login' === $routeMatch->getMatchedRouteName())
                || ('zfcuser/register' === $routeMatch->getMatchedRouteName()))
        ) {
            $url = $router->assemble(array(), array('name' => 'zfcuser'));
        }

        if (null === $url) {
            $url = $router->assemble(array(), array('name' => $this->redirectRoute));
        }

        $response = $response ? : new Response();

        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        $event->setResponse($response);
    }
}
