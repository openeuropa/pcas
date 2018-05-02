<?php

namespace OpenEuropa\pcas\Utils;

interface PCasSerializerFactoryInterface
{
    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    public function createSerializer();
}
