<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Transaction;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TransactionRestController extends FOSRestController
{
    private $_cache = false;

    private function initCache($customerId = false) {
        if (!empty($customerId)) {
            $this->_cache = new FilesystemAdapter('transactions' . $customerId, 3600);
        }
    }
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

        $this->initCache($customerId);
        if ( Response::HTTP_OK == $responseCode) {
            $params = [
                'customerId' => $customerId,
            ];
            if (!empty($amount)) {
                $params['amount'] = $amount;
            }
            if (!empty($date)) {
                $params['date'] = new DateTime($date);
            }
            $transactions = $this->getTransations($params, $amount, $limit, $offset);
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
        $logger = $this->get('monolog.logger.transactions');
        $data = new Transaction();
        $amount = $request->get('amount');
        if( empty($amount) && empty($customerId) )
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $this->initCache($customerId);
        $logger->info('Add transaction start', ['customerId' => $customerId, 'amount' => $amount]);

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
        $this->_cache->clear();
        $logger->info('trnsaction created', ['transaction' => $result]);
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
        $logger = $this->get('monolog.logger.transactions');
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
            $logger->info('Edit transaction start', ['params' => $params, 'amount' => $amount]);
            $transaction = $this->getDoctrine()->getRepository('AppBundle:Transaction')->findOneBy($params);
        }
        if (empty($transaction)) {
            $logger->info('Transaction is no exist');
            $result = 'Transaction is no exist';
            $responseCode = Response::HTTP_NOT_FOUND;
        }
        if ( Response::HTTP_OK == $responseCode) {
            $this->initCache($customerId);
            $logger->info('Edit transaction old amount', ['oldAmount' => $transaction->getAmount()]);
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
            $this->_cache->clear();
            $logger->info('Edit transaction updated', $result);
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
            $customerId = $transaction->getCustomerId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
            $this->initCache($customerId);
            $this->_cache->clear();
            $result = 'Success';
        }
        return new View($result, Response::HTTP_OK);
    }

    private function getTransations($params, $amount, $limit, $offset) {
        $date = '';
        if (!empty($params['date'])) {
            $date = $params['date']->format('Ymd') . '_';
        }
        $cacheName = 'tranasctions_' . $amount . '_' . $date . $limit . '_' . $offset;

        $cachedData = $this->_cache->getItem($cacheName);


        if (!$cachedData->isHit()) {
            $transactions = $this->getDoctrine()->getRepository('AppBundle:Transaction')->getTransactions($params, $limit, $offset);
            $this->_cache->save($cachedData->set($transactions));
        } else {
            $transactions = $cachedData->get();
        }

        return $transactions;
    }
}