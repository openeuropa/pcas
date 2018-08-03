<?php

namespace OpenEuropa\pcas\Config;

class PcasConfig
{
    /**
     * @var string
     *  A logger message.
     */
    private $loggerStartupMessage;

    /**
    * @var string
    *  The login uri.
    */
    private $loginUri;

    /**
    * @var array
    *  A logger message.
    */
    private $loginQuery;

    /**
     * @var array
     *  An array with all allowed login parameters.
     */
    private $loginAllowedParams;

    /**
     * @var string
     *  The logout uri.
     */
    private $logoutUri;

    /**
     * @var array
     *  A logout query to be validated.
     */
    private $logoutQuery;

    /**
     * @var array
     *  An array with all allowed logout parameters.
     */
    private $logoutAllowedParams;

    /**
     * @var string
     *  The service validate uri.
     */
    private $serviceValidateUri;

    /**
     * @var array
     *  A query to be validated.
     */
    private $serviceValidateQuery;

    /**
     * @var array
     *  An array with all allowed query parameters.
     */
    private $serviceValidateAllowedParams;

    /**
     * PcasConfig constructor.
     *
     * @param string $loginUri
     * @param array $loginQuery
     * @param array $loginAllowedParams
     * @param string $logoutUri
     * @param array $logoutQuery
     * @param array $logoutAllowedParams
     * @param string $serviceValidateUri
     * @param array $serviceValidateQuery
     * @param array $serviceValidateAllowedParams
     * @param string $loggerStartupMessage
     */
    public function __construct(
        string $loginUri,
        array $loginQuery,
        array $loginAllowedParams,
        string $logoutUri,
        array $logoutQuery,
        array $logoutAllowedParams,
        string $serviceValidateUri,
        array $serviceValidateQuery,
        array $serviceValidateAllowedParams,
        string $loggerStartupMessage = ''
    ) {
        $this->setLoginUri($loginUri);
        $this->setLoginQuery($loginQuery);
        $this->setLoginAllowedParams($loginAllowedParams);
        $this->setServiceValidateUri($serviceValidateUri);
        $this->setserviceValidateQuery($serviceValidateQuery);
        $this->setServiceValidateAllowedParams($serviceValidateAllowedParams);
        $this->setLogoutUri($logoutUri);
        $this->setLogoutQuery($logoutQuery);
        $this->setLogoutAllowedParams($logoutAllowedParams);
        $this->setLoggerStartupMessage($loggerStartupMessage);
    }

    /**
     * @return string
     */
    public function getLoggerStartupMessage(): string
    {
        return $this->loggerStartupMessage;
    }

    /**
     * @param string $loggerStartupMessage
     */
    public function setLoggerStartupMessage(string $loggerStartupMessage)
    {
        $this->loggerStartupMessage = $loggerStartupMessage;
    }

    /**
     * @return string
     */
    public function getLoginUri(): string
    {
        return $this->loginUri;
    }

    /**
     * @param string $loginUri
     */
    public function setLoginUri(string $loginUri)
    {
        $this->loginUri = $loginUri;
    }

    /**
     * @return array
     */
    public function getLoginQuery(): array
    {
        return $this->loginQuery;
    }

    /**
     * @param array $loginQuery
     */
    public function setLoginQuery(array $loginQuery)
    {
        $this->loginQuery = $loginQuery;
    }

    /**
     * @return array
     */
    public function getLoginAllowedParams(): array
    {
        return $this->loginAllowedParams;
    }

    /**
     * Set the allowed parameters.
     *
     * @param array $loginAllowedParams
     */
    public function setLoginAllowedParams(array $loginAllowedParams)
    {
        $this->loginAllowedParams = $loginAllowedParams;
    }

    /**
     * @return string
     */
    public function getLogoutUri(): string
    {
        return $this->logoutUri;
    }

    /**
     * @param string $logoutUri
     */
    public function setLogoutUri(string $logoutUri)
    {
        $this->logoutUri = $logoutUri;
    }

    /**
     * @return array
     */
    public function getLogoutQuery(): array
    {
        return $this->logoutQuery;
    }

    /**
     * @param array $logoutQuery
     */
    public function setLogoutQuery(array $logoutQuery)
    {
        $this->logoutQuery = $logoutQuery;
    }

    /**
     * @return array
     */
    public function getLogoutAllowedParams(): array
    {
        return $this->logoutAllowedParams;
    }

    /**
     * @param array $logoutAllowedParams
     */
    public function setLogoutAllowedParams(array $logoutAllowedParams)
    {
        $this->logoutAllowedParams = $logoutAllowedParams;
    }

    /**
     * @return string
     */
    public function getServiceValidateUri(): string
    {
        return $this->serviceValidateUri;
    }

    /**
     * @param string $serviceValidateUri
     */
    public function setServiceValidateUri(string $serviceValidateUri)
    {
        $this->serviceValidateUri = $serviceValidateUri;
    }

    /**
     * @return array
     */
    public function getServiceValidateQuery(): array
    {
        return $this->serviceValidateQuery;
    }

    /**
     * @param array $serviceValidateQuery
     */
    public function setServiceValidateQuery(array $serviceValidateQuery)
    {
        $this->serviceValidateQuery = $serviceValidateQuery;
    }

    /**
     * @return array
     */
    public function getServiceValidateAllowedParams(): array
    {
        return $this->serviceValidateAllowedParams;
    }

    /**
     * @param array $serviceValidateAllowedParams
     */
    public function setServiceValidateAllowedParams(array $serviceValidateAllowedParams)
    {
        $this->serviceValidateAllowedParams = $serviceValidateAllowedParams;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return [
                'protocol' => [
                    'login' => [
                        'uri' => $this->getLoginUri(),
                        'query' => $this->getLoginQuery(),
                        'allowed_parameters' => $this->getLoginAllowedParams(),
                    ],

                    'logout' => [
                        'uri' => $this->getLogoutUri(),
                        'query' => $this->getLogoutQuery(),
                        'allowed_parameters' => $this->getLogoutAllowedParams(),
                    ],

                    'servicevalidate' => [
                        'uri' => $this->serviceValidateUri,
                        'query' => $this->getServiceValidateQuery(),
                        'allowed_parameters' => $this->getServiceValidateAllowedParams(),
                    ],
                ],

                'logger_startup_message' => $this->getLoggerStartupMessage(),
        ];
    }
}
