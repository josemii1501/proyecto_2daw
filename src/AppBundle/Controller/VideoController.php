<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\History;
use AppBundle\Entity\Saved;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Video;
use AppBundle\Form\Type\VideoType;
use AppBundle\Repository\HistoryRepository;
use AppBundle\Repository\SavedRepository;
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
     * @Route("guardar/eliminar/video/{id}", name="eliminar_guardado_video",
     *     requirements={"id":"\d+"})
     */
    public function videoEliminarGuardarAction(Video $video,SavedRepository $savedRepository, HistoryRepository $historyRepository)
    {
        try {
            if ($this->getUser()) {
                $guardado = $savedRepository->findVideoUsuario($this->getUser(),$video);
                foreach($guardado as $item){
                    $this->getDoctrine()->getManager()->remove($item);
                    $this->getDoctrine()->getManager()->flush();
                }
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->videoVisualizarAction($video,$savedRepository, $historyRepository);
    }
    /**
     * @Route("video/{id}/estadisticas", name="estadisticas_video",
     *     requirements={"id":"\d+"})
     */
    public function videoEstadisticasAction(Video $video,SavedRepository $savedRepository)
    {
        $guardados = $savedRepository->findTotalSaves($video);
        $total_guardados = $guardados[0];

        return $this->render('video/estadisticas.html.twig', [
            'video' => $video,
            'video_guardados' => $total_guardados[1]
        ]);
    }
    /**
     * @Route("guardar/video/{id}", name="guardar_video",
     *     requirements={"id":"\d+"})
     */
    public function videoGuardarAction(Video $video,SavedRepository $savedRepository, HistoryRepository $historyRepository)
    {
        try {
            if ($this->getUser()) {
                $save = new Saved();

                $save->setVideo($video)
                    ->setUsuario($this->getUser())
                    ->setTimestamp(new \DateTime());

                $this->getDoctrine()->getManager()->persist($save);
                $this->getDoctrine()->getManager()->flush();
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->videoVisualizarAction($video,$savedRepository, $historyRepository);
    }
    /**
     * @Route("/videos/{id}", name="visualizar_video",
     *     requirements={"id":"\d+"})
     */
    public function videoVisualizarAction(Video $video,SavedRepository $savedRepository, HistoryRepository $historyRepository)
    {
        try{
            $guardado = false;

            if($this->getUser()){

                $historialEsta = $historyRepository->findHistorial($video, $this->getUser());
                if(empty($historialEsta)){
                    $historial = new History();

                    $historial->setVideo($video)
                        ->setUsuario($this->getUser())
                        ->setTimestamp(new \DateTime());

                    $this->getDoctrine()->getManager()->persist($historial);
                    $this->getDoctrine()->getManager()->flush();
                }else {
                    foreach($historialEsta as $item){
                        $item->setTimestamp(new \DateTime());
                        $this->getDoctrine()->getManager()->flush();
                    }
                }


                $estaGuardado = $savedRepository->findVideoUsuario($this->getUser(),$video);

                if(empty($estaGuardado)){
                    $guardado = false;
                } else {
                    $guardado = true;
                }
            }
            $video->setReproductions($video->getReproductions()+1);
            $this->getDoctrine()->getManager()->flush();

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }



        return $this->render('video/visualizar.html.twig', [
            'video' => $video,
            'guardado' => $guardado
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
            'new' => $new,
            'es_admin' => $this->isGranted('ROLE_ADMIN')
            ]);

        if(!$this->isGranted('ROLE_ADMIN')){
            $video->setCreator($this->getUser());
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('miniature')->getData();
                    $ruta = $form->get('route')->getData();
                    if( strpos($ruta, "v=") ){
                        $divisiones = explode("v=",$ruta);
                        $sinParametros = explode("&", $divisiones[1]);
                        $rutaDefinitiva = "https://www.youtube.com/embed/".$sinParametros[0];
                        $video->setRoute($rutaDefinitiva);
                    }

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
                } else {
                    if($new == true) {
                        $video->setMiniature("archivos_web/miniatura_predeterminada.png");
                    }
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
