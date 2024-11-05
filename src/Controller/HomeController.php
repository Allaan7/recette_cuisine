<?php

namespace App\Controller;

use App\Form\RegisterType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_recettes');
    }
    
    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher )
    {
        // Créer le form
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request); //  pour dtecter la soumission du formulaire

        if($form->isSubmitted()){
            $userInfo = $form->getData();
            $date = new DateTimeImmutable();

            $userInfo->setRoles(['ROLE_USER']);
            $userInfo->setCreateAt($date);
            $userInfo->setPassword($hasher->hashPassword($userInfo, $userInfo->getPassword()));

            $em->persist($userInfo);
            $em->flush();

            return $this->redirectToRoute('app_home');

        }

        return $this->render('home/inscription.html.twig', [
            'form' => $form
        ]);
    }
}
