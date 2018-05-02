<?php

namespace OpenEuropa\pcas\Security\Core\User;

interface PCasUserInterface
{
    /**
     * Returns the roles granted to the user.
     *
     * @return string[]
     *   The user roles
     */
    public function getRoles();

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string
     *   The username
     */
    public function getUsername();
}
