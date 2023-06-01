<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * This function display all ingredients
     *
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient',methods:['GET'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator,Request $request): Response
    {
        $ingredients = 
        
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This controller show the form to add an Ingredient in a database
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau',name:'app_ingredient_new',methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $manager):Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès !'
            );
    
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

   
    /**
     * This controller update the ingredient in the database 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}','app_ingredient_edit', methods:['GET','POST'] )]
    public function edit(IngredientRepository $ingredientRepository, int $id,Request $request,EntityManagerInterface $manager) : Response
    {

        $ingredient = $ingredientRepository->findOneBy(["id" => $id]) ;
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );
    
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig',[
            'form'=>$form->createview()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', 'app_ingredient_delete', methods:['GET'])]
    public function delete(EntityManagerInterface $manager,int $id,IngredientRepository $ingredientRepository) : Response
    {
        
        $ingredient = $ingredientRepository->findOneBy(["id"=>$id]);
        //vérification si l'ingrédient existe
        if(!$ingredient){
            $this->addFlash(
                'success',
                "Votre ingrédient n'a pas été trouvé !"
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
