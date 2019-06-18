<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Suscription;
use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\CambioClaveType;
use AppBundle\Form\Type\UsuarioType;
use AppBundle\Repository\HistoryRepository;
use AppBundle\Repository\SavedRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioController extends Controller
{
    /**
     * @Route("/usuarios", name="usuarios_listar")
     */
    public function usuarioListarAction(UsuarioRepository $usuarioRepository)
    {
        $todosUsuarios = $usuarioRepository->findAll();

        return $this->render('user/listar.html.twig', [
            'usuarios' => $todosUsuarios
        ]);
    }
    /**
     * @Route("/canal/{id}", name="canal_usuario",
     *     requirements={"id":"\d+"})
     */
    public function videosUsuarioAction(Usuario $usuario, SubscriptionRepository $subscriptionRepository ,HistoryRepository $historyRepository,SavedRepository $savedRepository)
    {
        $suscrito = false;
        $videosGuardados = null;
        $videosVistos = null;
        if($this->getUser()){
            $videosGuardados = $savedRepository->findGuardados($usuario);
            $videosVistos = $historyRepository->findVistos($usuario);
            $estaSuscrito = $subscriptionRepository->findSuscritoUsuario($usuario,$this->getUser());
            if(empty($estaSuscrito)){
                $suscrito = false;
            } else {
                $suscrito = true;
            }
        }

        return $this->render('user/canal.html.twig', [
            'usuario' => $usuario,
            'guardados' => $videosGuardados,
            'vistos' => $videosVistos,
            'suscrito' => $suscrito
        ]);
    }
    /**
     * @Route("canal/suscribirse/{id}", name="suscribirse_canal",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function usuarioSuscritoAction(Usuario $usuario, SubscriptionRepository $subscriptionRepository,HistoryRepository $historyRepository,SavedRepository $savedRepository)
    {
        try {
            if ($this->getUser()) {
                $suscripcion = new Suscription();

                $suscripcion->setChanel($usuario)
                    ->setSuscriptor($this->getUser())
                    ->setTimestamp(new \DateTime());

                $this->getDoctrine()->getManager()->persist($suscripcion);
                $this->getDoctrine()->getManager()->flush();
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->videosUsuarioAction( $usuario,$subscriptionRepository,$historyRepository, $savedRepository);
    }
    /**
     * @Route("suscripcion/eliminar/{id}", name="eliminar_suscripcion_canal",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function eliminarSuscripcionAction(Usuario $usuario,SubscriptionRepository $subscriptionRepository,HistoryRepository $historyRepository, SavedRepository $savedRepository)
    {
        try {
            if ($this->getUser()) {
                $suscripcion = $subscriptionRepository->findSuscritoUsuario($usuario,$this->getUser());
                foreach($suscripcion as $item){
                    $this->getDoctrine()->getManager()->remove($item);
                    $this->getDoctrine()->getManager()->flush();
                }
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->videosUsuarioAction($usuario,$subscriptionRepository, $historyRepository, $savedRepository);
    }
    /**
     * @Route("/cambio_clave", name="cambio_clave")
     * @Security("is_granted('ROLE_USER')")
     */
    public function cambioClaveAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $usuario = $this->getUser();
        $form = $this->createForm(CambioClaveType::class, $usuario, [
            'es_admin' => false
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $usuario->setClave(
                    $passwordEncoder->encodePassword(
                        $usuario,
                        $form->get('nuevaClave')->getData()
                    )
                );
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Nueva contraseña guardada con éxito');
                return $this->redirectToRoute('portada');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar la contraseña');
            }
        }
        return $this->render('user/cambio_clave.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario
        ]);
    }
    /**
     * @Route("/usuarios/datos_personales", name="datos_personales")
     * @Security("is_granted('ROLE_USER')")
     */
    public function datosPersonales(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $usuario = $this->getUser();
        $form = $this->createForm(UsuarioType::class, $usuario, [
            'es_admin' => $this->isGranted('ROLE_USER')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setClave(
                $passwordEncoder->encodePassword(
                    $usuario,
                    $form->get('clave')->getData()
                )
            );
            try {
                /** @var File $filename */
                $file = $form->get('avatar')->getData();

                if ($file) {

                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            "uploads/avatar",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $usuario->setAvatar($fileName);
                }

                if($usuario->isPublisher() === null){
                    $usuario->setPublisher(false);
                }
                if($usuario->isAdmin() === null){
                    $usuario->setAdmin(false);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('usuarios_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
            return $this->render('user/form.html.twig', [
                'form' => $form->createView(),
                'usuario' => $usuario,
                'es_nueva' => $usuario->getId() === null
            ]);
        }
        return $this->render('user/personal.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario
        ]);
    }
    /**
     * @Route("/usuarios/nuevo", name="usuario_nuevo")
     */
    public function formNuevoUsuario(UserPasswordEncoderInterface $passwordEncoder,Request $request)
    {
        if($this->getUser()){
            $this->addFlash('error', 'Parece que ya tienes una cuenta');
            return $this->redirect('/');
        } else {
            $usuario = New Usuario();

            $this->getDoctrine()->getManager()->persist($usuario);

            return $this->formUsuarioAction($passwordEncoder, $request, $usuario);
        }

    }

    /**
     * @Route("/usuarios/modificar/{id}", name="usuario_modificar",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function formUsuarioAction(UserPasswordEncoderInterface $passwordEncoder, Request $request, Usuario $usuario)
    {
        if(null === $usuario) {
            $usuario = new Usuario();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(UsuarioType::class, $usuario, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            $usuario->setClave(
                $passwordEncoder->encodePassword(
                    $usuario,
                    $form->get('clave')->getData()
                )
            );
            try {
                /** @var File $filename */
                $file = $form->get('avatar')->getData();

                if ($file) {

                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            "uploads/avatar",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $usuario->setAvatar($fileName);
                } else {
                    if($new == true){
                        $usuario->setAvatar("avatar_predeterminado.png");
                    }
                }

                if($usuario->isPublisher() === null){
                    $usuario->setPublisher(false);
                }
                if($usuario->isAdmin() === null){
                    $usuario->setAdmin(false);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('usuarios_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
            return $this->render('user/form.html.twig', [
                'form' => $form->createView(),
                'usuario' => $usuario,
                'es_nueva' => $usuario->getId() === null
            ]);
        }


        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario,
            'es_nueva' => $usuario->getId() === null
        ]);
    }

    /**
     * @Route("/usuarios/eliminar/{id}", name="usuario_eliminar")
     * @Security("is_granted('ROLE_USER')")
     */
    public function eliminarAction(Request $request, Usuario $usuario, HistoryRepository $historyRepository, SavedRepository $savedRepository, SubscriptionRepository $subscriptionRepository, TokenStorageInterface $tokenStorage, Session $session)
    {
        if($usuario != $this->getUser()) {
            $this->addFlash('error', 'Sólo puedes borrarte tu mismo');
            return $this->redirect('/');
        }

        if ($request->get('borrar') === '') {


                try {
                    $videos_propios = $usuario->getVideos();

                    if ($videos_propios != null) {
                        foreach ($videos_propios as $item) {
                            $files_videos = $item->getFile();
                            $historiales = $item->getHistoriales();
                            $guardados = $item->getGuardados();

                            $this->getDoctrine()->getManager()->flush();
                            if ($files_videos != null) {
                                foreach ($files_videos as $item2) {
                                    $this->getDoctrine()->getManager()->remove($item2);

                                }
                            }

                            if ($guardados != null) {
                                foreach ($guardados as $item2) {
                                    $this->getDoctrine()->getManager()->remove($item2);

                                }
                            }
                            $this->getDoctrine()->getManager()->flush();
                            if ($historiales != null) {
                                foreach ($historiales as $item2) {
                                    $this->getDoctrine()->getManager()->remove($item2);

                                }
                            }
                            $this->getDoctrine()->getManager()->flush();
                            $this->getDoctrine()->getManager()->remove($item);
                        }
                    }
                    $mis_historiales = $historyRepository->findVistos($usuario);
                    if ($mis_historiales != null) {
                        foreach ($mis_historiales as $item2) {
                            $this->getDoctrine()->getManager()->remove($item2);

                        }
                    }
                    $this->getDoctrine()->getManager()->flush();

                    $mis_guardados = $savedRepository->findGuardados($usuario);
                    if ($mis_guardados != null) {
                        foreach ($mis_guardados as $item2) {
                            $this->getDoctrine()->getManager()->remove($item2);

                        }
                    }
                    $this->getDoctrine()->getManager()->flush();

                    $mis_suscripciones = $subscriptionRepository->findSuscripcionesUsuario($usuario);
                    if ($mis_suscripciones != null) {
                        foreach ($mis_suscripciones as $item2) {
                            $this->getDoctrine()->getManager()->remove($item2);

                        }
                    }
                    $this->getDoctrine()->getManager()->flush();

                    $mis_suscriptores = $subscriptionRepository->findSuscripcionesCanal($usuario);
                    if ($mis_suscriptores != null) {
                        foreach ($mis_suscriptores as $item2) {
                            $this->getDoctrine()->getManager()->remove($item2);

                        }
                    }
                    $this->getDoctrine()->getManager()->flush();
                    if (!$this->isGranted('ROLE_ADMIN')) {
                        $tokenStorage->setToken(null);
                        $session->invalidate();
                    }
                    $this->getDoctrine()->getManager()->remove($usuario);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('exito', 'Usuario Borrado Con Éxito');
                    return $this->redirectToRoute('usuarios_listar');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                    $this->addFlash('error', $e->getMessage());

                }

        }
        return $this->render('user/eliminar.html.twig', [
            'usuario' => $usuario
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


}
