<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountActivateType;
use App\Form\ChangePasswordFormType;
use App\Form\Model\AccountActivateModel;
use App\Form\Model\ChangePasswordFormModel;
use App\Form\Model\UserEditFormModel;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{

    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
        ]);
    }

    /**
     * @Route ("/account/edit", name="app_account_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserFormType::class);
        if ($this->handleFormRequest($form, $entityManager, $request)) {
            $this->addFlash('flash_message', "Данные успешно изменены!");
            return $this->redirectToRoute('app_account');
        }
        /** @var UserEditFormModel $user */
        $userModel = $form->getData();
        $form2 = $this->createForm(UserFormType::class, $userModel);
        $form2->handleRequest($request);
        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось изменить данные!");
        return $this->renderForm('account/account_edit.html.twig', [
            'userForm' => $form2,
            'show_error' => $form->isSubmitted(),
        ]);
    }

    private function handleFormRequest(
        FormInterface $form,
        EntityManagerInterface $entityManager,
        Request $request): ?User
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserEditFormModel $user */
            $userModel = $form->getData();
            /** @var User $user */
            $user = $this->getUser();
            $user
                ->setFirstName($userModel->getFirstName())
                ->setPatronymic($userModel->getPatronymic() ?? null)
                ->setSurname($userModel->getSurname())
                ->setBirthDate($userModel->getBirthDate());
            $entityManager->persist($user);
            $entityManager->flush();

            return $user;
        }
        return null;
    }

    /**
     * @Route ("/account/changepassword", name="app_account_changepassword")
     * @IsGranted("ROLE_USER")
     */
    public function changePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHash,
        Security $security
    ): Response
    {


        $user = $security->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangePasswordFormModel $passwordModel */
            $passwordModel = $form->getData();
            $user->setPassword($passwordHash->hashPassword(
                $user,
                $passwordModel->getPlainPassword()
            ));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('flash_message', "Пароль успешно изменен!");
            return $this->redirectToRoute('app_account_edit');
        }

        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось изменить пароль!");
        return $this->renderForm('account/account_edit_password.html.twig', [
            'userForm' => $form,
        ]);
    }

    /**
     * @Route("/account/activate", name="app_account_activate")
     * @IsGranted("ROLE_USER")
     */
    public function activate(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(AccountActivateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AccountActivateModel $model */
            $this->checkTimeForActivate($user, $form->getData()->getActivationCode());
            $user->setConfirmed(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_account');
        }

        return $this->renderForm('account/activate.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/account/fastactivate/{code}", name="app_account_fast_activate")
     * //     * @IsGranted("ROLE_USER")
     * @throws NonUniqueResultException
     */
    public function fastActivate(EntityManagerInterface $entityManager, string $code, UserRepository $repository): Response
    {
        if ($this->isGranted(['ROLE_USER'])) {
            /** @var User $user */
            $user = $this->getUser();
        } else {
            $user = $repository->getUserByCode($code);
        }
        if ($user){
            $this->checkTimeForActivate($user, $code);
            $user->setConfirmed(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_account');
        }else{
            $this->addFlash('flash_error','Пользователь не найден!');
            return $this->redirectToRoute('app_login');
        }

    }

    private function checkTimeForActivate(User $user, string $code)
    {
        if ($user->getConfirmed() <= new \DateTime('-2 min')) {
            if ($user->activateUser($code)) {
                $this->addFlash('flash_message', "E-mail успешно активирован!");
            } else {
                $this->addFlash('flash_error', 'Не верный код активации. Попробуйте снова через 2 минуты.');
            }
        } else {
            $this->addFlash('flash_error', 'Прошло мало времени. Ожидайте еще 2 минуты.');
        }
    }
}
