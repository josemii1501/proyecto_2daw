<?php

namespace AppBundle\Controller;

use AppBundle\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends Controller
{
    /**
     * @Route("/histories", name="historiales_listar")
     */
    public function HistoryListarAction(HistoryRepository $HistoryRepository)
    {
        $todosHistorys = $HistoryRepository->findAll();

        return $this->render('history/listar.html.twig', [
            'historiales' => $todosHistorys
        ]);
    }
}
