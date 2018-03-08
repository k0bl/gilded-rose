<?php
namespace App\Controller;

use \Datetime;
use App\Entity\Booking;
use App\Entity\Cleaning;
use App\Entity\Room;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

        $iterTime = new DateTime();
        $iterTime->setTime(8, 00, 00);

        foreach($cleanings as $cleaning) {
            $cleaning->startTime = clone $iterTime;
            $iterTime->add(new \DateInterval('PT'.$cleaning->duration.'M'));
            $cleaning->endTime = clone $iterTime;
        }
        //View::create overrides the template system to return a JSON serialized response
        return View::create($cleanings, Response::HTTP_OK, []);
    }

    /**
     * Patch cleaning by id to complete the cleaning
     * @Rest\Patch("/cleaning_schedule/{id}")
     * @return array
     */
    public function completeCleaningAction(int $id)
    {
        $cleaningsRepo = $this->getDoctrine()->getRepository(Cleaning::class);
        $cleaning = $cleaningsRepo->find($id);

        if (!$cleaning) {
            //View::create overrides the template system to return a JSON serialized response
            return View::create(['error'=>'Cannot complete cleaning. Cleaning does not exist.'],
                400);
        }
        if (!$cleaning->completed) {
            $cleaning->completed = new DateTime();
        } else {
            //View::create overrides the template system to return a JSON serialized response
            return View::create(['error'=>'Cannot complete cleaning. Cleaning already completed.'],
                400);
        }

        $em = $this->getDoctrine()->getManager();
        //persist cleaning, flush to database
        $em->persist($cleaning);
        $em->flush();

        //View::create overrides the template system to return a JSON serialized response
        return View::create($cleaning, Response::HTTP_OK, []);
    }
}