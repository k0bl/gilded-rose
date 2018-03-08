<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CleaningRepository extends EntityRepository
{
    /* Find cleaning by roomId that has not been completed */
    public function findIncompleteCleaning($roomId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.room = :room')
        ->andWhere('c.completed IS NULL')
        ->setParameter('room', $roomId);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
        }
    }

    /* Retreive cleanings from the database. Order by duration, and then
    storage because it is better value for the business. */
    public function findIncompleteCleanings()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.room', 'r')
            ->where('c.completed IS NULL')
            ->orderBy('c.duration', 'desc')
            ->orderBy('r.totalStorage', 'desc');
        try {
            return $qb->getQuery()->getResult();
        } catch (NoResultException $e) {
        }
    }
}
