<?php

namespace PN\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\Bundle\ServiceBundle\Entity\VirtualDeleteTrait;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application")
 * @ORM\Entity(repositoryClass="PN\DynamicFormBundle\Repository\ApplicationRepository")
 */
class Application {

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
     * @ORM\Column(name="flag", type="smallint")
     */
    protected $flag = 1;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;


    /**
     * @ORM\OneToMany(targetEntity="ApplicationHasSectionAttribute", mappedBy="application", cascade={"all"})
     */
    protected $applicationHasSectionAttributes;

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
        $this->applicationHasSectionAttributes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set flag
     *
     * @param integer $flag
     *
     * @return Application
     */
    public function setFlag($flag) {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return integer
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Application
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

    /**
     * Add applicationHasSectionAttribute
     *
     * @param \PN\DynamicFormBundle\Entity\ApplicationHasSectionAttribute $applicationHasSectionAttribute
     *
     * @return Application
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

    public function getAttribute($sectionAttributeId) {
        foreach ($this->applicationHasSectionAttributes as $applicationHasSectionAttribute) {
            if ($applicationHasSectionAttribute->getSectionAttribute()->getId() == $sectionAttributeId) {
                return $applicationHasSectionAttribute->getValue();
            }
        }
        return null;
    }

}
