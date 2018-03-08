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
	/**
	 * Patch cleaning by id to complete the cleaning
	 * @Rest\Patch("/cleaning_schedule/{id}")
	 * @return array
	 */
	public function completeCleaningAction($id)
	{
        $cleaningsRepo = $this->getDoctrine()->getRepository(Cleaning::class);
        $cleaning = $cleaningsRepo->findCleaningById($id);
        $cleaning->completed = new DateTime();

        $em = $this->getDoctrine()->getManager();
        //persist cleaning, flush to database
        $em->persist($cleaning);
        $em->flush();

		return View::create($cleaning, Response::HTTP_OK, []);
	}
}