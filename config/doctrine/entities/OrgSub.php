<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgSub
 *
 * @ORM\Table(name="org_sub", indexes={@ORM\Index(name="fk_org_sub_org_group1_idx", columns={"org_group_id"})})
 * @ORM\Entity
 */
class OrgSub
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
     * @var \OrgGroup
     *
     * @ORM\ManyToOne(targetEntity="OrgGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_group_id", referencedColumnName="id")
     * })
     */
    private $orgGroup;


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
     * Set orgGroup
     *
     * @param \OrgGroup $orgGroup
     * @return OrgSub
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
