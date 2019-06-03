<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use AppBundle\Form\Type\ArchivoType;
use AppBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends Controller
{
    /**
     * @Route("/files", name="archivos_listar")
     */
    public function FileListarAction(FileRepository $FileRepository)
    {
        $todosFiles = $FileRepository->findAllFiles();

        return $this->render('file/listar.html.twig', [
            'archivos' => $todosFiles
        ]);
    }
    /**
     * @Route("/files/nuevo", name="archivo_nuevo")
     */
    public function formNuevaCategoria(Request $request)
    {
        $archivo = New File();

        $this->getDoctrine()->getManager()->persist($archivo);

        return $this->formCategoriaAction($request, $archivo);
    }

    /**
     * @Route("/files/{id}", name="archivo_editar",
     *     requirements={"id":"\d+"})
     */
    public function formCategoriaAction(Request $request, File $archivo)
    {
        if(null === $archivo) {
            $archivo = new $archivo();
            $new = true;
        } else {
            $new = false;
        }
        if($new == false){
            $archivo->setDate(new \DateTime());
        }
        $form = $this->createForm(ArchivoType::class, $archivo, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('file')->getData();

                if ($file) {
                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            "uploads/file_files",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $archivo->setFile("uploads/file_files/" . $fileName);
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
     * @Route("/file/eliminar/{id}", name="archivo_eliminar")
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
