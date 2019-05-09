<?php

namespace AppBundle\Controller;

use AppBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends Controller
{
    /**
     * @Route("/files", name="Files_listar")
     */
    public function FileListarAction(FileRepository $FileRepository)
    {
        $todosFiles = $FileRepository->findAllFiles();

        return $this->render('file/listar.html.twig', [
            'archivos' => $todosFiles
        ]);
    }
}
