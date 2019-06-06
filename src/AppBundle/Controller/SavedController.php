<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Saved;
use AppBundle\Form\Type\SavedType;
use AppBundle\Repository\SavedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SavedController extends Controller
{
    /**
     * @Route("/guardados", name="guardados_listar")
     */
    public function SavedListarAction(SavedRepository $savedRepository)
    {
        $todosGuardadoss = $savedRepository->findAllSaved();

        return $this->render('saved/listar.html.twig', [
            'guardados' => $todosGuardadoss
        ]);
    }

    /**
     * @Route("/guardados/nuevo", name="guardado_nuevo")
     */
    public function formNuevoSaved(Request $request)
    {
        $saved = New Saved();
        $saved->setTimestamp(new \DateTime());

        $this->getDoctrine()->getManager()->persist($saved);

        return $this->formSavedAction($request, $saved);
    }

    /**
     * @Route("/guardados/modificar/{id}", name="guardado_modificar",
     *     requirements={"id":"\d+"})
     */
    public function formSavedAction(Request $request, Saved $saved)
    {
        if(null === $saved) {
            $saved = new $saved();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(SavedType::class, $saved, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('guardados_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());
            }
            return $this->render('saved/form.html.twig', [
                'form' => $form->createView(),
                'saved' => $saved,
                'es_nueva' => $saved->getId() === null
            ]);
        }


        return $this->render('saved/form.html.twig', [
            'form' => $form->createView(),
            'saved' => $saved,
            'es_nueva' => $saved->getId() === null
        ]);
    }
    /**
     * @Route("/guardados/eliminar/{id}", name="guardado_eliminar")
     */
    public function eliminarAction(Request $request, Saved $saved)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($saved);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Guardado Borrado Con Éxito');
                return $this->redirectToRoute('guardados_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('saved/eliminar.html.twig', [
            'saved' => $saved
        ]);
    }
}
