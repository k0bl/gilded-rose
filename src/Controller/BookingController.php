<?php

namespace App\Controller;
use \DateInterval;
use \Datetime;
use App\Entity\Booking;
use App\Entity\Cleaning;
use App\Entity\CleaningCrew;
use App\Entity\Room;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingController extends Controller
{

    /**
     * Create Booking.
     * @Rest\Post("/booking")
     *
     * @return array
     */
    public function postBookingAction(Request $request)
    {
        /* checkInDate and checkOutDate will be set manually to the
        current time for check in and 8am the next day for check out. */
        $checkInDate = new DateTime();
        $checkOutDate = clone $checkInDate;
        $checkOutDate->add(new DateInterval("P1D")); //increment by one day
        $checkOutDate->setTime(8, 00, 00);

        //get next available room
        $room = $this->checkRoomAvailability(
            $request->get('luggage_items'), $checkInDate);

        //if we cannot find a room, we throw a 400 error
        if (!$room) {
            //View::create overrides the template system to return a JSON serialized response
            return View::create(['error'=>'Cannot complete booking. An error occurred'], 400);
        }
        //create new booking
        $booking = new Booking();

        $booking->luggageItems = $request->get('luggage_items');
        $booking->room = $room;

        $booking->checkIn = $checkInDate;
        $booking->checkOut = $checkOutDate;

        //get the cleaning crew to create a room service request
        $cleaningCrew = $this->getCleaningCrew();

        //create a cleaning or room service request for the room
        $cleaning = $this->getCleaning($room, $cleaningCrew);

        // increase the occupant count for the cleaning
        $cleaning->occupants = $cleaning->occupants + 1;

        // add occupantCleanTime to duration
        $cleaning->duration = $cleaning->duration +
            $cleaningCrew->occupantCleanTime;

        //get doctrine entity manager
        $em = $this->getDoctrine()->getManager();

        //persist booking and cleaning, flush to database
        $em->persist($booking);
        $em->persist($cleaning);
        $em->flush();

        //View::create overrides the template system to return a JSON serialized response
        return View::create($booking, Response::HTTP_CREATED , []);

    }

    /* check for incomplete cleaning, or return a new one */
    protected function getCleaning($room, $cleaningCrew)
    {
        $cleaningRepo = $this->getDoctrine()->getRepository(Cleaning::class);
        $incompleteCleaning = $cleaningRepo->findIncompleteCleaning($room->id);
        if ($incompleteCleaning) {
            return $incompleteCleaning;
        } else {
            $cleaning = new Cleaning();
            $cleaning->room = $room;
            $cleaning->crew = $cleaningCrew;
            $cleaning->duration = $cleaningCrew->baseCleanTime;
            return $cleaning;
        }
    }

    /* returns most profitable room for guest to stay in */
    protected function checkRoomAvailability($luggage, $checkIn)
    {
        $roomRepo = $this->getDoctrine()->getRepository(Room::class);
        $room = $roomRepo->findMostProfitableRoom($luggage, $checkIn);
        return $room;
    }

    /* get the cleaning crews and pop the first one off. We can extend this
    to support multiple cleaning crews in the future */
    protected function getCleaningCrew()
    {
        $cleaningCrews = $this->getDoctrine()->getRepository(CleaningCrew::class)
        ->findAll();
        if ($cleaningCrews) {
            return array_pop($cleaningCrews);
        }
    }
}