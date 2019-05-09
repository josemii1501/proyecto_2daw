<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }
    public function findAllVideos()
    {
        return $this->createQueryBuilder('v')
            ->addSelect('u')
            ->addSelect('c')
            ->join('v.creator','u')
            ->leftJoin('v.category','c')
            ->orderBy('v.date','DESC')
            ->getQuery()
            ->getResult();
    }
}