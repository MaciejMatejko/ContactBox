<?php
namespace ContactBoxBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ContactRepository extends EntityRepository
{
    public function findAllContactsOrderdBySurname(){
        $dql = 'SELECT c FROM ContactBoxBundle:Contact c ORDER BY c.surname ASC';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function findContactsByName($name){
        $dql = 'SELECT c FROM ContactBoxBundle:Contact c WHERE c.name LIKE :name ORDER BY c.surname ASC ';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('name', $name.'%');
        return $query->getResult();
    }
    
    public function findContactsBySurname($surname){
        $dql = 'SELECT c FROM ContactBoxBundle:Contact c WHERE c.surname LIKE :surname ORDER BY c.surname ASC ';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('surname', $surname.'%');
        return $query->getResult();
    }
}