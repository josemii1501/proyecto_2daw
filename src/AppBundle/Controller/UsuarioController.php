<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\CambioClaveType;
use AppBundle\Form\Type\UsuarioType;
use AppBundle\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/usuarios/canal/{id}", name="canal_usuario",
     *     requirements={"id":"\d+"})
     */
    public function videosUsuarioAction(User $user)
    {

        return $this->render('user/canal.html.twig', [
            'usuario' => $user
        ]);
    }
    /**
     * @Route("/clave", name="cambio_clave")
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
    public function datosPersonales(Request $request)
    {
        $usuario = $this->getUser();
        $form = $this->createForm(UsuarioType::class, $usuario, [
            'es_admin' => $this->isGranted('ROLE_USER')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Datos personales guardados con éxito');
                return $this->redirectToRoute('portada');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los datos personales');
            }
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
        $usuario = New User();

        $this->getDoctrine()->getManager()->persist($usuario);

        return $this->formUsuarioAction($passwordEncoder, $request, $usuario);
    }

    /**
     * @Route("/usuarios/modificar/{id}", name="usuario_modificar",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function formUsuarioAction(UserPasswordEncoderInterface $passwordEncoder, Request $request, User $user)
    {
        if(null === $user) {
            $user = new $user();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(UsuarioType::class, $user, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            $user->setClave(
                $passwordEncoder->encodePassword(
                    $user,
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
                            "uploads/avatar_photo",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $user->setAvatar("uploads/avatar_photo/" . $fileName);
                }

                if($user->isPublisher() === null){
                    $user->setPublisher(false);
                }
                if($user->isAdmin() === null){
                    $user->setAdmin(false);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('usuarios_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
            return $this->render('user/form.html.twig', [
                'form' => $form->createView(),
                'usuario' => $user,
                'es_nueva' => $user->getId() === null
            ]);
        }


        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
            'usuario' => $user,
            'es_nueva' => $user->getId() === null
        ]);
    }

    /**
     * @Route("/usuarios/eliminar/{id}", name="usuario_eliminar")
     * @Security("is_granted('ROLE_USER')")
     */
    public function eliminarAction(Request $request, User $user)
    {
        if ($request->get('borrar') === '') {
            try {
                $videos_propios = $user->getVideos();

                if($videos_propios != null) {
                    foreach ($videos_propios as $item){
                        $files_videos = $item->getFile();
                        $historiales = $item->getHistoriales();
                        $guardados = $item->getGuardados();

                        $this->getDoctrine()->getManager()->flush();
                        if($files_videos != null) {
                            foreach ($files_videos as $item2){
                                $this->getDoctrine()->getManager()->remove($item2);

                            }
                        }

                        if($guardados != null) {
                            foreach ($guardados as $item2){
                                $this->getDoctrine()->getManager()->remove($item2);

                            }
                        }
                        $this->getDoctrine()->getManager()->flush();
                        if($historiales != null) {
                            foreach ($historiales as $item2){
                                $this->getDoctrine()->getManager()->remove($item2);

                            }
                        }
                        $this->getDoctrine()->getManager()->flush();
                        $this->getDoctrine()->getManager()->remove($item2);
                    }
                }
                $this->getDoctrine()->getManager()->flush();

                $this->getDoctrine()->getManager()->remove($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Usuario Borrado Con Éxito');
                return $this->redirectToRoute('usuarios_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());

            }
        }
        return $this->render('user/eliminar.html.twig', [
            'usuario' => $user
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


}
