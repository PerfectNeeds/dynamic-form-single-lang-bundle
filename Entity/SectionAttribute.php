<?php

namespace PN\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use PN\Bundle\ServiceBundle\Entity\VirtualDeleteTrait;

/**
 * @ORM\Table(name="form_section_attribute")
 * @ORM\Entity(repositoryClass="PN\DynamicFormBundle\Repository\SectionAttributeRepository")
 */
class SectionAttribute {

    use VirtualDeleteTrait;

    CONST TYPE_TEXT = 1;
    CONST TYPE_LONGTEXT = 2;
    CONST TYPE_LINK = 3;
    CONST TYPE_IMAGE = 4;
    CONST TYPE_DOCUMENT = 5;
    CONST TYPE_EMAIL = 6;
    CONST TYPE_DATE = 7;
    CONST TYPE_PAST_DATE = 11;
    CONST TYPE_FUTURE_DATE = 12;
    CONST TYPE_NUMBER = 8;
    CONST TYPE_BOOLEAN = 9;
    CONST TYPE_ENUMS = 10;

    public static $types = [
        "Text (100 character)" => self::TYPE_TEXT,
        "Long text" => self::TYPE_LONGTEXT,
        "Link" => self::TYPE_LINK,
        "Upload Image" => self::TYPE_IMAGE,
        "Upload Document" => self::TYPE_DOCUMENT,
        "Email" => self::TYPE_EMAIL,
        "Date" => self::TYPE_DATE,
        "Past Date" => self::TYPE_PAST_DATE,
        "Future Date" => self::TYPE_FUTURE_DATE,
        "Number" => self::TYPE_NUMBER,
        "Yes or No" => self::TYPE_BOOLEAN,
        "Choices" => self::TYPE_ENUMS,
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="sectionAttributes", cascade={"persist", "remove"})
     */
    protected $section;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="field_name", type="string", length=50)
     */
    protected $fieldName;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="type", type="smallint")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="hint", type="string", nullable=true)
     */
    protected $hint;

    /**
     * @var string
     * @ORM\Column(name="publish", type="boolean")
     */
    protected $publish = true;

    /**
     * @var string
     * @ORM\Column(name="mandatory", type="boolean")
     */
    protected $mandatory = true;

    /**
     * @var string
     * @ORM\Column(name="sort", type="smallint", nullable=true)
     */
    protected $sort;

    /**
     * @ORM\OneToMany(targetEntity="SectionAttributeEnum", mappedBy="sectionAttribute", cascade={"remove"})
     */
    protected $sectionAttributeEnums;

    /**
     * @ORM\OneToMany(targetEntity="ApplicationHasSectionAttribute", mappedBy="sectionAttribute")
     */
    protected $applicationHasSectionAttributes;
    protected $value;

    public function __toString() {
        return (string) $this->getValue();
    }

    public function getTypeName() {
        return array_search($this->getType(), self::$types);
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->sectionAttributeEnums = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return SectionAttribute
     */
    public function setFieldName($fieldName) {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName() {
        return $this->fieldName;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return SectionAttribute
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
     * Set hint
     *
     * @param string $hint
     *
     * @return SectionAttribute
     */
    public function setHint($hint) {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get hint
     *
     * @return string
     */
    public function getHint() {
        return $this->hint;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     *
     * @return SectionAttribute
     */
    public function setPublish($publish) {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return boolean
     */
    public function getPublish() {
        return $this->publish;
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     *
     * @return SectionAttribute
     */
    public function setMandatory($mandatory) {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory() {
        return $this->mandatory;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return SectionAttribute
     */
    public function setSort($sort) {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * Set section
     *
     * @param \PN\DynamicFormBundle\Entity\Section $section
     *
     * @return SectionAttribute
     */
    public function setSection(\PN\DynamicFormBundle\Entity\Section $section = null) {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \PN\DynamicFormBundle\Entity\Section
     */
    public function getSection() {
        return $this->section;
    }

    /**
     * Add sectionAttributeEnum
     *
     * @param \PN\DynamicFormBundle\Entity\SectionAttributeEnum $sectionAttributeEnum
     *
     * @return SectionAttribute
     */
    public function addSectionAttributeEnum(\PN\DynamicFormBundle\Entity\SectionAttributeEnum $sectionAttributeEnum) {
        $this->sectionAttributeEnums[] = $sectionAttributeEnum;

        return $this;
    }

    /**
     * Remove sectionAttributeEnum
     *
     * @param \PN\DynamicFormBundle\Entity\SectionAttributeEnum $sectionAttributeEnum
     */
    public function removeSectionAttributeEnum(\PN\DynamicFormBundle\Entity\SectionAttributeEnum $sectionAttributeEnum) {
        $this->sectionAttributeEnums->removeElement($sectionAttributeEnum);
    }

    /**
     * Get sectionAttributeEnums
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectionAttributeEnums() {
        return $this->sectionAttributeEnums;
    }

    /**
     * Add application
     *
     * @param \PN\DynamicFormBundle\Entity\Application $application
     *
     * @return SectionAttribute
     */
    public function addApplication(\PN\DynamicFormBundle\Entity\Application $application) {
        $this->applications[] = $application;

        return $this;
    }

    /**
     * Remove application
     *
     * @param \PN\DynamicFormBundle\Entity\Application $application
     */
    public function removeApplication(\PN\DynamicFormBundle\Entity\Application $application) {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications() {
        return $this->applications;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Add applicationHasSectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\ApplicationHasSectionAttribute $applicationHasSectionAttribute
     *
     * @return SectionAttribute
     */
    public function addApplicationHasSectionAttribute(\PN\DynamicFormBundle\Entity\ApplicationHasSectionAttribute $applicationHasSectionAttribute) {
        $this->applicationHasSectionAttributes[] = $applicationHasSectionAttribute;

        return $this;
    }

    /**
     * Remove applicationHasSectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\ApplicationHasSectionAttribute $applicationHasSectionAttribute
     */
    public function removeApplicationHasSectionAttribute(\PN\DynamicFormBundle\Entity\ApplicationHasSectionAttribute $applicationHasSectionAttribute) {
        $this->applicationHasSectionAttributes->removeElement($applicationHasSectionAttribute);
    }

    /**
     * Get applicationHasSectionAttributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplicationHasSectionAttributes() {
        return $this->applicationHasSectionAttributes;
    }

}
