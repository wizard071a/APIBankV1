<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Customer;

class CustomerRestController extends FOSRestController
{

    /**
     * @Rest\Get("/customers")
     *
     * @param Request $request
     *
     * @return View|object
     */
    public function getAction(Request $request)
    {
        $responseCode = Response::HTTP_OK;
        $result = [];
        $offset = $request->get('offset');
        $limit = $request->get('limit');

        $customers = $this->getDoctrine()->getRepository('AppBundle:Customer')->findBy([], [], $limit, $offset);
        if (empty($customers)) {
            $result = 'Customers are no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }

        if ( Response::HTTP_OK == $responseCode ) {
            foreach ($customers as $customer) {
                $result[] = [
                    'customerId' => $customer->getId(),
                    'name' => $customer->getName(),
                    'cnp' => $customer->getCnp(),
                    'balance' => $customer->getBalance()
                ];
            }
        }
        return new View($result, $responseCode);
    }

    /**
     * @Rest\Post("/Client")
     *
     * @param Request $request
     * @return View
     */
    public function postAction(Request $request)
    {
        $data = new Customer();
        $name = $request->get('name');
        $cnp = $request->get('cnp');
        $balance = $request->get('balance');
        if(empty($name) || empty($cnp))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        if (empty($balance))
        {
            $balance = 0;
        }
        $data->setName($name);
        $data->setCnp($cnp);
        $data->setBalance($balance);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        $result = [
            'CustomerId' => $data->getId()
        ];
        return new View($result, Response::HTTP_OK);
    }
}