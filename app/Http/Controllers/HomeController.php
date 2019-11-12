<?php

namespace App\Http\Controllers;

use App\Events\PrivateEvent;
use App\Events\PublicEvent;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function sendNotification(Request $request)
    {
        broadcast(new PublicEvent(auth()->user()));
        broadcast(new PrivateEvent(auth()->user()));
        $request->user()->notify(new UserNotification());

        return response()->json(["message"=>"Notification is sent to you."],200);
    }
}
