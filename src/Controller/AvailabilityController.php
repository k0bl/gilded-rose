<?php
namespace App\Controller;
use App\Entity\Booking;
use App\Entity\Room;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
		return View::create($availRooms, Response::HTTP_OK, []);
	}
}