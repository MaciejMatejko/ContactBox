<?php

namespace ContactBoxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormView;
use ContactBoxBundle\Entity\Contact;
use ContactBoxBundle\Entity\Address;

class ContactController extends Controller
{
    
    /**
     * @Route("/new")
     * @Template("ContactBoxBundle:Contact:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        
        $form = $this->createFormBuilder($contact)->add("name")->add("surname")->add("description")->add("submit", "submit")->getForm();
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_contact_showall");
        }
        
        return ["form" => $form->createView()];
    }
    
    /**
     * @Route("/{id}/modify")
     * @Template("ContactBoxBundle:Contact:modify.html.twig")
     */
    public function modifyAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $form = $this->createFormBuilder($contact)->add("name")->add("surname")->add("description")->add("submit", "submit")->getForm();
        $form->handleRequest($request);
        
        $address = new Address();
        $addressForm = $this->createFormBuilder($address)->setAction($this->generateUrl('contactbox_contact_addaddress', ["id"=>$id]))->add("city")->add("street")->add("houseNumber")->add("apartmentNumber")->add("submit", "submit")->getForm();
        
        
        if($form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirectToRoute("contactbox_contact_showall");
        }
        
        return ["form" => $form->createView(), "addressForm" =>$addressForm->createView()];
    }
    
    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction($id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_contact_showall");
    }
    
    /**
     * @Route("/{id}")
     * @Template("ContactBoxBundle:Contact:show.html.twig")
     */
    public function showAction($id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        return ["contact" => $contact];
        
    }
    
    /**
     * @Route("/")
     * @Template()
     */
    public function showAllAction()
    {
        $contacts = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->findAllContactsOrderdBySurname();
        
        return ["contacts" => $contacts];
    }
    
    /**
     * @Route("/{id}/addAddress")
     * @Method({"POST"})
     * 
     */
    public function addAddressAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        $address = new Address();
        $form = $this->createFormBuilder($address)->add("city")->add("street")->add("houseNumber")->add("apartmentNumber")->add("submit", "submit")->getForm();
        
        $form->handleRequest($request);
        $address->setContact($contact);
        $contact->addAddress($address);
        
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_contact_showall");
        }
        
    }
    
}
