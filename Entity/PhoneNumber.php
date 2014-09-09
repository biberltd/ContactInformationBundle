<?php
namespace BiberLtd\Bundle\ContactInformationBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="phone_number",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_phone_number_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_phone_number", columns={"country_code","area_code","number","extension"})
 *     }
 * )
 */
class PhoneNumber extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    private $country_code;

    /** 
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    private $area_code;

    /** 
     * @ORM\Column(type="integer", length=7, nullable=false)
     */
    private $number;

    /** 
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $extension;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_removed;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\AddressManagementBundle\Entity\PhoneNumbersOfAddresses",
     *     mappedBy="phone"
     * )
     */
    private $phoneNumbersOfAddresses;
    /**
     * @name            setType ()
     *                  Sets the type property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $type
     *
     * @return          object                $this
     */
    public function setType($type)
    {
        if ($this->setModified('type', $type)->isModified()) {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * @name            getType ()
     *                  Returns the value of type property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @name                  setAreaCode ()
     *                                    Sets the area_code property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $area_code
     *
     * @return          object                $this
     */
    public function setAreaCode($area_code) {
        if(!$this->setModified('area_code', $area_code)->isModified()) {
            return $this;
        }
		$this->area_code = $area_code;
		return $this;
    }

    /**
     * @name            getAreaCode ()
     *                              Returns the value of area_code property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->area_code
     */
    public function getAreaCode() {
        return $this->area_code;
    }

    /**
     * @name                  setCountryCode ()
     *                                       Sets the country_code property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $country_code
     *
     * @return          object                $this
     */
    public function setCountryCode($country_code) {
        if(!$this->setModified('country_code', $country_code)->isModified()) {
            return $this;
        }
		$this->country_code = $country_code;
		return $this;
    }

    /**
     * @name            getCountryCode ()
     *                                 Returns the value of country_code property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->country_code
     */
    public function getCountryCode() {
        return $this->country_code;
    }

    /**
     * @name                  setExtension ()
     *                                     Sets the extension property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $extension
     *
     * @return          object                $this
     */
    public function setExtension($extension) {
        if(!$this->setModified('extension', $extension)->isModified()) {
            return $this;
        }
		$this->extension = $extension;
		return $this;
    }

    /**
     * @name            getExtension ()
     *                               Returns the value of extension property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->extension
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * @name            getId()
     *                      Returns the value of id property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @name                  setNumber ()
     *                                  Sets the number property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $number
     *
     * @return          object                $this
     */
    public function setNumber($number) {
        if(!$this->setModified('number', $number)->isModified()) {
            return $this;
        }
		$this->number = $number;
		return $this;
    }

    /**
     * @name            getNumber ()
     *                            Returns the value of number property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->number
     */
    public function getNumber() {
        return $this->number;
    }

}