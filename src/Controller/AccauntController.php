<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserEditFormModel;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccauntController extends AbstractController
{
    /**
     * @Route("/account", name="app_accaunt")
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
    public function edit(Request $request, EntityManagerInterface $entityManager):Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tempUser = new UserEditFormModel();
        $tempUser->fillModel($user);
        $form = $this->createForm(UserType::class,$tempUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /** @var UserEditFormModel $userModel */
            $userModel = $form->getData();
            $user
                ->setFirstName($userModel->getFirstName())
                ->setPatronymic($userModel->getPatronymic())
                ->setSurname($userModel->getSurname())
                ->setBirthDate($userModel->getBirthDate())
            ;
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('flash_message', "Данные успешно изменены!");
            return $this->redirectToRoute('app_account_edit');
        }
        return $this->renderForm('account/account_edit.html.twig', [
            'show_error' => $form->isSubmitted(),
            'userForm' => $form,
        ]);
    }
}
