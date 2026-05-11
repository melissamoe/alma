<?php

namespace App\Controllers;

use App\Core\View;

class HomeController
{
    public function index(): void
    {
        $view = new View();

        $view->render('home.html.twig', [
            'page_title' => 'ALMA 06 - Accueil'
        ]);
    }
}
