<?php

namespace OpenEuropa\pcas\Utils;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PCasSerializerFactory implements PCasSerializerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSerializer()
    {
        return new Serializer(
            [
                new ObjectNormalizer(),
            ],
            [
                new JsonEncoder(),
                new XmlEncoder(),
            ]
        );
    }
}
