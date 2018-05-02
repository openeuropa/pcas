<?php

namespace OpenEuropa\pcas\Utils;

interface PCasSessionFactoryInterface
{
    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function createSession();
}
