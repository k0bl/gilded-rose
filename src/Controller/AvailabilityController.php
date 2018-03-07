<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Room;
use App\Entity\Booking;

class AvailabilityController extends Controller
{
	/**
	 * Lists availability
	 * @Rest\Get("/availability")
	 * @return array
	 */
	public function getAvailabilityAction()
	{
        $roomRepo = $this->getDoctrine()->getRepository(Room::class);
        $availRooms = $roomRepo->findAvailableRooms(new \DateTime());
        // VarDumper::dump($availRooms);
		return View::create($availRooms, Response::HTTP_OK, []);
		// return $availRooms;
	}
}