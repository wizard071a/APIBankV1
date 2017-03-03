<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller
{
    /**
     * @Route("/main", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Client:homepage.html.twig');
    }

    public function customersAction()
    {
        return $this->render('AppBundle:Client:customers.html.twig');
    }

    public function transactionsAction($customerId)
    {
        return $this->render('AppBundle:Client:transactions.html.twig');
    }

    public function transactionAddAction($customerId)
    {
        return $this->render('AppBundle:Client:transaction_add.html.twig');
    }

    public function transactionEditAction($customerId, $transactionId)
    {
        return $this->render('AppBundle:Client:transaction_edit.html.twig');
    }
}
