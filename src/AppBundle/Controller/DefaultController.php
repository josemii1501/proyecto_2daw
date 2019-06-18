<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\UsuarioRepository;
use AppBundle\Repository\VideoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="portada")
     */
    public function indexAction(CategoryRepository $categoryRepository, VideoRepository $videoRepository, UsuarioRepository $usuarioRepository)
    {
        $algunasCatregorias = $categoryRepository->findAlgunasCategorias();
        $algunosVideos = $videoRepository->findAlgunosVideos();
        $algunosUsuarios = $usuarioRepository->findAlgunosUsuarios();

        return $this->render('default/index.html.twig', [
            'categorias' => $algunasCatregorias,
            'videos' => $algunosVideos,
            'usuarios' => $algunosUsuarios
        ]);
    }
    /**
     * @Route("/administrador", name="admin_panel")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminAction()
    {


        return $this->render('default/admin.html.twig');
    }

}
