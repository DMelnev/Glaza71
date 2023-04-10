<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('flash_error', "Не верный логин или пароль!");
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/sign_in.html.twig', ['last_username' => $lastUsername]);
    }

    /**
     * @Route("/register", name="app_register")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHash,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        EntityManagerInterface $em,
        Mailer $mailer
    ): ?Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $user = new User;
            $user
                ->setFirstName($userModel->getFirstName())
                ->setSurname($userModel->getSurname())
                ->setEmail($userModel->getEmail())
                ->setPassword($passwordHash->hashPassword(
                    $user,
                    $userModel->getPlainPassword()
                ))
                ->setRoles(['ROLE_USER'])
                ->setActivationCode(substr(md5($user->getPassword() . rand(0, 999)), rand(0, 15), 16));

            $em->persist($user);
            $em->flush();
            $mailer->sendWelcome($user);
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->renderForm('security/sign_up.html.twig', [
            "registrationForm" => $form,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
