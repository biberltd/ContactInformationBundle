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
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="contact_information_type",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNContactInformationDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNContactInformationDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idNContactInformationDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUContactInformationTypeId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUContactInformationTypeCode", columns={"code"})
 *     }
 * )
 */
class ContactInformationType extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=5)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $id;

    /** 
     * @ORM\Column(type="string", unique=true, length=155, nullable=false)
     * @var string
     */
    private $code;

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
	 * @param string $code
	 *
	 * @return $this
	 */
    public function setCode(string $code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
		$this->code = $code;
		return $this;
    }

	/**
	 * @return mixed
	 */
    public function getCode() {
        return $this->code;
    }

	/**
	 * @return int
	 */
    public function getId() {
        return $this->id;
    }
}