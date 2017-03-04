<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class TokenRestController extends FOSRestController
{

    /**
     * @Rest\Get("/getApiKey")
     *
     * @param Request $request
     *
     * @return View|object
     */
    public function getAction(Request $request)
    {
        $responseCode = Response::HTTP_OK;
        $user_logged = $this->get('security.token_storage')->getToken()->getUser();

        if ( !($user_logged instanceof User) ) {
            $username = $request->get('username');
            $password = $request->get('password');
            $user_logged = $this->getValidUser($username, $password);
        }

        if ( $user_logged instanceof User ) {
            $apiKey = $this->generateApiKey($user_logged);

            $user_logged->setApiKey($apiKey);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user_logged);
            $em->flush();
            $result = [
                'api_key' => $apiKey
            ];
        } else {
            $result = 'User does not valid';
            $responseCode = Response::HTTP_FORBIDDEN;
        }
        return new View($result, $responseCode);
    }

    private function generateApiKey($user)
    {
        $username = $user->getUsername();
        $password = $user->getPassword();
        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $token = base64_encode(sha1($nonce . $username . $created . $password, true));

        return $token;
    }

    private function getValidUser($username, $password) {
        $user_manager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

        $user = $user_manager->findUserByUsername($username);
        if (!empty($user)) {
            $encoder = $factory->getEncoder($user);

            $bool = ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) ? true : false;

            if (empty($bool)) {
                $user = false;
            }
        }
        return $user;
    }
}