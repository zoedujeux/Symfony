<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Advert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=10), message="le titre doit faire au moins {{ limit }} caractères.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;
    
    /**
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published=true;
    
    /**
    * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist","remove"})
    * @Assert\Valid()
    */
    private $image;
  
    /**
   * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
   */
    private $categories;
    
    /**
    * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")
    */
    private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures

     /**
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     *
     * @ORM\Column(name="nb_applications", type="integer")
     */
    private $nbApplications = 0;
    
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;
    
    public function __construct()
    {
      // Par défaut, la date de l'annonce est la date d'aujourd'hui
      $this->date = new \Datetime();
      $this->categories = new ArrayCollection();
      $this->applications = new ArrayCollection();
      
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }
    
     /**
    * @param Image $image
    * @return Advert
    */
    
     public function setImage(Image $image = null)
    {
      $this->image = $image;
      return $this;
    }
    
    /**
    *
    * @return Image
    */

    public function getImage()
    {
      return $this->image;
    }
    
    public function addCategory(Category $category)
    {
     
      $this->categories[] = $category;

      return $this;
    }

    public function removeCategory(Category $category)
    { 
      $this->categories->removeElement($category);
    }

    // Notez le pluriel, on récupère une liste de catégories ici !
    public function getCategories()
    {
      return $this->categories;
    }
    
    
    /**
   * @param Application $application
   * @return Advert
   */
    public function addApplication(Application $application)
    {
      $this->applications[] = $application;
      
      $application->setAdvert($this);

      return $this;
    }
    
  
    /**
    * @param Application $application
    *
    */
    public function removeApplication(Application $application)
    {
      $this->applications->removeElement($application);
    }
    
    /**
    * @return ArrayCollection
    */
    public function getApplications()
    {
      return $this->applications;
    }
    
    /**
    * @ORM\PreUpdate
    */
   public function updateDate()
   {
     $this->setUpdatedAt(new \Datetime());
   }

   public function setUpdatedAt(\Datetime $updatedAt)
   {
     $this->updatedAt = $updatedAt;
     return $this;
   }

   public function getUpdatedAt()
   {
     return $this->updatedAt;
   }

   public function increaseApplication()
   {
     $this->nbApplications++;
   }

   public function decreaseApplication()
   {
     $this->nbApplications--;
   }
   
}
