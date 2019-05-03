<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(type="date")
     * @var \DateTime
     */
    private $birthday;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $login;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $avatar;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $urlWebSite;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $publisher;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $admin;
}