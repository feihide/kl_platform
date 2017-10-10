<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrgGroup
 *
 * @ORM\Table(name="org_group", indexes={@ORM\Index(name="fk_org_group_org1_idx", columns={"passport_id"})})
 * @ORM\Entity
 */
class OrgGroup
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
     * @return OrgGroup
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
     * Set intro
     *
     * @param string $intro
     * @return OrgGroup
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
     * @return OrgGroup
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
