<?php

namespace App\Http\Controllers;

use App\Mail\CopyRequest;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Mail\MyDemoMail;
use Mail;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function myDemoMail()
    {
        $myEmail = 'jinsunglee.8033@gmail.com';

        $details = [
            'title' => 'This is title!!',
            'url' => 'https://www.kissusa.com'
        ];

        Mail::to($myEmail)->send(new MyDemoMail($details));
        return new MyDemoMail($details);
    }

    public function copy_request()
    {
        $toEmail = 'wendy@kisssusa.com';

        $details = [
            'title' => 'Copy Request!!',
            'body' => 'Created Email Blast Asset',
            'url' => 'https://www.kissusa.com'
        ];

        Mail::to($toEmail)->send(new CopyRequest($details));
        return new CopyRequest($details);
    }


}
