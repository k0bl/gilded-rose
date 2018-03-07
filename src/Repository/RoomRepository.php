<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RoomRepository extends EntityRepository
{
    public function findAvailableRooms($checkIn)
    {
        $qb = $this->getAvailableRoomsQueryBuilder($checkIn);
        try {
            $results =  $qb->getQuery()->getResult();
            $output = [];
            foreach($results as $result) {
                $output[] = $result[0];
            }
            return $output;
        } catch (NoResultException $e) {
        }
    }

    private function getAvailableRoomsQueryBuilder($checkIn)
    {
           $qb = $this->createQueryBuilder('r');
            $qb->leftJoin('r.bookings', 'b')
            ->leftJoin('r.cleanings', 'c')
            ->addGroupBy('r.id')
            ->andWhere(
                $qb->expr()->orX(
                    'c IS NULL',
                    'c.completed IS NULL',
                    'c.completed < :checkIn',
                    'c.occupants < r.maxOccupants'
                )
            )
            ->having('count(b) < r.maxOccupants OR count(b) = 0')
            ->setParameter('checkIn', $checkIn);
            return $qb;
    }

    public function findAvailableRoomById($id, $checkIn)
    {
        $qb = $this->getAvailableRoomsQueryBuilder($checkIn);
        $qb->andWhere('r.id = :id')
        ->setParameter('id', $id);
        try {
            $result = $qb->getQuery()->getSingleResult();
            if ($result) {
                return $result[0];
            }
        } catch (NoResultException $e) {
        }
    }
    public function findMostProfitableRoom($luggage = 0, $checkIn)
    {
        $qb = $this->getAvailableRoomsQueryBuilder($checkIn);
        $qb->addSelect('(r.maxOccupants - count(b))
                as availableOccupants')
            ->addSelect('(r.totalStorage - sum(b.luggageItems))
                as availableStorage')
            ->andHaving('(r.totalStorage - sum(b.luggageItems)) >= :luggage OR :luggage = 0 OR (count(b) = 0 AND :luggage <= r.totalStorage)')
            ->orderBy('availableOccupants', 'desc')
            ->orderBy('availableStorage', 'desc')
            ->setMaxResults(1)
            ->setParameter('luggage', $luggage);
        try {
            $result = $qb->getQuery()->getSingleResult();
            if ($result) {
                return $result[0];
            }
        } catch (NoResultException $e) {
        }
    }
}
