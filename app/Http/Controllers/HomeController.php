<?php

namespace App\Http\Controllers;

use App\View\ViewModels\HomeViewModel;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return (new HomeViewModel())->view('index');
    }
}
