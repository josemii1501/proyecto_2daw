<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    public function findAlgunasCategorias()
    {

        $cat = $this->findIdCategoria();
        $categorias = array_rand($cat,3);
        $categorias2 = [];
        foreach( $categorias as $item){
            array_push($categorias2,$cat[$item]);
        }
        return $this->createQueryBuilder('c')
            ->where('c.id IN (:lista)')
            ->setParameter('lista', $categorias2)
            ->getQuery()
            ->getResult();
    }
    public function findIdCategoria()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getResult();
    }
}