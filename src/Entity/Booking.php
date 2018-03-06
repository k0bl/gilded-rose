<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="booking")
 */
class Booking
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;

	 /**
     * @ORM\OneToMany(targetEntity="Room", inversedBy="bookings")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", onDelete="SET NULL")
     */
    public $rooms;

	/**
	 * @ORM\Column(type="integer")
	 */
	public $guests;

	/**
     * @ORM\Column(type="datetime")
	 */
	public $checkIn;

	/**
     * @ORM\Column(type="datetime")
	 */
	public $checkOut;


}