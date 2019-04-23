<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="history")
 */
class History
{
    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

}