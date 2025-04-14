<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BillUploaded extends Notification
{
    use Queueable;
    protected $portalBill;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($portalBill)
    {
        $this->portalBill=$portalBill;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Bill Uploaded',
            'message' => "{$this->portalBill->name} has uploaded a bill.",
            'bill_id' => $this->portalBill->id,
            'bill_url' => asset('storage/' . $this->portalBill->bill),
        ];           
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
