<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }
    public function findAlgunosUsuarios()
    {
        $usuarios_id = $this->findIdUsuarios();
        $usuarios = array_rand($usuarios_id,4);
        $usuarios_2 = [];
        foreach( $usuarios as $item){
            array_push($usuarios_2,$usuarios_id[$item]);
        }
        return $this->createQueryBuilder('v')
            ->where('v.id IN (:lista)')
            ->setParameter('lista', $usuarios_2)
            ->getQuery()
            ->getResult();
    }
    public function findIdUsuarios()
    {
        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->where('u.publisher = true')
            ->getQuery()
            ->getResult();
    }
}