<?php

namespace App\Http\Controllers;

use App\SmsMessage;
use Illuminate\Http\Request;

class SmsMessageController extends Controller
{
    public function index()
    {
        return SmsMessage::all();
    }
}
