<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\CambioClaveType;
use AppBundle\Form\Type\UsuarioType;
use AppBundle\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioController extends Controller
{
    /**
     * @Route("/users", name="usuarios_listar")
     */
    public function usuarioListarAction(UsuarioRepository $usuarioRepository)
    {
        $todosUsuarios = $usuarioRepository->findAll();

        return $this->render('user/listar.html.twig', [
            'usuarios' => $todosUsuarios
        ]);
    }

    /**
     * @Route("/clave", name="cambio_clave")
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
     * @Route("/user/datos_personales", name="datos_personales")
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
     * @Route("/users/nuevo", name="usuario_nuevo")
     */
    public function formNuevoUsuario(Request $request)
    {
        $usuario = New User();

        $this->getDoctrine()->getManager()->persist($usuario);

        return $this->formUsuarioAction($request, $usuario);
    }

    /**
     * @Route("/users/{id}", name="usuario_modificar",
     *     requirements={"id":"\d+"})
     */
    public function formUsuarioAction(Request $request, User $user)
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
     * @Route("/user/eliminar/{id}", name="usuario_eliminar")
     */
    public function eliminarAction(Request $request, User $user)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Usuario Borrado Con Éxito');
                return $this->redirectToRoute('usuarios_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');

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
