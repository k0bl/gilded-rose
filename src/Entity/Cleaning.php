<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="cleaning")
 * @ORM\Entity(repositoryClass="App\Repository\CleaningRepository")
 * @JMS\ExclusionPolicy("All")
 */
class Cleaning
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Room", inversedBy="cleanings")
	 * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
	 */
    	public $room;

	/**
	 * @ORM\ManyToOne(targetEntity="CleaningCrew", inversedBy="cleanings")
	 * @ORM\JoinColumn(name="crew_id", referencedColumnName="id")
	 */
    	public $crew;

    	/**
	 * @ORM\Column(type="integer")
	 * @JMS\Expose
	 */
	public $occupants;

	/**
	 * @ORM\Column(type="integer")
	 * @JMS\Expose
	 */
	public $duration;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @JMS\Expose
	 */
	public $completed;

	/**
	 * @JMS\Expose
	 * @JMS\Type("DateTime")
	 */
	public $startTime;

	/**
	 * @JMS\Expose
	 * @JMS\Type("DateTime")
	 */
	public $endTime;

	/**
	* @JMS\VirtualProperty
	* @JMS\SerializedName("room_number")
	*/
	public function getRoomNumber() {
		return $this->room->roomNumber;
	}
}
