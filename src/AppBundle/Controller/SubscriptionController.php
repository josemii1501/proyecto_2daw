<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Suscription;
use AppBundle\Form\Type\SuscriptionType;
use AppBundle\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends Controller
{
    /**
     * @Route("/subscriptions", name="subscription_listar")
     */
    public function subscriptionsListAction(SubscriptionRepository $subscriptionRepository)
    {
        $allSubscriptions = $subscriptionRepository->findAllSubscriptions();

        return $this->render('subscription/listar.html.twig', [
            'subscriptions' => $allSubscriptions
        ]);
    }
    /**
     * @Route("/subscriptions/nueva", name="subscriptions_nueva")
     */
    public function formNuevaSuscripcion(Request $request)
    {
        $suscription = New Suscription();
        $suscription->setTimestamp(new \DateTime());

        $this->getDoctrine()->getManager()->persist($suscription);

        return $this->formSuscripcionAction($request, $suscription);
    }

    /**
     * @Route("/subscriptions/{id}", name="subscriptions_editar",
     *     requirements={"id":"\d+"})
     */
    public function formSuscripcionAction(Request $request, Suscription $suscription)
    {
        if(null === $suscription) {
            $suscription = new $suscription();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(SuscriptionType::class, $suscription, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('subscription_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
                $this->addFlash('error', $e->getMessage());
            }
            return $this->render('subscription/form.html.twig', [
                'form' => $form->createView(),
                'subscriptions' => $suscription,
                'es_nueva' => $suscription->getId() === null
            ]);
        }


        return $this->render('subscription/form.html.twig', [
            'form' => $form->createView(),
            'subscriptions' => $suscription,
            'es_nueva' => $suscription->getId() === null
        ]);
    }
    /**
     * @Route("/subscriptions/eliminar/{id}", name="subscriptions_eliminar")
     */
    public function eliminarAction(Request $request, Suscription $suscription)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($suscription);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Suscripcion Borrada Con Éxito');
                return $this->redirectToRoute('subscription_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('subscription/eliminar.html.twig', [
            'subscriptions' => $suscription
        ]);
    }
}
