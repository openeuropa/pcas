<?php

namespace OpenEuropa\pcas\Utils;

use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class PCasSessionFactory implements PCasSessionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSession()
    {
        return new Session(
            new NativeSessionStorage(),
            new NamespacedAttributeBag(),
            new FlashBag()
        );
    }
}
