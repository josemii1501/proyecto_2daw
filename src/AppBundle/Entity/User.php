<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    private $id;

    private $name;

    private $lastname;

    private $email;

    private $phone;

    private $birthday;

    private $login;

    private $password;

    private $avatar;

    private $urlWebSite;

    private $description;

    private $publisher;

    private $admin;
}