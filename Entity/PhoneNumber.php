<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        22.12.2015
 */
namespace BiberLtd\Bundle\ContactInformationBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="phone_number",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUPhoneNumberId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUPhoneNumber", columns={"country_code","area_code","number"})
 *     }
 * )
 */
class PhoneNumber extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=5, nullable=false)
     * @var string
     */
    private $country_code;

    /** 
     * @ORM\Column(type="string", length=4, nullable=false)
     * @var string
     */
    private $area_code;

    /** 
     * @ORM\Column(type="string", length=7, nullable=false)
     * @var string
     */
    private $number;

    /** 
     * @ORM\Column(type="string", length=4, nullable=true)
     * @var string
     */
    private $extension;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_removed;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"h"})
     * @var string
     */
    private $type;

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(\string $type)
    {
        if ($this->setModified('type', $type)->isModified()) {
            $this->type = $type;
        }
        return $this;
    }

	/**
	 * @return string
	 */
    public function getType()
    {
        return $this->type;
    }

	/**
	 * @param string $area_code
	 *
	 * @return $this
	 */
    public function setAreaCode(\string $area_code) {
        if(!$this->setModified('area_code', $area_code)->isModified()) {
            return $this;
        }
		$this->area_code = $area_code;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getAreaCode() {
        return $this->area_code;
    }

	/**
	 * @param string $country_code
	 *
	 * @return $this
	 */
    public function setCountryCode(\string $country_code) {
        if(!$this->setModified('country_code', $country_code)->isModified()) {
            return $this;
        }
		$this->country_code = $country_code;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getCountryCode() {
        return $this->country_code;
    }

	/**
	 * @param string $extension
	 *
	 * @return $this
	 */
    public function setExtension(\string $extension) {
        if(!$this->setModified('extension', $extension)->isModified()) {
            return $this;
        }
		$this->extension = $extension;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getExtension() {
        return $this->extension;
    }

	/**
	 * @return int
	 */
    public function getId() {
        return $this->id;
    }

	/**
	 * @param string $number
	 *
	 * @return $this
	 */
    public function setNumber(\string $number) {
        if(!$this->setModified('number', $number)->isModified()) {
            return $this;
        }
		$this->number = $number;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getNumber() {
        return $this->number;
    }
}