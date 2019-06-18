<?php


namespace AppBundle\Repository;


use AppBundle\Entity\History;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }
    public function findAllHistories()
    {
        return $this->createQueryBuilder('h')
            ->addSelect('u')
            ->addSelect('v')
            ->join('h.video','v')
            ->leftJoin('h.usuario','u')
            ->orderBy('h.timestamp')
            ->getQuery()
            ->getResult();
    }
    public function findHistorial(Video $video, Usuario $usuario)
    {
        return $this->createQueryBuilder('h')
            ->addSelect('u')
            ->addSelect('v')
            ->join('h.video','v')
            ->leftJoin('h.usuario','u')
            ->where('u.id = '.$usuario->getId())
            ->where('v.id = '.$video->getId())
            ->orderBy('h.timestamp')
            ->getQuery()
            ->getResult();
    }
    public function findVistos(Usuario $usuario)
    {
        return $this->createQueryBuilder('h')
            ->addSelect('u')
            ->addSelect('v')
            ->join('h.video','v')
            ->leftJoin('h.usuario','u')
            ->where('u.id = '.$usuario->getId())
            ->orderBy('h.timestamp')
            ->getQuery()
            ->getResult();
    }
}