<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file")
 */
class File
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
    private $file;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Video", inversedBy="file")
     * @ORM\JoinColumn(nullable=true)
     * @var video
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return File
     */
    public function setFile($file)
    {
        $this->file = $file;
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
     * @return File
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param video $video
     * @return File
     */
    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

}