<?php
namespace OpenEuropa\pcas\Cas\Protocol\V2;

use OpenEuropa\pcas\Cas\Protocol\AbstractCasProtocol;
use OpenEuropa\pcas\Utils\StringUtils;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CasProtocolV2.
 */
class CasProtocolV2 extends AbstractCasProtocol
{
    /**
     * {@inheritdoc}
     */
    public function validateServiceTicket($serviceTicket, $requestPgt = false, $url = null)
    {
        if (empty($serviceTicket) || false === $serviceTicket) {
            return false;
        }

        $properties = $this->getProperties();

        $proxyGrantingTicketCallback = $requestPgt ?
            (isset($properties['pgt_callback']) ?
                $properties['pgt_callback'] : null) :
            null;

        $query = [
            'ticket'  => $serviceTicket,
            'service' => $this->currentUrl($url)->__toString(),
        ];

        $query += (array) $this->getSession()->get('pcas/query');

        /** @var ResponseInterface $response */
        $response = $this->getContainer()->get('pcas.httpclient')->request(
            $this->get('servicevalidate', $query)
        );

        if (200 === $response->getStatusCode()) {
            $validatedResponse = $this->validateResponse($response);

            // @todo: refactor.
            if (false === $validatedResponse) {
                return false;
            }
            $pCasUser = $validatedResponse;


            //check if a ProxyGrantingTicketIOU was set
            if ($requestPgt
                && StringUtils::isNotEmpty($proxyGrantingTicketCallback)
                && StringUtils::isNotEmpty($pCasUser->getPgtIOU())
            ) {
                $pgtiou = $pCasUser->getPgtIOU();

                //checking if we have a PGTIOU in the cache
                /*
                                if ($this->getCache()->has($pgtiou)) {
                                    $pCasUser->setProxyGrantingTicket(
                                      $this->getCache()->get(
                                        $pgtiou
                                      )->getValue()
                                    );

                                    $this->getLogger()->debug(sprintf(
                                      '
                              Found PGT %s for PGTIOU %s',
                                      $pCasUser->getProxyGrantingTicket(),
                                      $pgtiou
                                    ));
                                } else {
                                    $this->getLogger()->warn(sprintf(
                                      '
                              Could not find any PGT in the cache for IOU: %s',
                                      $pgtiou
                                    ));
                                }
                */
            }

            $this->getSession()->set('pcas/user', $pCasUser);

            return true;
        }

        return false;
    }

    /**
     * Parse the ticket service validation request body.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *   The response.
     *
     * @throws \Exception
     *
     * @return bool|\OpenEuropa\pcas\Security\Core\User\PCasUserInterface
     */
    private function validateResponse(ResponseInterface $response)
    {
        $root = $this->serializerFactory->createSerializer()
            ->decode($response->getBody()->__toString(), 'xml');

        if (false === $root) {
            // @todo: log
            return false;
        }

        //check if the validation was ok!
        if (!isset($root['cas:authenticationSuccess'])) {
            // @todo: log
            return false;
        }

        // @todo: log
        return $this->userFactory
            ->createUser($root['cas:authenticationSuccess']);
    }
}
