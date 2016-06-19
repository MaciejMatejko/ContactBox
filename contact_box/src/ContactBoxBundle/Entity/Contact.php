<?php

namespace ContactBoxBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Contact
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContactBoxBundle\Entity\ContactRepository")
 * 
 */
class Contact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ContactBoxBundle\Entity\Address", mappedBy="contact", cascade={"remove"})
     */
    private $addresses;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ContactBoxBundle\Entity\Phone", mappedBy="contact", cascade={"remove"})
     */
    private $phones;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ContactBoxBundle\Entity\Email", mappedBy="contact", cascade={"remove"})
     */
    private $emails;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="Crew", mappedBy="contacts")
     * @ORM\JoinTable(name="contac_crew")
     */
    private $crews;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->crews = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Contact
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Contact
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    

     /**
     * @param \ContactBoxBundle\Entity\Address $address
     * @return Contact
     */
    public function addAddress(\ContactBoxBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

     /**
     * @param \ContactBoxBundle\Entity\Address $address
     */
    public function removeAddress(\ContactBoxBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

     /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add phones
     *
     * @param \ContactBoxBundle\Entity\Phone $phone
     * @return Contact
     */
    public function addPhone(\ContactBoxBundle\Entity\Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phones
     *
     * @param \ContactBoxBundle\Entity\Phone $phone
     */
    public function removePhone(\ContactBoxBundle\Entity\Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Add emails
     *
     * @param \ContactBoxBundle\Entity\Email $email
     * @return Contact
     */
    public function addEmail(\ContactBoxBundle\Entity\Email $email)
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \ContactBoxBundle\Entity\Email $email
     */
    public function removeEmail(\ContactBoxBundle\Entity\Email $email)
    {
        $this->emails->removeElement($email);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Add crews
     *
     * @param \ContactBoxBundle\Entity\Crew $crew
     * @return Contact
     */
    public function addCrew(\ContactBoxBundle\Entity\Crew $crew)
    {
        $this->crews[] = $crew;

        return $this;
    }

    /**
     * Remove crews
     *
     * @param \ContactBoxBundle\Entity\Crew $crew
     */
    public function removeCrew(\ContactBoxBundle\Entity\Crew $crew)
    {
        $this->crews->removeElement($crew);
    }

    /**
     * Get crews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCrews()
    {
        return $this->crews;
    }
}
