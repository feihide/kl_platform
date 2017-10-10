<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgMemberGroupHasPrivilege
 *
 * @ORM\Table(name="org_member_group_has_privilege", indexes={@ORM\Index(name="IDX_E605149BF539C707", columns={"org_member_group_id"})})
 * @ORM\Entity
 */
class OrgMemberGroupHasPrivilege
{
    /**
     * @var string
     *
     * @ORM\Column(name="org_privilege_code", type="string", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $orgPrivilegeCode;

    /**
     * @var \OrgMemberGroup
     *
     * @ORM\OneToOne(targetEntity="OrgMemberGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_member_group_id", referencedColumnName="id", unique=true)
     * })
     */
    private $orgMemberGroup;


    /**
     * Set orgPrivilegeCode
     *
     * @param string $orgPrivilegeCode
     * @return OrgMemberGroupHasPrivilege
     */
    public function setOrgPrivilegeCode($orgPrivilegeCode)
    {
        $this->orgPrivilegeCode = $orgPrivilegeCode;

        return $this;
    }

    /**
     * Get orgPrivilegeCode
     *
     * @return string 
     */
    public function getOrgPrivilegeCode()
    {
        return $this->orgPrivilegeCode;
    }

    /**
     * Set orgMemberGroup
     *
     * @param \OrgMemberGroup $orgMemberGroup
     * @return OrgMemberGroupHasPrivilege
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
