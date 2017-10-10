<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgGroupHasPrivilege
 *
 * @ORM\Table(name="org_group_has_privilege", indexes={@ORM\Index(name="fk_org_group_has_privilege_org_group1_idx", columns={"org_group_id"})})
 * @ORM\Entity
 */
class OrgGroupHasPrivilege
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
     * @var \OrgGroup
     *
     * @ORM\OneToOne(targetEntity="OrgGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_group_id", referencedColumnName="id", unique=true)
     * })
     */
    private $orgGroup;


    /**
     * Set orgPrivilegeCode
     *
     * @param string $orgPrivilegeCode
     * @return OrgGroupHasPrivilege
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
     * Set orgGroup
     *
     * @param \OrgGroup $orgGroup
     * @return OrgGroupHasPrivilege
     */
    public function setOrgGroup(\OrgGroup $orgGroup = null)
    {
        $this->orgGroup = $orgGroup;

        return $this;
    }

    /**
     * Get orgGroup
     *
     * @return \OrgGroup 
     */
    public function getOrgGroup()
    {
        return $this->orgGroup;
    }
}
