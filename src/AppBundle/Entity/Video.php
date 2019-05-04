<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="video")
 */
class Video
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
    private $route;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * \var int
     */
    private $reproductions;

    /**
     * @ORM\Column(type="string")
     * \var string
     */
    private $miniature;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="videos")
    * @ORM\JoinColumn(nullable=false)
    * @var User
    */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="video")
     * @ORM\JoinColumn(nullable=true)
     * @var Category
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\File", mappedBy="video")
     * @var File[]
     */
    private $file;

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
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Video
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Video
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Video
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Video
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReproductions()
    {
        return $this->reproductions;
    }

    /**
     * @param mixed $reproductions
     * @return Video
     */
    public function setReproductions($reproductions)
    {
        $this->reproductions = $reproductions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMiniature()
    {
        return $this->miniature;
    }

    /**
     * @param mixed $miniature
     * @return Video
     */
    public function setMiniature($miniature)
    {
        $this->miniature = $miniature;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     * @return Video
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Video
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return File[]
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File[] $file
     * @return Video
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }


    public function __toString()
    {
        return $this->getTitle().' ';
    }


}