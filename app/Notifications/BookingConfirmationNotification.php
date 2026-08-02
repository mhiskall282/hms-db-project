<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Booking Confirmation — Reference #{$this->booking->booking_reference}")
            ->greeting("Hello {$this->booking->guest->name},")
            ->line("Your reservation at " . config('hms.hotel_name') . " has been successfully confirmed!")
            ->line("Booking Reference: {$this->booking->booking_reference}")
            ->line("Room: Room {$this->booking->room->room_number} ({$this->booking->room->roomType->name})")
            ->line("Check-In Date: " . $this->booking->check_in_date->format('F j, Y'))
            ->line("Check-Out Date: " . $this->booking->check_out_date->format('F j, Y'))
            ->line("Nights: {$this->booking->nights} night(s)")
            ->line("Thank you for choosing " . config('hms.hotel_name') . ".");
    }
}
