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
use ContactBoxBundle\Entity\Crew;

/**
* @Route("/group")
*/
class CrewController extends Controller
{
    /**
     * @Route("/new")
     * @Template("ContactBoxBundle:Crew:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $crew = new Crew();
        
        $form = $this->createFormBuilder($crew)->add("name")->add("description")->add("submit", "submit")->getForm();
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($crew);
            $em->flush();
            
            return $this->redirectToRoute("contactbox_crew_show", ["id" => $crew->getId()]);
        }
        
        return ["form" => $form->createView()];
    }
    
    /**
     * 
     * @Route("/{id}/show")
     * @Template("ContactBoxBundle:Crew:show.html.twig")
     */
    public function showAction($id)
    {
        $crew = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->find($id);
        
        if(!$crew){
            throw $this->createNotFoundException("Group not found");
        }
        
        return ["crew" => $crew];
        
    }
    
    /**
     * @Route("/")
     * @Template("ContactBoxBundle:Crew:showAll.html.twig")
     */
    public function showAllAction()
    {
        $crews = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->findAll();
        
        return ["crews" => $crews];
    }
    
    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction($id)
    {
        $crew = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->find($id);
        
        if(!$crew){
            throw $this->createNotFoundException("Group not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($crew);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_crew_showall");
    }
    
    /**
     * @Route("/{id}/modify")
     * @Template("ContactBoxBundle:Crew:modify.html.twig")
     */
    public function modifyAction(Request $request, $id)
    {
        $crew = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->find($id);
        
        if(!$crew){
            throw $this->createNotFoundException("Group not found");
        }
        
        $form = $this->createFormBuilder($crew)->add("name")->add("description")->add("edit", "submit")->getForm();
        $form->handleRequest($request);
                
        $data = [];
        $contactForm = $this->createFormBuilder($data)->setAction($this->generateUrl('contactbox_crew_addcontact', ["id"=>$id]))->add('Contact', 'entity', array('class' => 'ContactBoxBundle:Contact', 'choice_label' => 'surname'))->add("add", "submit")->getForm();
        
        if($form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirectToRoute("contactbox_crew_showall");
        }
        return ["crew" => $crew, "form" => $form->createView(), "contactForm" => $contactForm->createView()];
    }
    
    
    /**
     * @Route("/{id}/addContact")
     */
    public function addContactAction(Request $request, $id)
    {
        $data = [];
        $contactForm = $this->createFormBuilder($data)->add('Contact', 'entity', array('class' => 'ContactBoxBundle:Contact', 'choice_label' => 'surname'))->add("add", "submit")->getForm();
        $contactForm->handleRequest($request);  
        $formData=$contactForm->getData();
        
        if($contactForm->isValid()){
            $contact = $formData["Contact"];
            $crew = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->find($id);
            
            $crew->addContact($contact);
            $contact->addCrew($crew);
            
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirectToRoute("contactbox_crew_show", ["id" => $id]);
        }
        
    }
    
    /**
     * @Route("/{id}/{contactId}/removeContact")
     */
    public function removeContactAction($id, $contactId)
    {
        $crew = $this->getDoctrine()->getRepository("ContactBoxBundle:Crew")->find($id);
        
        if(!$crew){
            throw $this->createNotFoundException("Group not found");
        }
        
        $contact = $this->getDoctrine()->getRepository("ContactBoxBundle:Contact")->find($contactId);
        
        if(!$contact){
            throw $this->createNotFoundException("Contact not found");
        }
        
        $em = $this->getDoctrine()->getManager();
        $contact->removeCrew($crew);
        $crew->removeContact($contact);
        $em->flush();
        
        return $this->redirectToRoute("contactbox_crew_modify", ["id" => $id]);
        
    }
    
}
