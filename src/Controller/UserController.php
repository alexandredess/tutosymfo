<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * This controller allow to update the name and the pseudo of user
     * 
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/utilisateur/edition/{id}', name: 'app_user_edit', methods:['GET','POST'])]
    public function edit(UserRepository $userRepository,int $id,Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user =$userRepository->findOneBy(["id"=>$id]);
        //vérif si le user est connecté
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        //vérif si le user connecté est le même que nous avons récupéré
        if($this->getuser() !== $user){
            return $this->redirectToroute('app_recipe');
        }

        //création du formulaire
        $form = $this->createForm(UserType::class,$user);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){
                $user = $form->getData();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre compte ont bien été modifiées'
            );
            return $this->redirectToRoute('app_recipe');
            }
            else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}',name: 'user_edit_password', methods:['GET','POST'])]
    public function editPassword(UserRepository $userRepository,int $id,Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {
        //récupération du user par son $id
        $user =$userRepository->findOneBy(["id"=>$id]);

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user,$form->getData()['plainPassword'])){
                
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->getData()['newPassword']
                    )
                    );

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );
                return $this->redirectToRoute('app_recipe');
            }else{
                $this->addFlash(
                    'warning',
                    "Le mot de passe renseigné est incorrect"
                );
            }
        }
        
        return $this->render('pages/user/edit_password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
