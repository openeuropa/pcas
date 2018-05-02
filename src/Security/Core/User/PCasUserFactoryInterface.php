<?php
namespace OpenEuropa\pcas\Security\Core\User;

interface PCasUserFactoryInterface
{
    /**
     * @param array $properties
     *
     * @return \OpenEuropa\pcas\Security\Core\User\PCasUserInterface
     */
    public function createUser(array $properties = []);
}
