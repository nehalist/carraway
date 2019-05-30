<?php

namespace App\Repository;

use App\Entity\ContactRequest;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ContactRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRequest[]    findAll()
 * @method ContactRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContactRequest::class);
    }

    public function countByIpForDate(string $ip, DateTime $date)
    {
        return (int)$this->createQueryBuilder('cr')
                         ->select('COUNT(cr.id)')
                         ->andWhere('cr.ip = :ip')
                         ->andWhere('cr.createdAt > :date')
                         ->setParameters([
                             'ip'    => $ip,
                             'date'  => $date->format('Y-m-d')
                         ])
                         ->getQuery()
                         ->getSingleScalarResult();
    }
}
