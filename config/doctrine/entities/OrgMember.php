<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgMember
 *
 * @ORM\Table(name="org_member", indexes={@ORM\Index(name="fk_org_user_org_user_group1_idx", columns={"org_member_group_id"})})
 * @ORM\Entity
 */
class OrgMember
{
    /**
     * @var integer
     *
     * @ORM\Column(name="passport_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $passportId;

    /**
     * @var string
     *
     * @ORM\Column(name="department_name", type="string", length=45, nullable=false)
     */
    private $departmentName;

    /**
     * @var \OrgMemberGroup
     *
     * @ORM\ManyToOne(targetEntity="OrgMemberGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_member_group_id", referencedColumnName="id")
     * })
     */
    private $orgMemberGroup;


    /**
     * Get passportId
     *
     * @return integer 
     */
    public function getPassportId()
    {
        return $this->passportId;
    }

    /**
     * Set departmentName
     *
     * @param string $departmentName
     * @return OrgMember
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get departmentName
     *
     * @return string 
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set orgMemberGroup
     *
     * @param \OrgMemberGroup $orgMemberGroup
     * @return OrgMember
     */
    public function setOrgMemberGroup(\OrgMemberGroup $orgMemberGroup = null)
    {
        $this->orgMemberGroup = $orgMemberGroup;

        return $this;
    }

    /**
     * Get orgMemberGroup
     *
     * @return \OrgMemberGroup 
     */
    public function getOrgMemberGroup()
    {
        return $this->orgMemberGroup;
    }
}
