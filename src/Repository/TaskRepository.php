<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function deleteTask($title)
    {
        if (!empty($title)) {
            return $this->createQueryBuilder('t')
                ->delete()
                ->where('t.title = :title')
                ->setParameter('title', $title)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        }
        return false;
    }
}