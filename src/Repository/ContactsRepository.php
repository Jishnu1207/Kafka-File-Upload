<?php

namespace App\Repository;

use App\Entity\Contacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Persistence\ManagerRegistry;

use function PHPUnit\Framework\isEmpty;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contacts::class);
    }

    public function add(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkMail($email, $row, $map)
    {
        $det = $this->findOneBy(['email' => $email]);
        if (empty($det))
        {
            $duplicate=false;
        }
        else
        {
            $size = sizeof($row);
            $det = $this->findOneBy(['email' => $email]);
            $date = new \DateTime('now');
            for ($i = 0;$i < $size;$i++)
            {
                if ( (!empty($map[$i])) && (!empty($row[$i])))
                    {
                        switch ($map[$i])
                        {
                            case "first_name" :
                                while ( ($det->getFirstName()) != $row[$i] )
                                {
                                $det->setFirstName($row[$i]);

                                $det->setModifiedDate($date);
                                }
                                    break;
                            case "last_name" :
                                while( ($det->getLastName()) != $row[$i] )
                                {
                                $det->setLastName($row[$i]);

                                $det->setModifiedDate($date);
                                }
                                    break;
                            case "company_name" :
                                while(($det->getCompanyName()) != $row[$i])
                                {
                                $det->setCompanyName($row[$i]);
                                
                                $det->setModifiedDate($date);
                                }
                                    break; 
                            case "city" :
                                while(($det->getCity()) != $row[$i])
                                {
                                $det->setCity($row[$i]);

                                $det->setModifiedDate($date);
                                }
                                    break;  
                            case "zip" :
                                while(($det->getZip()) != $row[$i])
                                {
                                $det->setZip($row[$i]);

                                $det->setModifiedDate($date);
                                }
                                    break; 
                            case "phone" :
                                while(($det->getPhone()) != $row[$i])
                                {
                                $det->setPhone($row[$i]);

                                $det->setModifiedDate($date);
                                }
                                    break;
                            default:
                            //do nothing
                                break;
                        }
                    }
            }
            $duplicate = true;
        }
        return $duplicate;
    }

    public function findByFileId($fileId,$pageNo,$sortField,$sortOrder,$email): array
   {
        $firstResult=(($pageNo-1)*3);
        return $this->createQueryBuilder('c')
                ->andWhere('c.file_id = :val AND c.email LIKE :val2')
                ->setParameter('val', $fileId)
                ->setParameter('val2', '%'.$email.'%')
                ->setFirstResult($firstResult)
                ->setMaxResults(3)
                ->orderBy('c.'.$sortField,$sortOrder)
                ->getQuery()
                ->getArrayResult()
       ;
   }

   public function recordCount($fileId): ?int
   {
        return $this->createQueryBuilder('c')
                ->select("count(c.id)")
                ->andWhere('c.file_id=:val')
                ->setParameter('val', $fileId)
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
