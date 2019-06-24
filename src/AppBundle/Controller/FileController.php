<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use AppBundle\Entity\Video;
use AppBundle\Form\Type\ArchivoType;
use AppBundle\Repository\FileRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends Controller
{
    /**
     * @Route("/archivos", name="archivos_listar")
     */
    public function FileListarAction(FileRepository $FileRepository)
    {
        $todosFiles = $FileRepository->findAllFiles();

        return $this->render('file/listar.html.twig', [
            'archivos' => $todosFiles
        ]);
    }
    /**
     * @Route("/archivos/nuevo", name="archivo_nuevo")
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function formNuevoFile(Request $request)
    {
        $archivo = New File();
        $archivo->setDate(new \DateTime());

        $this->getDoctrine()->getManager()->persist($archivo);

        return $this->formFileAction($request, $archivo);
    }
    /**
     * @Route("/archivos/nuevo/{id}", name="subir_archivo"),
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function formSubirFile(Request $request, Video $video)
    {
        if($video->getCreator() != $this->getUser()){
            $this->addFlash('error', 'Solo puedes subir archivos a tus videos');
            return $this->redirect('/');
        }
        $archivo = New File();
        $archivo->setDate(new \DateTime());
        $archivo->setVideo($video);
        $this->getDoctrine()->getManager()->persist($archivo);

        return $this->formFileAction($request, $archivo);
    }

    /**
     * @Route("/archivos/modificar/{id}", name="archivo_modificar",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function formFileAction(Request $request, File $archivo)
    {
        if(null === $archivo) {
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(ArchivoType::class, $archivo, [
            'new' => $new,
            'es_admin'=>$this->isGranted('ROLE_ADMIN')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('file')->getData();

                if ($file) {
                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    $archivo->setExtension($file->guessExtension());
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            "uploads/archivo",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $archivo->setFile($fileName);
                }


                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('archivos_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());
            }
            return $this->render('file/form.html.twig', [
                'form' => $form->createView(),
                'archivo' => $archivo,
                'es_nueva' => $archivo->getId() === null
            ]);
        }


        return $this->render('file/form.html.twig', [
            'form' => $form->createView(),
            'archivo' => $archivo,
            'es_nueva' => $archivo->getId() === null
        ]);
    }
    /**
     * @Route("/archivos/eliminar/{id}", name="archivo_eliminar")
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function eliminarAction(Request $request, File $archivo)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($archivo);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Archivo Borrado Con Éxito');
                return $this->redirectToRoute('archivos_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('file/eliminar.html.twig', [
            'archivo' => $archivo
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
