<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends Controller
{
    protected $class = 'class_here';
    protected $basePath = 'base_path_here';
    protected $type = 'type_here';

    public function getForm($data = null, array $options = [])
    {
        return parent::createForm($this->type, $data, $options);
    }

    public function render($view, array $parameters = [], Response $response = null)
    {
        return parent::render($view.'.html.twig', $parameters);
    }

    protected function persist($entity) 
    {
        $this->getDoctrine()->getManager()->persist($entity);
    }

    protected function flush() 
    {
        $this->getDoctrine()->getManager()->flush();
    }

    protected function getRepo($entity) 
    {
        return $this->getDoctrine()->getRepository($entity);
    }

     protected function find($handle, $id)
    {
        return $this->getRepo($handle)->find($id);
    }

    protected function findBy($handle, array $champs, array $order = [], $limit  = null, $offset = null)
    {
        return $this->getRepo($handle)->findBy($champs, $order, $limit, $offset);
    }

    protected function findAll($handle)
    {
        return $this->getRepo($handle)->findAll();
    }
 
    public function save($object, $flush = true)
    {
        $this->getDoctrine()->getManager()->persist($object);
        if ($flush) {
            $this->getDoctrine()->getManager()->flush();
        }
    }
 
    public function remove($object, $flush = true)
    {
        $this->getDoctrine()->getManager()->remove($object);
        if ($flush) {
            $this->getDoctrine()->getManager()->flush();
        }
    }

    protected function createEntity(object $entity)
    {
        return new $entity;
    }

    public function redirectTo($path, array $params = [])
    {
        return $this->redirect($this->generateUrl($path, $params));
    }

    public function jsonSuccess()
    {
        return new JsonResponse(['message' => 'success'], 200);
    }

    public function jsonFail()
    {
        return new JsonResponse(['message' => 'fail'], 200);
    }

    public function countCart()
    {
        $cartExtension = $this->container->get('app.twig.cart_extension');
        $countCart =  $cartExtension->countCart($this->getUser());
        return $countCart;
    }
}
