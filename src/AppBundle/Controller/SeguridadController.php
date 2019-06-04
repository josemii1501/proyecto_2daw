<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SeguridadController extends Controller
{
    /**
     * @Route("/entrar", name="usuario_entrar")
     */
    public function indexAction()
    {
        // formulario de entrada
        return $this->render('seguridad/entrar.html.twig');
    }
    /**
     * @Route("/salir", name="usuario_salir")
     */
    public function salirAction()
    {
    }
}
