<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Saved;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Video;
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
    public function findGuardados(Usuario $usuario)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u')
            ->addSelect('v')
            ->join('s.video','v')
            ->leftJoin('s.usuario','u')
            ->where('u.id = '.$usuario->getId())
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
    public function findVideoUsuario(Usuario $usuario, Video $video)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('u')
            ->addSelect('v')
            ->join('s.video','v')
            ->leftJoin('s.usuario','u')
            ->where('u.id = '.$usuario->getId().' and v.id = '.$video->getId())
            ->orderBy('s.timestamp')
            ->getQuery()
            ->getResult();
    }
}