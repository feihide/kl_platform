<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgMemberGroup
 *
 * @ORM\Table(name="org_member_group", indexes={@ORM\Index(name="fk_org_user_group_org1_idx", columns={"passport_id"})})
 * @ORM\Entity
 */
class OrgMemberGroup
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="text", nullable=false)
     */
    private $intro;

    /**
     * @var \Org
     *
     * @ORM\ManyToOne(targetEntity="Org")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passport_id", referencedColumnName="passport_id")
     * })
     */
    private $passport;


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
     * Set name
     *
     * @param string $name
     * @return OrgMemberGroup
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
     * Set type
     *
     * @param boolean $type
     * @return OrgMemberGroup
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set intro
     *
     * @param string $intro
     * @return OrgMemberGroup
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

    /**
     * Set passport
     *
     * @param \Org $passport
     * @return OrgMemberGroup
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
