<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Saved;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SavedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Saved::class);
    }

    public function findAllSaved()
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u')
            ->addSelect('v')
            ->join('s.video','v')
            ->leftJoin('s.usuario','u')
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
}