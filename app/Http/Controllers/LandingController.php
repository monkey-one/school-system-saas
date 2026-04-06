<?php

namespace App\Http\Controllers;

// Serves the public marketing landing page for the EduSaaS platform.
class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }
}
