<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Usuario;
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
    public function findByUser(Usuario $usuario)
    {
        return $this->createQueryBuilder('v')
            ->addSelect('u')
            ->addSelect('c')
            ->where('v.creator = :usuario')
            ->setParameter('usuario', $usuario)
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
    public function findAlgunosVideos()
    {
        $videos_id = $this->findIdVideos();
        $videos = array_rand($videos_id,3);
        $videos_2 = [];
        foreach( $videos as $item){
            array_push($videos_2,$videos_id[$item]);
        }
        return $this->createQueryBuilder('v')
            ->where('v.id IN (:lista)')
            ->setParameter('lista', $videos_2)
            ->getQuery()
            ->getResult();
    }
    public function findIdVideos()
    {
        return $this->createQueryBuilder('v')
            ->select('v.id')
            ->getQuery()
            ->getResult();
    }
}