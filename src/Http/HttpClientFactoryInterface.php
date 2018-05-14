<?php

namespace OpenEuropa\pcas\Http;

use Http\Client\Common\Plugin;

interface HttpClientFactoryInterface
{
    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin);

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param string $fqcn
     */
    public function removePlugin($fqcn);

    /**
     * Get an HTTP client.
     *
     * @return \OpenEuropa\pcas\Http\HttpClientInterface
     */
    public function getHttpClient();
}
