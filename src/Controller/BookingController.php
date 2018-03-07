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
use Symfony\Component\VarDumper\VarDumper;

class BookingController extends Controller
{
    /**
     * Lists Bookings
     * @Rest\Get("/booking")
     * @return array
     */
    public function getBookingAction()
    {
        $repo = $this->getDoctrine()->getRepository(Booking::class);
        $avail = $repo->findall();
        // return View::create($avail, Response::HTTP_OK, []);
        return $avail;
    }

    /**
     * Create Booking.
     * @Rest\Post("/booking")
     *
     * @return array
     */
    public function postBookingAction(Request $request)
    {
        /* checkInDate and checkOutDate will be set manually to 4 pm
        for check in and 8am for check out. We can expand this in the
        future to allow us to accept a checkInDate and checkOutDate
        from the request for future reservations.
        */
        $checkInDate = new DateTime();
        $checkInDate->setTime(16, 00, 00);
        $checkOutDate = clone $checkInDate;
        $checkOutDate->add(new DateInterval("P1D")); //add one day
        $checkOutDate->setTime(8, 00, 00);

        //get next available room
        $room = $this->checkRoomAvailability(
            $request->get('luggage_items'), $checkInDate);

        //if we cannot find a room, we throw a 400 error
        if (!$room) {
            throw new HttpException(400, "Cannot complete booking.
                An error occurred");
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

        // update cleaning duration for room, add occupantCleanTime
        $cleaning->duration = $cleaning->duration +
            $cleaningCrew->occupantCleanTime;

        //get doctrine entity manager
        $em = $this->getDoctrine()->getManager();
        //persist booking and cleaning, flush to database
        $em->persist($booking);
        $em->persist($cleaning);
        $em->flush();

        return View::create($room->roomNumber, Response::HTTP_CREATED , []);

    }

    /* check for incomplete cleaning, or return a new one */
    protected function getCleaning($room, $cleaningCrew)
    {

    }

    /* returns most profitable room for guest to stay in */
    protected function checkRoomAvailability($luggage, $checkIn)
    {

    }
    /* get the cleaning crews and pop the first one off. We can extend this
    to support multiple cleaning crews in the future */
    protected function getCleaningCrew()
    {

    }

}