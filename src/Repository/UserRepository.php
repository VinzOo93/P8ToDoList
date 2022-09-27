<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function deleteUser($email)
    {
        if (!empty($email)) {
            return $this->createQueryBuilder('u')
                ->delete()
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        }
    return false;
    }
}