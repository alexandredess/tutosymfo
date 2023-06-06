<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * Show the all recipes
     *
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/recette', name: 'app_recipe', methods:['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator,Request $request): Response
    {
        
        $recipes = $paginator->paginate(
            $recipeRepository->findAll(),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recette/creation', name: 'app_recipe_new', methods:['GET','POST'])]
    public function new(Request $request,EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe =$form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès !'
            );

            return $this->redirectToRoute('app_recipe');

        }

        return $this->render('pages/recipe/new.html.twig',[
            'form' => $form->createView(),
        ]);
        
    }

    #[Route('/recette/edition/{id}', name:'app_recipe_edit',methods:['GET','POST'])]
    public function edit(RecipeRepository $recipeRepository,int $id,Request $request,EntityManagerInterface $manager):Response
    {
        $recipe = $recipeRepository->findOneBy(["id" => $id]) ;
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
            );
            return $this->redirectToRoute('app_recipe');
        }
            return $this->render('pages/recipe/new.html.twig',[
                'form'=>$form->createView()
        ]);
    }

    #[Route('/recette/suppression/{id}', 'app_recipe_delete', methods:['GET'])]
    public function delete(EntityManagerInterface $manager,int $id,RecipeRepository $ingredientRepository) : Response
    {
        
        $recipe = $ingredientRepository->findOneBy(["id"=>$id]);
        //vérification si l'ingrédient existe
        if(!$recipe){
            $this->addFlash(
                'success',
                "Votre recette n'a pas été trouvée !"
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );

        return $this->redirectToRoute('app_recipe');
    }
}