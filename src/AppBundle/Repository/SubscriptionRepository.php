<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Suscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suscription::class);
    }
    public function findAllSubscriptions()
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u1')
            ->addSelect('u2')
            ->join('s.suscriptor','u1')
            ->leftJoin('s.chanel','u2')
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
}