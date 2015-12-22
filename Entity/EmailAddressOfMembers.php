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
 *     name="email_address_of_members",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNEmailAddressOfMemberDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNEmailAddressOfMemberDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNEmailAddressOfMemberDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxNEmailAddressOfMember", columns={"email_address"})}
 * )
 */
class EmailAddressesOfMember extends CoreEntity
{
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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ContactInformationBundle\Entity\EmailAddress")
     * @ORM\JoinColumn(name="email_address", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var string
     */
    private $email_address;

    /** 
     * 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member", inversedBy="fMember")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType
     */
    private $type;

    /**
     * @param \BiberLtd\Bundle\ContactInformationBundle\Entity\EmailAddress $email_address
     *
     * @return $this
     */
    public function setEmailAddress(\BiberLtd\Bundle\ContactInformationBundle\Entity\EmailAddress $email_address) {
        if(!$this->setModified('email_address', $email_address)->isModified()) {
            return $this;
        }
		$this->email_address = $email_address;
		return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress() {
        return $this->email_address;
    }

    /**
     * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
     *
     * @return $this
     */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @param \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType $type
     *
     * @return $this
     */
    public function setType(\BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType $type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
		$this->type = $type;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType
     */
    public function getType() {
        return $this->type;
    }
}