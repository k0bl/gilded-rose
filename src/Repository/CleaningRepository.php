<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CleaningRepository extends EntityRepository
{
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
    public function findIncompleteCleanings()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.completed IS NULL');
        try {
            return $qb->getQuery()->getResult();
        } catch (NoResultException $e) {
        }
    }
}
