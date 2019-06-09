<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
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
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('v')
            ->addSelect('u')
            ->addSelect('c')
            ->where('v.creator = :usuario')
            ->setParameter('usuario', $user)
            ->join('v.creator','u')
            ->leftJoin('v.category','c')
            ->orderBy('v.date','DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByCategory(Category $category)
    {
        return $this->createQueryBuilder('v')
            ->addSelect('u')
            ->addSelect('c')
            ->where('v.category = :categoria')
            ->setParameter('categoria', $category)
            ->join('v.creator','u')
            ->leftJoin('v.category','c')
            ->orderBy('v.date','DESC')
            ->getQuery()
            ->getResult();
    }
}