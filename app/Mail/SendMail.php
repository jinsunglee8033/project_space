<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        $details = [
            'receiver'          =>  'MDM Team',
            'sender_team'       =>  'Ivy Lash (LD)',
            'sender_person'     =>  'Rachel Kim',
            'project_name'      =>  'LD ivy Project 1',
            'due_date'          =>  '10/29/2025',
            'c_id'              =>  1645,
            'url'               => '/admin/mm_request/1101/edit',

        ];

        return $this->markdown('emails.project_space_test')->with('details', $details);
    }

    public function email_send()
    {

//        Mail::to('jilee2@kissusa.com')->send(new SendMail());
        return new SendMail();
    }


}
