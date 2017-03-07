<?php

namespace Ebooks\Ebooks\Freebooks\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Freebooks
 *
 * @ORM\Table(name="ebooks_ebooks_freebooks", indexes={@ORM\Index(name="ebook_id_index", columns={"ebook_id"}), @ORM\Index(name="limit_id_index", columns={"limit_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Freebooks extends \Kazist\Table\BaseTable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ebook_id", type="integer", length=11, nullable=false)
     */
    protected $ebook_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_id", type="integer", length=11, nullable=false)
     */
    protected $limit_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $start_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $end_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=true)
     */
    protected $created_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $date_created;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", length=11, nullable=true)
     */
    protected $modified_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $date_modified;


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
     * Set ebook_id
     *
     * @param integer $ebookId
     * @return Freebooks
     */
    public function setEbookId($ebookId)
    {
        $this->ebook_id = $ebookId;

        return $this;
    }

    /**
     * Get ebook_id
     *
     * @return integer 
     */
    public function getEbookId()
    {
        return $this->ebook_id;
    }

    /**
     * Set limit_id
     *
     * @param integer $limitId
     * @return Freebooks
     */
    public function setLimitId($limitId)
    {
        $this->limit_id = $limitId;

        return $this;
    }

    /**
     * Get limit_id
     *
     * @return integer 
     */
    public function getLimitId()
    {
        return $this->limit_id;
    }

    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Freebooks
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Freebooks
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Get created_by
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Get modified_by
     *
     * @return integer 
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Get date_modified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        // Add your code here
    }
}
