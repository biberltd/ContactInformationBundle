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
 *     name="email_address",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNEmailAddressDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNEmailAddressDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNEmailAddressDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUEmailAddressId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUEmailAddress", columns={"email"})
 *     }
 * )
 */
class EmailAddress extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $email;

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
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(\string $email) {
        if(!$this->setModified('email', $email)->isModified()) {
            return $this;
        }
		$this->email = $email;
		return $this;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
}