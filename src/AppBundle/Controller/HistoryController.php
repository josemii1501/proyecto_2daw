<?php

namespace AppBundle\Controller;

use AppBundle\Entity\History;
use AppBundle\Form\Type\HistoryType;
use AppBundle\Repository\HistoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends Controller
{
    /**
     * @Route("/historiales", name="historiales_listar")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function HistoryListarAction(HistoryRepository $HistoryRepository)
    {
        $todosHistorys = $HistoryRepository->findAllHistories();

        return $this->render('history/listar.html.twig', [
            'historiales' => $todosHistorys
        ]);
    }
    /**
     * @Route("/historiales/nuevo", name="historial_nuevo")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function formNuevoHistorial(Request $request)
    {
        $history = New History();
        $history->setTimestamp(new \DateTime());

        $this->getDoctrine()->getManager()->persist($history);

        return $this->formHistorialAction($request, $history);
    }

    /**
     * @Route("/historiales/modificar/{id}", name="historial_modificar",
     *     requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
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
     * @Route("/historiales/eliminar/{id}", name="historial_eliminar")
     * @Security("is_granted('ROLE_ADMIN')")
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
