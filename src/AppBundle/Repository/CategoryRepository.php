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


        return $this->createQueryBuilder('f')
            ->where('f.id IN (:lista)')
            ->setParameter('lista', $categorias)
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