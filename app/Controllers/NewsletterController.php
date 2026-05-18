<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Mailer;
use App\Models\Subscriber;


class NewsletterController extends Controller
{
    public function subscribe(): void
    {
        $csrfToken = Request::post('csrf_token', '');

if (!Security::verifyCsrfToken($csrfToken)) {
    $_SESSION['error'] = "Requête invalide. Veuillez réessayer.";
    Response::redirect('/');
}

        $email = trim(Request::post('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Adresse email invalide.";
            Response::redirect('/');
        }

        $subscriberModel = new Subscriber();
        $existingSubscriber = $subscriberModel->findByEmail($email);

        $token = bin2hex(random_bytes(32));

        if ($existingSubscriber && $existingSubscriber['status'] === 'active') {
            $_SESSION['error'] = "Cette adresse email est déjà inscrite.";
            Response::redirect('/');
        }

        if ($existingSubscriber && $existingSubscriber['status'] === 'unsubscribed') {
            $subscriberModel->reactivate($email, $token);
        } else {
            $subscriberModel->create($email, $token);
        }

      // $this->sendConfirmationEmail($email, $token);

        $_SESSION['success'] = "Votre inscription à la newsletter a bien été prise en compte.";
        Response::redirect('/');
    }

    public function unsubscribe(): void
    {
        $token = Request::get('token', '');

        if (empty($token)) {
            $_SESSION['error'] = "Lien de désabonnement invalide.";
            Response::redirect('/');
        }

        $subscriberModel = new Subscriber();
        $subscriber = $subscriberModel->findByToken($token);

        if (!$subscriber) {
            $_SESSION['error'] = "Lien de désabonnement invalide ou expiré.";
            Response::redirect('/');
        }

        $subscriberModel->unsubscribeByToken($token);

        $_SESSION['success'] = "Vous êtes bien désabonné de la newsletter.";
        Response::redirect('/');
    }

    private function sendConfirmationEmail(string $email, string $token): void
    {
        $unsubscribeLink = rtrim($_ENV['APP_URL'], '/') . "/newsletter/unsubscribe?token=" . $token;

        $html = "
            <h2>Inscription confirmée</h2>
            <p>Bonjour,</p>
            <p>Votre inscription à la newsletter ALMA 06 a bien été prise en compte.</p>
            <p>Vous recevrez environ un email par mois concernant les actualités et les actions de l'association.</p>
            <p>
                Si vous souhaitez vous désabonner, cliquez ici :
                <a href=\"$unsubscribeLink\">Se désabonner</a>
            </p>
        ";

        $mailer = new Mailer();
        $mailer->send($email, "Confirmation d'inscription à la newsletter ALMA 06", $html);
    }
}
