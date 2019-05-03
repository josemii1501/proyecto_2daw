<?php

namespace AppBundle\Controller;

use AppBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends Controller
{
    /**
     * @Route("/videos", name="videos_listar")
     */
    public function videoListarAction(VideoRepository $videoRepository)
    {
        $todosvideos = $videoRepository->findAll();

        return $this->render('video/listar.html.twig', [
            'videos' => $todosvideos
        ]);
    }
}
