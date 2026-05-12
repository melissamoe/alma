<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        $view = new View();
        $view->render($template, $data);
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}