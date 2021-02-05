<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'home', 'contact']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        //dd(Auth::check());
        //dd(Auth::id());
        //dd(Auth::user());
        return view('home.index');
    }

    public function index()
    {
        return view('home.index');
    }

    /**
     * 
     */
    public function contact()
    {
        return view('home.contact');
    }
}
