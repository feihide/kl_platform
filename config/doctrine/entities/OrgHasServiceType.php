<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgHasServiceType
 *
 * @ORM\Table(name="org_has_service_type")
 * @ORM\Entity
 */
class OrgHasServiceType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="service_type_id", type="integer", nullable=false)
     */
    private $serviceTypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="service_num", type="integer", nullable=false)
     */
    private $serviceNum;

    /**
     * @var \Org
     *
     * @ORM\OneToOne(targetEntity="Org")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passport_id", referencedColumnName="passport_id", unique=true)
     * })
     */
    private $passport;


    /**
     * Set serviceTypeId
     *
     * @param integer $serviceTypeId
     * @return OrgHasServiceType
     */
    public function setServiceTypeId($serviceTypeId)
    {
        $this->serviceTypeId = $serviceTypeId;

        return $this;
    }

    /**
     * Get serviceTypeId
     *
     * @return integer 
     */
    public function getServiceTypeId()
    {
        return $this->serviceTypeId;
    }

    /**
     * Set serviceNum
     *
     * @param integer $serviceNum
     * @return OrgHasServiceType
     */
    public function setServiceNum($serviceNum)
    {
        $this->serviceNum = $serviceNum;

        return $this;
    }

    /**
     * Get serviceNum
     *
     * @return integer 
     */
    public function getServiceNum()
    {
        return $this->serviceNum;
    }

    /**
     * Set passport
     *
     * @param \Org $passport
     * @return OrgHasServiceType
     */
    public function setPassport(\Org $passport = null)
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * Get passport
     *
     * @return \Org 
     */
    public function getPassport()
    {
        return $this->passport;
    }
}
