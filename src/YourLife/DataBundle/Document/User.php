<?php

namespace YourLife\DataBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document(collection="users")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $username;

    /**
     * @MongoDB\String
     */
    protected $password;

    /**
     * @MongoDB\String
     */
    protected $passwordSalt;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     */
    protected $firstname;

    /**
     * @MongoDB\String
     */
    protected $lastname;

    /**
     * @MongoDB\Int
     */
    protected $rating;

    /**
     * @MongoDB\Int
     */
    protected $points;

    /**
     * @MongoDB\Int
     */
    protected $level;

    public function __construct()
    {
        $this->passwordSalt = sha1(uniqid(null, true));
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt()
    {
        return $this->passwordSalt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize([$this->id]);
    }

    public function unserialize($serialized)
    {
        list ( $this->id ) = unserialize($serialized);
    }
}