<?php

namespace App\Mailer;


use App\Entity\User;

class Mailer
{
    /**
     * @param \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @param \Twig_Environment $twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $mailFrom;


    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment, string $mailFrom)
    {
        $this->mailer   = $mailer;
        $this->twig     = $twig_Environment;
        $this->mailFrom = $mailFrom;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('Email/registration.html.twig',
            [
                'user' => $user
            ]);

        $message = (new \Swift_Message())
            ->setSubject('Welcome to the micro_post app!')
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}