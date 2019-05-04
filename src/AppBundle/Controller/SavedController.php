<?php

namespace AppBundle\Controller;

use AppBundle\Repository\SavedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SavedController extends Controller
{
    /**
     * @Route("/guardados", name="guardados_listar")
     */
    public function SavedListarAction(SavedRepository $savedRepository)
    {
        $todosGuardadoss = $savedRepository->findAll();

        return $this->render('saved/listar.html.twig', [
            'guardados' => $todosGuardadoss
        ]);
    }
}
