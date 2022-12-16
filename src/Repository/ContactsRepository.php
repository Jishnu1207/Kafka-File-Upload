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

    public function checkMail($email,$row,$map)
    {
        $det=$this->findOneBy(['email'=>$email]);
        if(empty($det))
        {
            $val=false;
        }
        else
        {
            $size=sizeof($row);
            $det=$this->findOneBy(['email'=>$email]);
            $date=new \DateTime('now');
            for( $i=0; $i<$size; $i++)
            {
                if( (!empty($map[$i])) && (!empty($row[$i])))
                    {
                        switch($map[$i])
                        {
                            case "first_name" :
                                $det->setFirstName($row[$i]);
                                    break;
                            case "last_name" :
                                $det->setLastName($row[$i]);
                                    break;
                            case "company_name" :
                                $det->setCompanyName($row[$i]);
                                    break; 
                            case "city" :
                                $det->setCity($row[$i]);
                                    break;  
                            case "zip" :
                                $det->setZip($row[$i]);
                                    break; 
                            case "phone" :
                                $det->setPhone($row[$i]);
                                    break;
                            default:
                            $det->setModifiedDate($date);
                                break;
                        }
                    }
            }
            $val=true;
        }
        return $val;
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
