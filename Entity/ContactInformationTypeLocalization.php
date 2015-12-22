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
 *     name="contact_information_type_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="idxULocalizedContactInformationType",
 *             columns={"contact_information_type","language"}
 *         ),
 *         @ORM\UniqueConstraint(
 *             name="idxULocalizaedContactInformationTypeUrylKey",
 *             columns={"contact_information_type","url_key","language"}
 *         )
 *     }
 * )
 */
class ContactInformationTypeLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $url_key;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType")
     * @ORM\JoinColumn(name="contact_information_type", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType
     */
    private $contact_information_type;

    /** 
     * 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language",
     *     inversedBy="contactInformationTypeLocalization"
     * )
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @param \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType $contact_information_type
     *
     * @return $this
     */
    public function setContactInformationType(\BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType $contact_information_type) {
        if(!$this->setModified('contact_information_type', $contact_information_type)->isModified()) {
            return $this;
        }
		$this->contact_information_type = $contact_information_type;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType
     */
    public function getContactInformationType() {
        return $this->contact_information_type;
    }

	/**
	 * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
	 *
	 * @return $this
	 */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
	 */
    public function getLanguage() {
        return $this->language;
    }

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
    public function setName(\string $name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getName() {
        return $this->name;
    }

	/**
	 * @param string $url_key
	 *
	 * @return $this
	 */
    public function setUrlKey(\string $url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
		$this->url_key = $url_key;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUrlKey() {
        return $this->url_key;
    }
}