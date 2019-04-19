<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use App\Booking;
use App\Room;

class ConfirmInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The booking instance
     * 
     * @var Booking
     */
    public $booking;
    /**
     * The room instance
     * 
     * @var Room
     */
    public $room;


    public $location;
    public $eventOP;
    public $dateRange;
    public $duration;
    public $email;
    public $start;
    public $end;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $email, $dateRange, $duration, $eventOP, $room)
    {   
        $location   = explode("_", $room['name']);
        $building   = implode(" ", explode("-", $location[0]));
        $floor      = implode(" ", explode("-", $location[1]));
        $room       = $location[2];

        if(count($booking->dow) > 0) {

            $startRange = new \Carbon\Carbon($booking->ranges['start']);
            $endRange = new \Carbon\Carbon($booking->ranges['end']);

            $duration = explode(", ", $dateRange)[1];

            $dateRange = $startRange->toFormattedDateString() . " - " . $endRange->toFormattedDateString() . " (" .  $this->numToDay($booking->dow) . ")";
        }

        $this->booking      = $booking;
        $this->email        = $email;
        $this->eventOP      = $eventOP;
        $this->dateRange    = $dateRange;
        $this->duration     = $duration;
        $this->room         = Room::find($booking->room_id);
        $this->start        = new \Carbon\Carbon($booking->start); 
        $this->end          = new \Carbon\Carbon($booking->end); 
        $this->location     = $building . ', ' . $floor . ', ' . $room;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.confirm')
                    ->with([
                        'eventName' => $this->booking->title,
                        'details'   => $this->booking->description,
                        'username'  => $this->booking->username,
                    ]);
    }


    public function numToDay($dow) 
    {
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $string = '';
        $dowCount = count($dow);

        foreach($dow as $index => $day) {
            $string = $string . $days[$day];

            if($index + 1 < $dowCount) {
                $string = $string . ' ';
            } 
        }

        return $string;
    }
}
