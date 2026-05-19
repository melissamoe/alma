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

    

        $this->sendConfirmationEmail($email, $token);

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

       $baseUrl = rtrim($_ENV['APP_URL'], '/');

$logoUrl = $baseUrl . "/assets/images/logo1.png";
$flyerRectoUrl = $baseUrl . "/assets/images/flyer-recto.jpg";
$flyerVersoUrl = $baseUrl . "/assets/images/flyer-verso.jpg";

$html = "
<div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 750px; margin: auto; padding: 20px;'>

    <div style='text-align:center; margin-bottom: 25px;'>
        <img src='$logoUrl'
             alt='Logo ALMA 06'
             style='max-width: 160px; height: auto; margin-bottom: 15px;'>

        <h1 style='color:#1f4f9a; margin:0;'>Bienvenue à ALMA 06</h1>
    </div>

    <p>Bonjour,</p>

    <p>
        Nous vous remercions pour votre inscription à la newsletter de <strong>l’association ALMA 06</strong>.
    </p>

    <p>
        En rejoignant notre liste de diffusion, vous serez informé(e) des actualités, actions de sensibilisation,
        événements et initiatives menées par notre association dans le cadre de la lutte contre les maltraitances
        et de la promotion de la bientraitance.
    </p>

    <p>
        Notre objectif est de continuer à informer, sensibiliser et accompagner les personnes concernées,
        ainsi que leurs proches et les professionnels.
    </p>

    <div style='margin: 35px 0; text-align:center;'>
        <img src='$flyerRectoUrl'
             alt='Flyer ALMA 06 recto'
             style='max-width:100%; height:auto; border-radius:10px; margin-bottom:20px;'>

        <img src='$flyerVersoUrl'
             alt='Flyer ALMA 06 verso'
             style='max-width:100%; height:auto; border-radius:10px;'>
    </div>

    <p>
        Vous recevrez environ un email par mois contenant les informations importantes concernant l’association.
    </p>

    <p>
        Si vous souhaitez vous désabonner à tout moment, vous pouvez utiliser le lien ci-dessous :
    </p>

    <p style='text-align:center; margin: 30px 0;'>
        <a href='$unsubscribeLink'
           style='background-color:#1f4f9a; color:white; padding:12px 24px;
                  text-decoration:none; border-radius:6px; display:inline-block;'>
            Se désabonner
        </a>
    </p>

</div>
";

        $mailer = new Mailer();
        $mailer->send($email, "Bienvenue dans la newsletter ALMA 06", $html);
    }
}
