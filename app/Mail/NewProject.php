<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class NewProject extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $task_name = $this->details['task_name'];
        $c_id = $this->details['c_id'];
        $team = $this->details['team'];

        return $this->subject('New Project Created [' . $team . '] - ' . $task_name . ' #' . $c_id)->markdown('emails.new_project')
            ->with('details', $this->details);
    }

}
