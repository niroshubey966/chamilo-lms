<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessUrl
 *
 * @ORM\Table(name="access_url")
 * @ORM\Entity
 */
class AccessUrl
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false, unique=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", unique=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false, unique=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false, unique=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tms", type="datetime", nullable=true)
     */
    private $tms;

    /**
     * @var boolean
     *
     * @ORM\Column(name="url_type", type="boolean", nullable=true)
     */
    private $urlType;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_courses", type="integer", nullable=true, unique=false)
     */
    private $limitCourses;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_active_courses", type="integer", nullable=true, unique=false)
     */
    private $limitActiveCourses;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_sessions", type="integer", nullable=true, unique=false)
     */
    private $limitSessions;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_users", type="integer", nullable=true, unique=false)
     */
    private $limitUsers;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_teachers", type="integer", nullable=true, unique=false)
     */
    private $limitTeachers;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_disk_space", type="integer", nullable=true, unique=false)
     */
    private $limitDiskSpace;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=false)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="AccessUrlRelCourse", mappedBy="url", cascade={"persist"}, orphanRemoval=true)
     **/
    protected $course;

    /**
     * @ORM\OneToMany(targetEntity="AccessUrlRelSession", mappedBy="url", cascade={"persist"}, orphanRemoval=true)
     **/
    protected $session;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\SettingsCurrent", mappedBy="url", cascade={"persist"}, orphanRemoval=true)
     **/
    //protected $settings;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\SessionCategory", mappedBy="url", cascade={"persist"}, orphanRemoval=true)
     **/
    protected $sessionCategory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tms = new \DateTime();
        $this->createdBy = 1;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUrl();
    }

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
     * Set url
     *
     * @param string $url
     * @return AccessUrl
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AccessUrl
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return AccessUrl
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return AccessUrl
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set tms
     *
     * @param \DateTime $tms
     * @return AccessUrl
     */
    public function setTms($tms)
    {
        $this->tms = $tms;

        return $this;
    }

    /**
     * Get tms
     *
     * @return \DateTime
     */
    public function getTms()
    {
        return $this->tms;
    }

    /**
     * Set urlType
     *
     * @param boolean $urlType
     * @return AccessUrl
     */
    public function setUrlType($urlType)
    {
        $this->urlType = $urlType;

        return $this;
    }

    /**
     * Get urlType
     *
     * @return boolean
     */
    public function getUrlType()
    {
        return $this->urlType;
    }

    /**
     * @return int
     */
    public function getLimitActiveCourses()
    {
        return $this->limitActiveCourses;
    }

    /**
     * @param int $limitActiveCourses
     * @return AccessUrl
     */
    public function setLimitActiveCourses($limitActiveCourses)
    {
        $this->limitActiveCourses = $limitActiveCourses;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitSessions()
    {
        return $this->limitSessions;
    }

    /**
     * @param int $limitSessions
     * @return AccessUrl
     */
    public function setLimitSessions($limitSessions)
    {
        $this->limitSessions = $limitSessions;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitUsers()
    {
        return $this->limitUsers;
    }

    /**
     * @param int $limitUsers
     * @return AccessUrl
     */
    public function setLimitUsers($limitUsers)
    {
        $this->limitUsers = $limitUsers;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitTeachers()
    {
        return $this->limitTeachers;
    }

    /**
     * @param int $limitTeachers
     * @return AccessUrl
     */
    public function setLimitTeachers($limitTeachers)
    {
        $this->limitTeachers = $limitTeachers;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitDiskSpace()
    {
        return $this->limitDiskSpace;
    }

    /**
     * @param int $limitDiskSpace
     * @return AccessUrl
     */
    public function setLimitDiskSpace($limitDiskSpace)
    {
        $this->limitDiskSpace = $limitDiskSpace;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return AccessUrl
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param mixed $course
     * @return AccessUrl
     */
    public function setCourse($course)
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param mixed $settings
     * @return AccessUrl
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionCategory()
    {
        return $this->sessionCategory;
    }

    /**
     * @param mixed $sessionCategory
     * @return AccessUrl
     */
    public function setSessionCategory($sessionCategory)
    {
        $this->sessionCategory = $sessionCategory;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitCourses()
    {
        return $this->limitCourses;
    }

    /**
     * @param int $limitCourses
     * @return AccessUrl
     */
    public function setLimitCourses($limitCourses)
    {
        $this->limitCourses = $limitCourses;
        return $this;
    }




}
