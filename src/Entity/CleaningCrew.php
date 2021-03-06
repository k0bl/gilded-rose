<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="cleaning_crew")
 * @JMS\ExclusionPolicy("All")
 */
class CleaningCrew
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $id;

    /**
     * @ORM\OneToMany(targetEntity="Cleaning", mappedBy="crew")
     */
    public $cleanings;

    /**
	 * @ORM\Column(type="integer")
	 */
	public $baseCleanTime;

    /**
	 * @ORM\Column(type="integer")
	 */
	public $occupantCleanTime;

}