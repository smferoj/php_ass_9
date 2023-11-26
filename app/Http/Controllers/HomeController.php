<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homePage()
    {
        return view('home'); 
    }

    public function aboutPage()
    {
        return view('about'); // Assuming you have a Blade file named about.blade.php
    }
    public function projectsPage()
    {
        return view('projects'); // Assuming you have a Blade file named about.blade.php
    }
    public function contactPage()
    {
        return view('contact'); // Assuming you have a Blade file named about.blade.php
    }
}