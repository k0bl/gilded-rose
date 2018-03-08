<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="booking")
 * @JMS\ExclusionPolicy("All")
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
     * @JMS\Expose
     */
    public $luggageItems;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    public $checkIn;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    public $checkOut;

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("room_number")
     */
    public function getRoomNumber() {
        return $this->room->roomNumber;
    }

}