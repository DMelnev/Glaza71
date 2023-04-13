<?php


namespace App\Service;


use App\Entity\User;
use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailer
{
    private MailerInterface $mailer;
    private string $systemEmail;
    private string $emailName;

    public function __construct(MailerInterface $mailer, $systemEmail, $emailName)
    {
        $this->mailer = $mailer;
        $this->systemEmail = $systemEmail;
        $this->emailName = $emailName;
    }

    /**
     * @param User|string $user
     * @param string $subject
     * @param string $text
     * @param string $htmlTemplate
     * @param Closure|null $callback
     * @throws TransportExceptionInterface
     */
    private function send($user, string $subject, string $text, string $htmlTemplate, \Closure $callback = null)
    {
        if (is_string($user)) {
            $email = $user;
            $name = $user;
        } else {
            $email = $user->getEmail();
            $name = $user->getFirstName();
        }
        $email = (new TemplatedEmail())
            ->from(new Address($this->systemEmail, $this->emailName))
            ->to(new Address($email, $name))
            ->subject($subject)
            ->text($text)
            ->htmlTemplate($htmlTemplate)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
//            ->priority(TemplatedEmail::PRIORITY_HIGH)
        ;
        if ($callback) {
            $callback($email);
        }
        $this->mailer->send($email);

    }

    /**
     * @param User $user
     */
    public function sendWelcome(User $user)
    {
//        $email = (new Email())
//            ->from("gb2010@internet.ru")
//            ->to("dvmau@mail.ru")
//            ->subject('тест')
//            ->text('какой то текст');
//
//        try {
//            $this->mailer->send($email);
//        } catch (TransportExceptionInterface $e) {
//            dd($e->getMessage());
//        }

//        dd($user);
        $this->send(
            $user,
            'Добро пожаловать на сайт',
            'Ваш почтовый клиент не поддерживает В личном кабинете нажмите кнопку  и введите этот код: '.$user->getActivationCode(),
            'mailer/welcome.html.twig',
            function (TemplatedEmail $email) use ($user) {
                $email
                    ->context([
                        'user' => $user,
                    ]);
            }
        );


    }

}