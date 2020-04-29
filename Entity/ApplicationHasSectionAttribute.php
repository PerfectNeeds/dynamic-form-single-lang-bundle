<?php

namespace PN\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="application_has_section_attribute")
 * @ORM\Entity()
 */
class ApplicationHasSectionAttribute {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="applicationHasSectionAttributes", cascade={"persist", "remove"})
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="SectionAttribute", inversedBy="applicationHasSectionAttributes")
     */
    protected $sectionAttribute;

    /**
     * @var string
     * @ORM\Column(name="attribute", type="text")
     */
    protected $attribute;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="type", type="smallint")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    protected $value;

    /**
     * @ORM\ManyToMany(targetEntity="\PN\Bundle\MediaBundle\Entity\Image", cascade={"persist", "remove" })
     */
    protected $image;

    /**
     * @ORM\ManyToMany(targetEntity="\PN\Bundle\MediaBundle\Entity\Document", cascade={"persist", "remove" })
     */
    protected $document;

    /**
     * Constructor
     */
    public function __construct() {
        $this->image = new \Doctrine\Common\Collections\ArrayCollection();
        $this->document = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set attribute
     *
     * @param string $attribute
     *
     * @return ApplicationHasSectionAttribute
     */
    public function setAttribute($attribute) {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return ApplicationHasSectionAttribute
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ApplicationHasSectionAttribute
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set application
     *
     * @param \PN\DynamicFormBundle\Entity\Application $application
     *
     * @return ApplicationHasSectionAttribute
     */
    public function setApplication(\PN\DynamicFormBundle\Entity\Application $application = null) {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \PN\DynamicFormBundle\Entity\Application
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * Set sectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute
     *
     * @return ApplicationHasSectionAttribute
     */
    public function setSectionAttribute(\PN\DynamicFormBundle\Entity\SectionAttribute $sectionAttribute = null) {
        $this->sectionAttribute = $sectionAttribute;

        return $this;
    }

    /**
     * Get sectionAttribute
     *
     * @return \PN\DynamicFormBundle\Entity\SectionAttribute
     */
    public function getSectionAttribute() {
        return $this->sectionAttribute;
    }

    /**
     * Add image
     *
     * @param \PN\Bundle\MediaBundle\Entity\Image $image
     *
     * @return ApplicationHasSectionAttribute
     */
    public function addImage(\PN\Bundle\MediaBundle\Entity\Image $image) {
        $this->image[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \PN\Bundle\MediaBundle\Entity\Image $image
     */
    public function removeImage(\PN\Bundle\MediaBundle\Entity\Image $image) {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImage() {
        return $this->image->first();
    }

    /**
     * Add document
     *
     * @param \PN\Bundle\MediaBundle\Entity\Document $document
     *
     * @return ApplicationHasSectionAttribute
     */
    public function addDocument(\PN\Bundle\MediaBundle\Entity\Document $document) {
        $this->document[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \PN\Bundle\MediaBundle\Entity\Document $document
     */
    public function removeDocument(\PN\Bundle\MediaBundle\Entity\Document $document) {
        $this->document->removeElement($document);
    }

    /**
     * Get document
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocument() {
        return $this->document->first();
    }

}
