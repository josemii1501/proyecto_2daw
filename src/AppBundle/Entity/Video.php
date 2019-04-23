<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="video")
 */
class Video
{
    private $id;

    private $route;

    private $title;

    private $description;

    private $date;

    private $reproductions;

    private $miniature;

}