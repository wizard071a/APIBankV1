<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Transaction;

class TransactionRestController extends FOSRestController
{
    /**
     * @Rest\Get("/transaction/{customerId}/{transactionId}")
     *
     * @param integer $customerId
     * @param integer $transactionId
     *
     * @return View|object
     */
    public function getAction($customerId, $transactionId)
    {
        $transaction = null;
        $responseCode = Response::HTTP_OK;
        $result = '';

        if (empty($customerId) || empty($transactionId)) {
            $result = 'Incorrect request';
            $responseCode = Response::HTTP_NOT_ACCEPTABLE;
        }

        if ( Response::HTTP_OK == $responseCode) {
            $params = [
                'customerId' => $customerId,
                'id' => $transactionId
            ];
            $transaction = $this->getDoctrine()->getRepository('AppBundle:Transaction')->findOneBy($params);
        }
        if ($transaction === null) {
            $result = 'Transaction is no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }

        if ( Response::HTTP_OK == $responseCode ) {
            $result = [
                'transactionId' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getFormattedDate()
            ];
        }
        return new View($result, $responseCode);
    }

    /**
     * @Rest\Get("/transactions/{customerId}")
     *
     * @param integer $customerId
     * @param Request $request
     *
     * @return View|object
     */
    public function getTransactionsAction($customerId, Request $request)
    {
        $transactions = [];
        $responseCode = Response::HTTP_OK;
        $result = [];
        $amount = $request->get('amount');
        $date = $request->get('date');
        $offset = $request->get('offset');
        $limit = $request->get('limit');

        if (empty($customerId)) {
            $result = 'Incorrect request';
            $responseCode = Response::HTTP_NOT_ACCEPTABLE;
        }

        if ( Response::HTTP_OK == $responseCode) {
            $params = [
                'customerId' => $customerId,
            ];
            if (!empty($amount)) {
                $params['amount'] = $amount;
            }
            if (!empty($date)) {
                $params['date'] = $date;
            }

            $transactions = $this->getDoctrine()->getRepository('AppBundle:Transaction')->findBy($params, array(), $limit, $offset);
        }
        if (empty($transactions)) {
            $result = 'Transactions are no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }

        if ( Response::HTTP_OK == $responseCode ) {
            foreach ($transactions as $transaction) {
                $result[] = [
                    'transactionId' => $transaction->getId(),
                    'customerId' => $transaction->getCustomerId(),
                    'amount' => $transaction->getAmount(),
                    'date' => $transaction->getFormattedDate(),
                    'status' => $transaction->getStatus()
                ];
            }
        }
        return new View($result, $responseCode);
    }


    /**
     * @Rest\Post("/transaction/{customerId}")
     *
     * @param integer $customerId
     * @param Request $request
     *
     * @return View
     */
    public function postAction($customerId, Request $request)
    {
        $data = new Transaction();
        $amount = $request->get('amount');
        if( empty($amount) && empty($customerId) )
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setCustomerId($customerId);
        $data->setAmount($amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        $result = [
            'TransactionId' => $data->getId(),
            'CustomerId' => $customerId,
            'amount' => $amount,
            'date' => $data->getDate()
        ];
        return new View($result, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/transaction/{customerId}/{transactionId}")
     *
     * @param integer $customerId
     * @param integer $transactionId
     * @param Request $request
     *
     * @return View
     */
    public function putAction($customerId, $transactionId, Request $request)
    {
        $transaction = null;
        $responseCode = Response::HTTP_OK;
        $result = '';

        $amount = $request->get('amount');
        if( empty($amount) && empty($customerId) && empty($transactionId))
        {
            $result = 'Incorrect request';
            $responseCode = Response::HTTP_NOT_ACCEPTABLE;
        }

        if ( Response::HTTP_OK == $responseCode) {
            $params = [
                'customerId' => $customerId,
                'id' => $transactionId
            ];
            $transaction = $this->getDoctrine()->getRepository('AppBundle:Transaction')->findOneBy($params);
        }
        if (empty($transaction)) {
            $result = 'Transaction is no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }
        if ( Response::HTTP_OK == $responseCode) {
            $transaction->setCustomerId($customerId);
            $transaction->setAmount($amount);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();
            $result = [
                'TransactionId' => $transaction->getId(),
                'CustomerId' => $customerId,
                'amount' => $amount,
                'date' => $transaction->getDate()
            ];
        }
        return new View($result, $responseCode);
    }

    /**
     * @Rest\Delete("/transaction/{transactionId}")
     *
     * @param integer $transactionId
     *
     * @return View
     */
    public function deleteAction($transactionId)
    {
        $transaction = null;
        $responseCode = Response::HTTP_OK;
        $result = '';

        if( empty($transactionId) )
        {
            $result = 'Incorrect request';
            $responseCode = Response::HTTP_NOT_ACCEPTABLE;
        }

        if ( Response::HTTP_OK == $responseCode) {
            $params = [
                'id' => $transactionId
            ];
            $transaction = $this->getDoctrine()->getRepository('AppBundle:Transaction')->findOneBy($params);
        }

        if (empty($transaction)) {
            $result = 'Transaction is no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }

        if ( Response::HTTP_OK == $responseCode ) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
            $result = 'Success';
        }
        return new View($result, Response::HTTP_OK);
    }
}