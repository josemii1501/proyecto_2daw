<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="suscription")
 */
class Suscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var User
     */
    private $suscriptor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var User
     */
    private $chanel;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     * @return Suscription
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return User
     */
    public function getSuscriptor()
    {
        return $this->suscriptor;
    }

    /**
     * @param User $suscriptor
     * @return Suscription
     */
    public function setSuscriptor($suscriptor)
    {
        $this->suscriptor = $suscriptor;
        return $this;
    }

    /**
     * @return User
     */
    public function getChanel()
    {
        return $this->chanel;
    }

    /**
     * @param User $chanel
     * @return Suscription
     */
    public function setChanel($chanel)
    {
        $this->chanel = $chanel;
        return $this;
    }

}