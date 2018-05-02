<?php
namespace OpenEuropa\pcas\Security\Core\User;

/**
 * Class PCasUser.
 */
class PCasUser implements PCasUserInterface
{
    /**
     * The user storage.
     *
     * @var array
     */
    private $storage = [];

    /**
     * PCasUser constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->storage = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getUsername() ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        if ($roles = $this->get('cas:groups')) {
            if (isset($roles['cas:group']) && $roles = $roles['cas:group']) {
                return $roles;
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->get('cas:user');
    }

    /**
     * Get a value.
     *
     * @param string $key
     *   The key.
     *
     * @return mixed
     *   The value.
     */
    public function get($key)
    {
        return isset($this->getStorage()[$key]) ? $this->getStorage()[$key] : null;
    }

    /**
     * Get the storage.
     *
     * @return array
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
