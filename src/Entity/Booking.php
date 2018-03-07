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
	 * @ORM\ManyToOne(targetEntity="Room", inversedBy="bookings")
	 * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
	 */
    public $room;

	/**
	 * @ORM\Column(type="integer")
	 */
	public $luggageItems;

	/**
     * @ORM\Column(type="datetime")
	 */
	public $checkIn;

	/**
     * @ORM\Column(type="datetime")
	 */
	public $checkOut;


}