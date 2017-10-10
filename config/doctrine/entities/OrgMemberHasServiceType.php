<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgMemberHasServiceType
 *
 * @ORM\Table(name="org_member_has_service_type")
 * @ORM\Entity
 */
class OrgMemberHasServiceType
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
     * @var \OrgMember
     *
     * @ORM\OneToOne(targetEntity="OrgMember")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passport_id", referencedColumnName="passport_id", unique=true)
     * })
     */
    private $passport;


    /**
     * Set serviceTypeId
     *
     * @param integer $serviceTypeId
     * @return OrgMemberHasServiceType
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
     * @return OrgMemberHasServiceType
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
     * @param \OrgMember $passport
     * @return OrgMemberHasServiceType
     */
    public function setPassport(\OrgMember $passport = null)
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * Get passport
     *
     * @return \OrgMember 
     */
    public function getPassport()
    {
        return $this->passport;
    }
}
