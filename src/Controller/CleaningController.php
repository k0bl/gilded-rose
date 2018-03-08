<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Room;
use App\Entity\Booking;
use App\Entity\Cleaning;

class CleaningController extends Controller
{
	/**
	 * Lists cleanings
	 * @Rest\Get("/cleaning_schedule")
	 * @return array
	 */
	public function getCleaningAction()
	{
        $cleaningsRepo = $this->getDoctrine()->getRepository(Cleaning::class);
        $cleanings = $cleaningsRepo->findIncompleteCleanings();
		return View::create($cleanings, Response::HTTP_OK, []);
	}

}