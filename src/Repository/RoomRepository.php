<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RoomRepository extends EntityRepository
{
    //find all rooms that are available right now
    public function findAvailableRooms($checkIn)
    {
        $qb = $this->getAvailableRoomsQueryBuilder($checkIn);
        try {
            $results =  $qb->getQuery()->getResult();
            return $results;
        } catch (NoResultException $e) {
        }
    }

    //base query builder for finding rooms that are available right now
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

    //return a room by id if it is available
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

    /*Find the most profitable room for the guest to stay in. If the guest has 1
    piece of luggage, make sure we put them in a room with 1 storage space if possible. If a guest has no luggage, make sure we put them in a room with no
    storage space. This will leave the rooms with the most storage space available
    for a higher paying guest with 2 pieces of luggage */
    public function findMostProfitableRoom($luggage = 0, $checkIn)
    {
        $qb = $this->getAvailableRoomsQueryBuilder($checkIn);
        $qb->addSelect('(r.maxOccupants - count(b))
                as availableOccupants')
            ->addSelect('(r.totalStorage - coalesce(sum(b.luggageItems), 0))
                as availableStorage')
            ->andHaving('(r.totalStorage - sum(b.luggageItems)) >= :luggage OR :luggage = 0 OR (count(b) = 0 AND :luggage <= r.totalStorage)')
            ->orderBy('availableOccupants', 'desc')
            ->orderBy('availableStorage')
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
