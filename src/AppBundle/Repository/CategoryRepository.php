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
            $item = $item+1;
            array_push($categorias2,$item);
        }
        return $this->createQueryBuilder('f')
            ->where('f.id IN (:lista)')
            ->setParameter('lista', $categorias2)
            ->getQuery()
            ->getResult();
    }
    public function findCategoryRand()
    {
        return $this->createQueryBuilder('f')
            ->addSelect('v')
            ->join('f.video','v')
            ->orderBy('f.date')
            ->getQuery()
            ->getResult();
    }
    public function findIdCategoria()
    {
        return $this->createQueryBuilder('f')
            ->select('f.id')
            ->getQuery()
            ->getResult();
    }
}