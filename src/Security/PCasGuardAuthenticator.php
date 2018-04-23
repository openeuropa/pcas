<?php
namespace drupol\pcas\Security;

use drupol\pcas\PCas;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class PCasGuardAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * The PCas library.
     *
     * @var \drupol\pcas\PCas
     */
    private $pcas;

    /**
     * PCasGuardAuthenticator constructor.
     *
     * @param \drupol\pcas\PCas $pcas
     */
    public function __construct(PCas $pcas)
    {
        $this->pcas = $pcas;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Exception
     *
     * @return UserInterface|null
     */
    public function getCredentials(Request $request)
    {
        $this->pcas->validateServiceTicket($request->query->get('ticket'));

        return $this->pcas->getAuthenticatedUser();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->pcas->isAuthenticated();
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // If authentication was successful, redirect to the current URI with
        // the ticket parameter removed so that it is hidden from end-users.
        if ($request->query->has('ticket') && 'true' != $this->pcas->getQueryParameter('renew')) {
            return new RedirectResponse($this->pcas->getProtocol()->currentUrl()->__toString());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, 403);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->pcas->login();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        if ($request->query->has('ticket') || $this->pcas->isAuthenticated()) {
            return true;
        }

        return false;
    }
}
