<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Org
 *
 * @ORM\Table(name="org")
 * @ORM\Entity
 */
class Org
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
     * @var integer
     *
     * @ORM\Column(name="brand_id", type="integer", nullable=false)
     */
    private $brandId;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer", nullable=false)
     */
    private $cityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sub_num", type="integer", nullable=false)
     */
    private $subNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="biaodi_id", type="integer", nullable=false)
     */
    private $biaodiId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;


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
     * Set brandId
     *
     * @param integer $brandId
     * @return Org
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId
     *
     * @return integer 
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     * @return Org
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer 
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Org
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set subNum
     *
     * @param integer $subNum
     * @return Org
     */
    public function setSubNum($subNum)
    {
        $this->subNum = $subNum;

        return $this;
    }

    /**
     * Get subNum
     *
     * @return integer 
     */
    public function getSubNum()
    {
        return $this->subNum;
    }

    /**
     * Set biaodiId
     *
     * @param integer $biaodiId
     * @return Org
     */
    public function setBiaodiId($biaodiId)
    {
        $this->biaodiId = $biaodiId;

        return $this;
    }

    /**
     * Get biaodiId
     *
     * @return integer 
     */
    public function getBiaodiId()
    {
        return $this->biaodiId;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Org
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
