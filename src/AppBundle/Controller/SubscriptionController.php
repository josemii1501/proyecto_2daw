<?php

namespace AppBundle\Controller;

use AppBundle\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends Controller
{
    /**
     * @Route("/subscriptions", name="subscription_list")
     */
    public function subscriptionsListAction(SubscriptionRepository $subscriptionRepository)
    {
        $allSubscriptions = $subscriptionRepository->findAll();

        return $this->render('subscription/listar.html.twig', [
            'subscriptions' => $allSubscriptions
        ]);
    }
}
