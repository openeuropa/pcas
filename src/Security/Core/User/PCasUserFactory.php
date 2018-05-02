<?php
namespace OpenEuropa\pcas\Security\Core\User;

class PCasUserFactory implements PCasUserFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUser(array $properties = [])
    {
        return new PCasUser($properties);
    }
}
