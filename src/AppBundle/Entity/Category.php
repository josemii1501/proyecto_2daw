<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Video", mappedBy="category")
     * @var Video[]
     */
    private $video;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return Category
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Video[]
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param Video[] $video
     * @return Category
     */
    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    public function __toString()
    {
        return $this->getName().'';
    }


}