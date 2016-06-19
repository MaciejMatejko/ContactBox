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
use ContactBoxBundle\Entity\Phone;
use ContactBoxBundle\Entity\Email;

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
            
            return $this->redirectToRoute("contactbox_contact_show", ["id" => $contact->getId()]);
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
        
        $form = $this->createFormBuilder($contact)->add("name")->add("surname")->add("description")->add("edit", "submit")->getForm();
        $form->handleRequest($request);
        
        $address = new Address();
        $addressForm = $this->createFormBuilder($address)->setAction($this->generateUrl('contactbox_contact_addaddress', ["id"=>$id]))->add("city")->add("street")->add("houseNumber")->add("apartmentNumber")->add("add", "submit")->getForm();
        
        $phone = new Phone();
        $phoneForm = $this->createFormBuilder($phone)->setAction($this->generateUrl('contactbox_contact_addphone', ["id"=>$id]))->add("number")->add("type")->add("add", "submit")->getForm();
        
        $email = new Email();
        $emailForm = $this->createFormBuilder($email)->setAction($this->generateUrl('contactbox_contact_addemail', ["id"=>$id]))->add("address")->add("type")->add("add", "submit")->getForm();
        
        if($form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirectToRoute("showAll");
        }
        return ["contact" => $contact, "form" => $form->createView(), "addressForm" =>$addressForm->createView(), "phoneForm" => $phoneForm->createView(), "emailForm" => $emailForm->createView()];
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
        
        return $this->redirectToRoute("showAll");
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
     * @Route("/", name="showAll")
     * @Template()
     */
    public function showAllAction(Request $request)
    {
        $contacts = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->findAllContactsOrderdBySurname();
        $data=[];
        $searchForm=$this->createFormBuilder($data)->add('searchBy', 'choice', array('choices'  => array(
        'Name' => "name",
        'Surname' => "surname",
            ), 'choices_as_values' => true,))->add("Query")->add("search", "submit")->getForm();
        
        $searchForm->handleRequest($request);
        
        if($searchForm->isValid()){
            $formData = $searchForm->getData();
            if($formData["searchBy"] === "name"){
                $contacts = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->findContactsByName($formData["Query"]);
                
                return ["contacts" => $contacts, "searchForm" => $searchForm ->createView()];
            }
            elseif($formData["searchBy"] === "surname"){
                $contacts = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->findContactsBySurname($formData["Query"]);
                
                return ["contacts" => $contacts, "searchForm" => $searchForm ->createView()];
            }
        }
        
        return ["contacts" => $contacts, "searchForm" => $searchForm ->createView()];
    }
    
    
    /**
     * @Route("/{id}/addAddress")
     * @Method({"POST"})
     * 
     */
    public function addAddressAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $address = new Address();
        $form = $this->createFormBuilder($address)->add("city")->add("street")->add("houseNumber")->add("apartmentNumber")->add("add", "submit")->getForm();
        
        $form->handleRequest($request);
        $address->setContact($contact);
        $contact->addAddress($address);
        
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_contact_show", ["id" => $id]);
        }
        
    }
    
    /**
     * @Route("/{id}/{addressId}/deleteAddress")
     */
    public function deleteAddressAction($id, $addressId)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $address = $this->getDoctrine()->getRepository("ContactBoxBundle:Address")->find($addressId);
        
        if(!$address){
            throw $this->createNotFoundException("Address not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $contact->removeAddress($address);
        $em->remove($address);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_contact_modify", ["id" => $id]);
        
    }
    
    /**
     * @Route("/{id}/addPhone")
     * @Method({"POST"})
     * 
     */
    public function addPhoneAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $phone = new Phone();
        $form = $this->createFormBuilder($phone)->add("number")->add("type")->add("add", "submit")->getForm();
        
        $form->handleRequest($request);
        $phone->setContact($contact);
        $contact->addPhone($phone);
        
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($phone);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_contact_show", ["id" => $id]);
        }
        
    }
    
    /**
     * @Route("/{id}/{phoneId}/deletePhone")
     */
    public function deletePhoneAction($id, $phoneId)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $phone = $this->getDoctrine()->getRepository("ContactBoxBundle:Phone")->find($phoneId);
        
        if(!$phone){
            throw $this->createNotFoundException("Address not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $contact->removePhone($phone);
        $em->remove($phone);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_contact_modify", ["id" => $id]);
        
    }
    
    /**
     * @Route("/{id}/addEmail")
     * @Method({"POST"})
     * 
     */
    public function addEmailAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $email = new Email();
        $form = $this->createFormBuilder($email)->add("address")->add("type")->add("add", "submit")->getForm();
        
        $form->handleRequest($request);
        $email->setContact($contact);
        $contact->addEmail($email);
        
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_contact_show", ["id" => $id]);
        }
        
    }
    
    /**
     * @Route("/{id}/{emailId}/deleteEmail")
     */
    public function deleteEmailAction($id, $emailId)
    {
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($id);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $email = $this->getDoctrine()->getRepository("ContactBoxBundle:Email")->find($emailId);
        
        if(!$email){
            throw $this->createNotFoundException("Email not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $contact->removeEmail($email);
        $em->remove($email);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_contact_modify", ["id" => $id]);
        
    }
    
}
