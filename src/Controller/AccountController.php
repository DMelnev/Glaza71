<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\Model\UserEditFormModel;
use App\Form\UserFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        if ($this->handleFormRequest($form, $entityManager, $request)){
            $this->addFlash('flash_message', "Данные успешно изменены!");
            return $this->redirectToRoute('app_account_edit',[
                'show_error' => $form->isSubmitted(),
            ]);
        }
        /** @var UserEditFormModel $user */
        $userModel = $form->getData();
        $form2 = $this->createForm(UserFormType::class, $userModel);
        $form2->handleRequest($request);
        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось изменить данные!");
        return $this->renderForm('account/account_edit.html.twig', [
            'show_error' => $form->isSubmitted(),
            'userForm' => $form2,
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
//            dd($userModel->getBirthDate());
            $user
                ->setFirstName($userModel->getFirstName())
                ->setPatronymic($userModel->getPatronymic()??null)
                ->setSurname($userModel->getSurname())
                ->setBirthDate($userModel->getBirthDate())
            ;
            $entityManager->persist($user);
            $entityManager->flush();

            return $user;
        }
        return null;
    }
}
