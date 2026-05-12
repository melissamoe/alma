<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home.html.twig', [
            'page_title' => 'ALMA 06 - Accueil'
        ]);
    }
}