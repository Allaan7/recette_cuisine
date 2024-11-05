<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RecetteType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManager;
use App\Form\CommentaireType;
use App\Form\UpdateType;

class RecetteController extends AbstractController
{
    #[Route('/add', name: 'app_add')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecetteType::class);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $recetteInfo = $form->getData();
            $date = new DateTimeImmutable();

            $file = $form->get('url_img')->getData();

            $fileName = uniqid() . "." . $file->guessExtension();
            $user = $this->getUser();

            $recetteInfo->setCreateAt($date);
            $recetteInfo->setUrlImg($fileName);
            $recetteInfo->setUser($user);

            $file->move($this->getParameter('upload_directory'), $fileName);

            $em->persist($recetteInfo);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('recette/recetteform.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/recettes', name: 'app_recettes')]
    public function getAllRecettes(RecetteRepository $repository)
    {
        $recettes = $repository->findAll();
        // dd($recettes);

        return $this->render('home/index.html.twig', [
            'recettes' => $recettes
        ]);
    }

    #[Route('like/{id}', name: 'app_like')]
    public function like(int $id, EntityManagerInterface $em, RecetteRepository $repository)
    {
        
        $recette = $repository->find($id);

        $user = $this->getUser(); // l'utilisateur qui veut liker

        if(!$user){
            return $this->redirectToRoute('app_login');
        }

        $recette->addUser($user); // ajou de l'utilisateur dans les likers de la recette

        $em->persist($recette);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }
    #[Route('/recette/{id}', name: 'app_recette')]
    public function recette(int $id, EntityManagerInterface $em, RecetteRepository $repository, Request $request)
    {
        $recette = $repository->find($id);
        $form = $this->createForm(CommentaireType::class);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $commentaire = $form->getData();
            $user = $this->getUser();
            $commentaire->setUser($user);
            $commentaire->setRecette($recette);

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('app_home');

        }
        


        return $this->render('recette/recette.html.twig', [
            'recette' => $recette,
            'form' => $form
        ]);
    }

    #[Route('/delete{id}', name: 'app_delete')]
    public function delete(int $id, EntityManagerInterface $em, RecetteRepository $repository)
    {
        $recette = $repository->find($id);
        $em->remove($recette);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }
    #[Route('/update/{id}', name: 'app_update')]
    public function update(int $id, EntityManagerInterface $em, RecetteRepository $repository, Request $request)
    {
        $form = $this->createForm(UpdateType::class);
        $recette = $repository->find($id);

        $form ->handleRequest($request);

        if($form->isSubmitted()){
            $updateRecette = $form->getData();

            $recette->setTitre($updateRecette->getTitre());
            $recette->setDescription($updateRecette->getDescription());

            $em->flush();
            return $this->redirectToRoute('app_home');

        }

        return $this->render('recette/update.html.twig', [
            'form' => $form,
            'recette' => $recette
        ]);
    }
}
