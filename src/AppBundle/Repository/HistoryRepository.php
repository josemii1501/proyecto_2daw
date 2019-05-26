<?php


namespace AppBundle\Repository;


use AppBundle\Entity\History;
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
            ->leftJoin('h.user','u')
            ->orderBy('h.timestamp')
            ->getQuery()
            ->getResult();
    }
}