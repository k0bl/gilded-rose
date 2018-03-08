<?php
namespace App\Controller;

use \Datetime;
use App\Entity\Booking;
use App\Entity\Cleaning;
use App\Entity\Room;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

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
        return View::create($cleanings, Response::HTTP_OK, []);
    }

    /**
     * Patch cleaning by id to complete the cleaning
     * @Rest\Patch("/cleaning_schedule/{id}")
     * @return array
     */
    public function completeCleaningAction(int $id)
    {
        VarDumper::dump($id);
        $cleaningsRepo = $this->getDoctrine()->getRepository(Cleaning::class);
        $cleaning = $cleaningsRepo->find($id);
        VarDumper::dump($cleaning);
        if (!$cleaning) {
            throw new HttpException(400, "Cleaning does not exist");
        }
        if (!$cleaning->completed) {
            $cleaning->completed = new DateTime();
        } else {
            throw new HttpException(400, "Cannot complete cleaning, already completed.");
        }

        $em = $this->getDoctrine()->getManager();
        //persist cleaning, flush to database
        $em->persist($cleaning);
        $em->flush();

        return View::create($cleaning, Response::HTTP_OK, []);
    }
}