<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
use AppBundle\Form\Type\VideoType;
use AppBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends Controller
{
    /**
     * @Route("/videos", name="videos_listar")
     */
    public function videoListarAction(VideoRepository $videoRepository)
    {
        $todosvideos = $videoRepository->findAllVideos();

        return $this->render('video/listar.html.twig', [
            'videos' => $todosvideos
        ]);
    }
    /**
     * @Route("/videos/nuevo", name="video_nuevo")
     */
    public function formNuevoVideo(Request $request)
    {
        $video = New Video();

        $this->getDoctrine()->getManager()->persist($video);

        return $this->formVideoAction($request, $video);
    }
    /**
     * @Route("/videos/{id}", name="video_editar",
     *     requirements={"id":"\d+"})
     */
    public function formVideoAction(Request $request, Video $video)
    {
        if(null === $video) {
            $video = new $video();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(VideoType::class, $video, [
            'new' => $new
        ]);

        $form->handleRequest($request);
        if($new == true){
            $video->setDate(new \DateTime());
            $video->setReproductions(0);
        } else {
            $video->setDate($video->getDate());
            $video->setReproductions($video->getReproductions());
        }
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('miniature')->getData();

                if ($file) {
                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            "uploads/video_photo",
                            $fileName
                        );
                    } catch (FileException $e) {

                    }

                    // updates the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $video->setMiniature("uploads/video_photo/" . $fileName);
                }


                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('videos_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());

            }
            return $this->render('video/form.html.twig', [
                'form' => $form->createView(),
                'video' => $video,
                'es_nueva' => $video->getId() === null
            ]);
        }


        return $this->render('video/form.html.twig', [
            'form' => $form->createView(),
            'video' => $video,
            'es_nueva' => $video->getId() === null
        ]);
    }
    /**
     * @Route("/category/eliminar/{id}", name="category_eliminar")
     */
    public function eliminarAction(Request $request, Video $video)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($video);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Video Borrado Con Éxito');
                return $this->redirectToRoute('videos_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('video/eliminar.html.twig', [
            'video' => $video
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
