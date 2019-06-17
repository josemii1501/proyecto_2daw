<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Suscription;
use AppBundle\Entity\Usuario;
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
    public function findSuscripcionesUsuario(Usuario $usuario)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u1')
            ->addSelect('u2')
            ->join('s.suscriptor','u1')
            ->where('s.suscriptor = :usuario')
            ->setParameter('usuario', $usuario)
            ->leftJoin('s.chanel','u2')
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
    public function findSuscripcionesCanal(Usuario $usuario)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u1')
            ->addSelect('u2')
            ->join('s.suscriptor','u1')
            ->where('s.chanel = :usuario')
            ->setParameter('usuario', $usuario)
            ->leftJoin('s.chanel','u2')
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
    public function findSuscritoUsuario(Usuario $canal,Usuario $suscriptor)
    {
        return $this->createQueryBuilder('s')
            ->where('s.suscriptor = '.$suscriptor->getId())
            ->where('s.chanel = '.$canal->getId())
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
}