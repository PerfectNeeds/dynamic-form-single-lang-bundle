<?php

namespace PN\Bundle\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use PN\Bundle\ServiceBundle\Entity\VirtualDeleteTrait;

/**
 * @ORM\Table(name="form_section_attribute_enum")
 * @ORM\Entity()
 */
class SectionAttributeEnum {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="SectionAttribute", inversedBy="sectionAttributeEnums")
     */
    protected $sectionAttribute;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="option_text", type="string", length=150)
     */
    protected $optionText;


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
     * Set optionText
     *
     * @param string $optionText
     *
     * @return SectionAttributeEnum
     */
    public function setOptionText($optionText)
    {
        $this->optionText = $optionText;

        return $this;
    }

    /**
     * Get optionText
     *
     * @return string
     */
    public function getOptionText()
    {
        return $this->optionText;
    }

    /**
     * Set sectionAttribute
     *
     * @param \PN\Bundle\FormBundle\Entity\SectionAttribute $sectionAttribute
     *
     * @return SectionAttributeEnum
     */
    public function setSectionAttribute(\PN\Bundle\FormBundle\Entity\SectionAttribute $sectionAttribute = null)
    {
        $this->sectionAttribute = $sectionAttribute;

        return $this;
    }

    /**
     * Get sectionAttribute
     *
     * @return \PN\Bundle\FormBundle\Entity\SectionAttribute
     */
    public function getSectionAttribute()
    {
        return $this->sectionAttribute;
    }
}
