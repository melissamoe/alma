<?php

namespace App\Core;

class Response
{
    public static function notFound(): void
    {
        http_response_code(404);
        echo '404 - Page introuvable';
    }

    public static function forbidden(): void
    {
        http_response_code(403);
        echo '403 - Accès interdit';
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}