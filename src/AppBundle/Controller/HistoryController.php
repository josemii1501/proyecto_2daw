<?php

namespace AppBundle\Controller;

use AppBundle\Entity\History;
use AppBundle\Form\Type\HistoryType;
use AppBundle\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends Controller
{
    /**
     * @Route("/histories", name="historiales_listar")
     */
    public function HistoryListarAction(HistoryRepository $HistoryRepository)
    {
        $todosHistorys = $HistoryRepository->findAllHistories();

        return $this->render('history/listar.html.twig', [
            'historiales' => $todosHistorys
        ]);
    }
    /**
     * @Route("/histories/nuevo", name="historiales_nuevo")
     */
    public function formNuevoHistorial(Request $request)
    {
        $history = New History();

        $this->getDoctrine()->getManager()->persist($history);

        return $this->formHistorialAction($request, $history);
    }

    /**
     * @Route("/histories/{id}", name="historiales_editar",
     *     requirements={"id":"\d+"})
     */
    public function formHistorialAction(Request $request, History $history)
    {
        if(null === $history) {
            $history = new $history();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(HistoryType::class, $history, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if($new == false){
            $history->setTimestamp(new \DateTime());
        }
        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('historiales_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());
            }
            return $this->render('saved/form.html.twig', [
                'form' => $form->createView(),
                'historial' => $history,
                'es_nueva' => $history->getId() === null
            ]);
        }


        return $this->render('history/form.html.twig', [
            'form' => $form->createView(),
            'historial' => $history,
            'es_nueva' => $history->getId() === null
        ]);
    }
    /**
     * @Route("/histories/eliminar/{id}", name="historiales_eliminar")
     */
    public function eliminarAction(Request $request, History $history)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($history);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Historial Borrado Con Éxito');
                return $this->redirectToRoute('historiales_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('history/eliminar.html.twig', [
            'historial' => $history
        ]);
    }
}
