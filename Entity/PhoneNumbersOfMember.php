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
use BiberLtd\Bundle\MemberManagementBundle\Entity\Member;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="phone_numbers_of_member",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNPhoneNumberOfMemberDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNPhoneNumberOfMemberDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNPhoneNumberOfMemberDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ixUPhoneNumberOfMember", columns={"phone_number","member"})}
 * )
 */
class PhoneNumbersOfMember extends CoreEntity
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
     * 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber")
     * @ORM\JoinColumn(name="phone_number", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber
     */
    private $phone_number;

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
	 * @param \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber $phone_number
	 *
	 * @return $this
	 */
    public function setPhoneNumber(\BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber $phone_number) {
        if(!$this->setModified('phone_number', $phone_number)->isModified()) {
            return $this;
        }
		$this->phone_number = $phone_number;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber
	 */
    public function getPhoneNumber() {
        return $this->phone_number;
    }
}