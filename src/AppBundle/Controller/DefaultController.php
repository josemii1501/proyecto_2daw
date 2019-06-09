<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="portada")
     */
    public function indexAction(CategoryRepository $categoryRepository)
    {
        $algunasCatregorias = $categoryRepository->findAlgunasCategorias();

        return $this->render('default/index.html.twig', [
            'categorias' => $algunasCatregorias
        ]);
    }
}
