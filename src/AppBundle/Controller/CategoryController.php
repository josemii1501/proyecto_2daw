<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categorias_listar")
     */
    public function categoryListarAction(CategoryRepository $categoryRepository)
    {
        $todoscategorias = $categoryRepository->findAll();

        return $this->render('category/listar.html.twig', [
            'categorias' => $todoscategorias
        ]);
    }
}
