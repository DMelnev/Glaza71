<?php

namespace App\Controller;

use App\Entity\MainPage;
use App\Form\FileUploaderFormType;
use App\Form\MainPageFormType;
use App\Service\MyFiles;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/main/{id}", name="app_admin_main_pages")
     */
    public function mainPages(MainPage $mainPage, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(MainPageFormType::class, $mainPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MainPage $mainPage */
            $mainPage = $form->getData();
            $entityManager->persist($mainPage);
            $entityManager->flush();

            $this->addFlash('flash_message', "Данные успешно изменены!");
            return $this->redirectToRoute('app_admin_main_pages', [
                'id' => $mainPage->getId(),
            ]);
        }

        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось изменить данные!");
        return $this->renderForm('/admin/main_page.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/pictures", name="app_admin_pictures")
     */
    public function pictures(MyFiles $files, Request $request): Response
    {

        $form = $this->createForm(FileUploaderFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $this->addFlash('flash_message', $files->uploadFile($image));
            }
        }
//        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось сохранить файл!");

        return $this->renderForm('admin/pictures.html.twig', [
            'fileList' => $files->getListFiles('./img/'),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/pictures/{filename}/delete", name="app_admin_pictures_delete")
     */
    public function delete($filename, MyFiles $files): Response
    {
//        dd($filename);
        $this->addFlash('flash_error', $files->deleteFile($filename));
        return $this->redirectToRoute('app_admin_pictures');
    }
}
