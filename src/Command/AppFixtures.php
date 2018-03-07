<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\Room;
use App\Entity\Booking;
use App\Entity\Cleaning;
use App\Entity\CleaningCrew;

class AppFixtures extends ContainerAwareCommand
{
    const ROOM_BASE_COST = 10;
    const STORAGE_BASE_COST = 2;

    protected function configure()
    {
        $this->setName('app:fixtures:load');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	//Create the four rooms according to constraints.
    	$this->initializeRooms();

    	//Create the cleaning crew (gnomes!)
    	$this->initializeCleaningCrew();

    	//write to MySQL database
    	$this->getEM()->flush();    	

    }
    
    protected function initializeRooms() {
    	$this->getEM()->persist($this->createRoom(2, 1, 1));
    	$this->getEM()->persist($this->createRoom(2, 0, 2));
    	$this->getEM()->persist($this->createRoom(1, 2, 3));
    	$this->getEM()->persist($this->createRoom(1, 0, 4));
    }
    
    protected function createRoom($maxOccupants, $totalStorage, $roomNumber) {
    	$room = new Room();
    	$room->roomNumber = $roomNumber;
    	$room->maxOccupants = $maxOccupants;
    	$room->totalStorage = $totalStorage;
    	$room->baseCost = $this::ROOM_BASE_COST;
    	$room->baseStorageCost = $this::STORAGE_BASE_COST;
    	return $room;
    }
	
	protected function initializeCleaningCrew() {
    	$crew = new CleaningCrew();
    	$crew->baseCleanTime = 60;
    	$crew->occupantCleanTime = 30;
    	$this->getEM()->persist($crew);
    }
	
	//get doctrine entity manager for persistance
    protected function getEM()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
