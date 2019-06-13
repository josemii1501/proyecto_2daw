<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\History;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Video;
use AppBundle\Form\Type\VideoType;
use AppBundle\Repository\VideoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends Controller
{
    /**
     * @Route("/videos/usuario/{id}", name="videos_usuario",
     *     requirements={"id":"\d+"})
     */
    public function videosUsuarioAction(VideoRepository $videoRepository, Usuario $usuario)
    {
        $todosvideos = $videoRepository->findByUser($usuario);

        return $this->render('video/listar.html.twig', [
            'videos' => $todosvideos
        ]);
    }
    /**
     * @Route("/videos/categoria/{id}", name="videos_categoria",
     *     requirements={"id":"\d+"})
     */
    public function videosCategoriaAction(VideoRepository $videoRepository, Category $category)
    {
        $todosvideos = $videoRepository->findByCategory($category);

        return $this->render('video/listar.html.twig', [
            'videos' => $todosvideos
        ]);
    }
    /**
     * @Route("/videos/{id}", name="visualizar_video",
     *     requirements={"id":"\d+"})
     */
    public function videoVisualizarAction(Video $video)
    {
        try{
            $historial = new History();

            $historial->setVideo($video);
                $historial->setUsuario($this->getUser());
                $historial->setTimestamp(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $video->setReproductions($video->getReproductions()+1);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('video/visualizar.html.twig', [
            'video' => $video
        ]);
    }
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
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function formNuevoVideo(Request $request)
    {
        $video = New Video();
        $video
            ->setDate(new \DateTime())
            ->setReproductions(0);
        $this->getDoctrine()->getManager()->persist($video);

        return $this->formVideoAction($request, $video);
    }
    /**
     * @Route("/videos/modificar/{id}", name="video_modificar",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_PUBLISHER')")
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

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('miniature')->getData();
                $ruta = $form->get('route')->getData();
                $divisiones = explode("v=",$ruta);
                $sinParametros = explode("&", $divisiones[1]);
                $rutaDefinitiva = "https://www.youtube.com/embed/".$sinParametros[0];
                $video->setRoute($rutaDefinitiva);
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
     * @Route("/videos/eliminar/{id}", name="video_eliminar")
     * @Security("is_granted('ROLE_PUBLISHER')")
     */
    public function eliminarAction(Request $request, Video $video)
    {
        if ($request->get('borrar') === '') {
            try {
                $files_videos = $video->getFile();
                $historiales = $video->getHistoriales();
                $guardados = $video->getGuardados();

                $this->getDoctrine()->getManager()->flush();
                if($files_videos != null) {
                    foreach ($files_videos as $item){
                        $this->getDoctrine()->getManager()->remove($item);

                    }
                }

                if($guardados != null) {
                    foreach ($guardados as $item){
                        $this->getDoctrine()->getManager()->remove($item);

                    }
                }
                $this->getDoctrine()->getManager()->flush();
                if($historiales != null) {
                    foreach ($historiales as $item){
                        $this->getDoctrine()->getManager()->remove($item);

                    }
                }
                $this->getDoctrine()->getManager()->flush();


                $this->getDoctrine()->getManager()->remove($video);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Video Borrado Con Éxito');
                return $this->redirectToRoute('videos_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());
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
