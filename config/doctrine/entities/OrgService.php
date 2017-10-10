<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgService
 *
 * @ORM\Table(name="org_service")
 * @ORM\Entity
 */
class OrgService
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="passport_id", type="integer", nullable=false)
     */
    private $passportId;

    /**
     * @var integer
     *
     * @ORM\Column(name="service_type_id", type="integer", nullable=false)
     */
    private $serviceTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=false)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="target_type", type="boolean", nullable=false)
     */
    private $targetType;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="text", nullable=false)
     */
    private $intro;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set passportId
     *
     * @param integer $passportId
     * @return OrgService
     */
    public function setPassportId($passportId)
    {
        $this->passportId = $passportId;

        return $this;
    }

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
     * Set serviceTypeId
     *
     * @param integer $serviceTypeId
     * @return OrgService
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
     * Set name
     *
     * @param string $name
     * @return OrgService
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return OrgService
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set targetType
     *
     * @param boolean $targetType
     * @return OrgService
     */
    public function setTargetType($targetType)
    {
        $this->targetType = $targetType;

        return $this;
    }

    /**
     * Get targetType
     *
     * @return boolean 
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * Set intro
     *
     * @param string $intro
     * @return OrgService
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * Get intro
     *
     * @return string 
     */
    public function getIntro()
    {
        return $this->intro;
    }
}
