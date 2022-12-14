<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function add(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchMap($id)
    {
        $det = $this->findOneBy(['id' => $id]);
        print_r($det);
        $mapping = json_decode($det->getMapping());
        $date = new \DateTime('now');
        $det->setStatus(2);
        $det->setUploadedAt($date);
        $this->getEntityManager()->flush();
        return $mapping;
    }

    public function fetchname($id)
    {
        $det=$this->findOneBy(['id'=>$id]);
        $mapping=$det->getNewName();
        return $mapping;
    }

    public function saveMap($id,$mapping)

    {
        $det = $this->findOneBy(['id' => $id]);
        $det->setMapping($mapping);
        $this->getEntityManager()->flush();
    } 

    public function findList($pageNo,$sortField,$sortOrder): array
    {
        $firstResult=($pageNo-1)*3;
        return $this->createQueryBuilder('f')
                ->select("f")
                ->setMaxResults(3)
                ->setFirstResult($firstResult)
                ->orderBy('f.'.$sortField,$sortOrder)
                ->getQuery()
                ->getArrayResult();
   }

   public function recordCount(): ?int
   {
        return $this->createQueryBuilder('f')
                ->select("count(f.id)")
                ->getQuery()
                ->getSingleScalarResult();   
    }

//    /**
//     * @return File[] Returns an array of File objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?File
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
