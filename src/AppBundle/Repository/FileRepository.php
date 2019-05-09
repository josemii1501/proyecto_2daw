<?php


namespace AppBundle\Repository;

use AppBundle\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }
    public function findAllFiles()
    {
        return $this->createQueryBuilder('f')
            ->addSelect('v')
            ->join('f.video','v')
            ->orderBy('f.date')
            ->getQuery()
            ->getResult();
    }
}