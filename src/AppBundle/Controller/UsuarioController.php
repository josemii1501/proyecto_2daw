<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends Controller
{
    /**
     * @Route("/usuarios", name="usuarios_listar")
     */
    public function indexAction()
    {
        return $this->render('user/listar.html.twig');
    }
}
