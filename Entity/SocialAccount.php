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
 *     name="social_account",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNSocialAccountDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNSocialAccountDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNSocialAccountDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUSocialAccountId", columns={"id"})}
 * )
 */
class SocialAccount extends CoreEntity
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
    private $username;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"f"})
     * @var string
     */
    private $network;

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
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
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
     * @param string $network
     *
     * @return $this
     */
    public function setNetwork(string $network) {
        if(!$this->setModified('network', $network)->isModified()) {
            return $this;
        }
		$this->network = $network;
		return $this;
    }

    /**
     * @return string
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username) {
        if(!$this->setModified('username', $username)->isModified()) {
            return $this;
        }
		$this->username = $username;
		return $this;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
}