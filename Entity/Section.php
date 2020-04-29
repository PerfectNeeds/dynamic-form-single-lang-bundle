<?php

namespace PN\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use PN\Bundle\ServiceBundle\Entity\VirtualDeleteTrait;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="form_section")
 * @ORM\Entity(repositoryClass="PN\DynamicFormBundle\Repository\SectionRepository")
 */
class Section {

    use VirtualDeleteTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="title", type="string", length=50)
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="SectionAttribute", mappedBy="section", cascade={"all"})
     */
    protected $sectionAttributes;

    public function getHashCode() {
        $str = $this->getId() . $this->getCreated()->format("Y-m-d");
        return md5($str);
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() {
        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->sectionAttributes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Section
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Add sectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute
     *
     * @return Section
     */
    public function addSectionAttribute(\PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute) {
        $this->sectionAttributes[] = $sectionAttribute;

        return $this;
    }

    /**
     * Remove sectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute
     */
    public function removeSectionAttribute(\PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute) {
        $this->sectionAttributes->removeElement($sectionAttribute);
    }

    /**
     * Get sectionAttributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectionAttributes() {
        return $this->sectionAttributes;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Section
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

}
