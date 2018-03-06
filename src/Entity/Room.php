<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="room")
 */
class Room
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	public $maxOccupants

	/**
	 * @ORM\Column(type="integer")
	 */
	public $totalStorage

	/**
	 * @ORM\Column(type="integer")
	 */
	public $baseCost
    
    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="room")
     */
    public $bookings;
}