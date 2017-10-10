<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgServiceAttr
 *
 * @ORM\Table(name="org_service_attr", indexes={@ORM\Index(name="IDX_83C85CEF6800E622", columns={"org_service_id"})})
 * @ORM\Entity
 */
class OrgServiceAttr
{
    /**
     * @var string
     *
     * @ORM\Column(name="service_attr_code", type="string", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $serviceAttrCode;

    /**
     * @var string
     *
     * @ORM\Column(name="v", type="text", nullable=false)
     */
    private $v;

    /**
     * @var \OrgService
     *
     * @ORM\OneToOne(targetEntity="OrgService")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_service_id", referencedColumnName="id", unique=true)
     * })
     */
    private $orgService;


    /**
     * Set serviceAttrCode
     *
     * @param string $serviceAttrCode
     * @return OrgServiceAttr
     */
    public function setServiceAttrCode($serviceAttrCode)
    {
        $this->serviceAttrCode = $serviceAttrCode;

        return $this;
    }

    /**
     * Get serviceAttrCode
     *
     * @return string 
     */
    public function getServiceAttrCode()
    {
        return $this->serviceAttrCode;
    }

    /**
     * Set v
     *
     * @param string $v
     * @return OrgServiceAttr
     */
    public function setV($v)
    {
        $this->v = $v;

        return $this;
    }

    /**
     * Get v
     *
     * @return string 
     */
    public function getV()
    {
        return $this->v;
    }

    /**
     * Set orgService
     *
     * @param \OrgService $orgService
     * @return OrgServiceAttr
     */
    public function setOrgService(\OrgService $orgService = null)
    {
        $this->orgService = $orgService;

        return $this;
    }

    /**
     * Get orgService
     *
     * @return \OrgService 
     */
    public function getOrgService()
    {
        return $this->orgService;
    }
}
