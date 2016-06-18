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
        $dql = 'SELECT c FROM ContactBookBundle:Contact c WHERE c.name LIKE :name';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('name', $name.'%');
        return $query->getResult();
    }
}