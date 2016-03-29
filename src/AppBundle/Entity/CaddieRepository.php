<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CaddieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CaddieRepository extends EntityRepository
{
	public function getUserOrSession($user) 
	{
		$identifiant = ($user) ? 'user' : 'identifiant';
		$identifiantvalue = ($user) ? $user : session_id();

		return ["id" => $identifiant, "value" => $identifiantvalue];
	}

	public function getAllProducts($user) 
	{
		$results = $this->getEntityManager()
		->createQuery('SELECT c FROM AppBundle:Caddie c WHERE (c.user = :name1 OR c.identifiant = :name2)')
		->setParameter('name1', $user)
		->setParameter('name2', session_id())
		->getResult();

		return $results;
	}

	public function switchSessionToUserProduct($user)
	{
		$listecaddies = $this->getEntityManager()
		->createQuery('SELECT c From AppBundle:Caddie c WHERE c.identifiant = :identifiant')
		->setParameter('identifiant', session_id())
		->getResult();

		foreach ($listecaddies as $listecaddie) {

            if (!$listecaddie->getUser() && $user) {
                $listecaddie->setUser($user);
                $this->getEntityManager()->persist($listecaddie);
            }
        }
        
        $this->getEntityManager()->flush();
	}

	public function getTotalPrix($user) 
	{
		$results = $this->getAllProducts($user);
		// Additionne les prix de chaque produits avec leur quantité
		$prixtotal = 0;	
		foreach($results as $result)
		{
			$prixtotal += ($result->getPrix())*($result->getQuantite());
		}

		return $prixtotal;
	}

	public function getProductCaddie($id, $productType, $user) 
	{
		// Récupere tous les objets produits du type demandé (menu,upsell etc)
		$results = $this->getEntityManager()
		->createQuery('SELECT c FROM AppBundle:Caddie c WHERE c.' . $this->getUserOrSession($user)["id"] . ' = :identifiant AND c.' . $productType . ' = :' . $productType .'')
		->setParameter('identifiant', $this->getUserOrSession($user)["value"])
		->setParameter($productType, $id)
		->getResult();

		return $results;
	}
}
