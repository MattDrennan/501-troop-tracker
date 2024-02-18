<?php

/**
 * This file is the event class which manages events.
 *
 * @author  Matthew Drennan
 *
 */

namespace TroopTracker\Event;

/**
 * The event class manages events.
 * @package TroopTracker\Event
 */
class Event {

    /**
     * @var string Name of the event
     */
    public $eventName;
    
    /**
     * @var string Venue of the event
     */
    public $eventVenue;

    /**
     * @var string Address of the event
     */
    public $location;

    /**
     * @var string Start date for the event
     */
    public $startDate;

    /**
     * @var string End date for the event
     */
    public $endDate;

    /**
     * @var string Website of the event
     */
    public $website;

    /**
     * @var int Number of projected attendees
     */
    public $numberOfAttend;

    /**
     * @var int Requested number of characters
     */
    public $requestedNumber;

    /**
     * @var string Requested characters
     */
    public $requestedCharacter;

    /**
     * @var int Is there provided secure areas?
     */
    public $secure;

    /**
     * @var int Are blasters allowed?
     */
    public $blasters;

    /**
     * @var int Are lightsabers allowed?
     */
    public $lightsabers;

    /**
     * @var int Is there parking?
     */
    public $parking;

    /**
     * @var int Is this venue accessible?
     */
    public $mobility;

    /**
     * @var string Amenities of the event
     */
    public $amenities;

    /**
     * @var string Additional information on the troop - BB Code supported
     */
    public $comments;

    /**
     * @var string Who referred the event
     */
    public $referred;

    /**
     * @var int ID for the event
     */
    public $eventId;

    /**
     * @var int Label type for the event
     */
    public $label;

    /**
     * @var int Thread ID linked to Xenforo Forum
     */
    public $thread_id;

    /**
     * @var int Post ID linked to Xenforo Forum
     */
    public $post_id;

    /**
     * @var int Limited event true or false (0 or 1)
     */
    public $limitedEvent;

    /**
     * @var int Limit total troopers for an event
     */
    public $limitTotalTroopers;

    /**
     * @var int Limit total handlers for an event
     */
    public $limitHandlers;

    /**
     * @var int Limit total friends allowed to be added
     */
    public $friendLimit;

    /**
     * @var int Allow tentative troopers true or false (0 or 1)
     */
    public $allowTentative;

    /**
     * @var int Integer value for closed status
     */
    public $closed;

    /**
     * @var float Amount of direct charity raised
     */
    public $charityDirectFunds;

    /**
     * @var float Amount of indirect charity raised
     */
    public $charityIndirectFunds;

    /**
     * @var string Name of charity raised for
     */
    public $charityName;

    /**
     * @var int Charity additional hours to add for an event
     */
    public $charityAddHours;

    /**
     * @var string Note for charity donation
     */
    public $charityNote;

    /**
     * @var int Integer value for squad where event is taking place
     */
    public $squad;

    /**
     * @var int Value of linked events
     */
    public $link;

    /**
     * @var object Database connection object
     */
    protected $sql;

    public function __construct() {
        // Nothing now
    }

    /**
     * Get event from event ID
     *
     * @return object
     */ 
    public static function getEventFromId(object $conn, int $eventId) {
        // Create new object
        $instance = new self();
        $instance->sql = $conn;

        // Query
        $statement = $instance->sql->prepare("SELECT * FROM events WHERE id = ?");
        $statement->bind_param("i", $eventId);
        $statement->execute();

        if($result = $statement->get_result())
        {
            while ($db = mysqli_fetch_object($result))
            {
                $instance->eventName = $db->name;
                $instance->eventVenue = $db->venue;
                $instance->location = $db->location;
                $instance->startDate = $db->dateStart;
                $instance->endDate = $db->dateEnd;
                $instance->website = $db->website;
                $instance->numberOfAttend = $db->numberOfAttend;
                $instance->requestedNumber = $db->requestedNumber;
                $instance->requestedCharacter = $db->requestedCharacter;
                $instance->secure = $db->secureChanging;
                $instance->blasters = $db->blasters;
                $instance->lightsabers = $db->lightsabers;
                $instance->parking = $db->parking;
                $instance->mobility = $db->mobility;
                $instance->amenities = $db->amenities;
                $instance->comments = $db->comments;
                $instance->eventId = $db->id;
                $instance->label = $db->label;
                $instance->thread_id = $db->thread_id;
                $instance->post_id = $db->post_id;
                $instance->limitedEvent = $db->limitedEvent;
                $instance->limitTotalTroopers = $db->limitTotalTroopers;
                $instance->limitHandlers = $db->limitHandlers;
                $instance->friendLimit = $db->friendLimit;
                $instance->allowTentative = $db->allowTentative;
                $instance->closed = $db->closed;
                $instance->charityDirectFunds = $db->charityDirectFunds;
                $instance->charityIndirectFunds = $db->charityIndirectFunds;
                $instance->charityName = $db->charityName;
                $instance->charityAddHours = $db->charityAddHours;
                $instance->charityNote = $db->charityNote;
                $instance->squad = $db->squad;
                $instance->link = $db->link;
            }
        }

        return $instance;
    }

    /**
     * Get name of the event
     *
     * @return  string
     */ 
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Set name of the event
     *
     * @param  string  $eventName  Name of the event
     *
     * @return  self
     */ 
    public function setEventName(string $eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * Get venue of the event
     *
     * @return  string
     */ 
    public function getEventVenue()
    {
        return $this->eventVenue;
    }

    /**
     * Set venue of the event
     *
     * @param  string  $eventVenue  Venue of the event
     *
     * @return  self
     */ 
    public function setEventVenue(string $eventVenue)
    {
        $this->eventVenue = $eventVenue;

        return $this;
    }

    /**
     * Get address of the event
     *
     * @return  string
     */ 
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set address of the event
     *
     * @param  string  $location  Address of the event
     *
     * @return  self
     */ 
    public function setLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get start date for the event
     *
     * @return  string
     */ 
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set start date for the event
     *
     * @param  string  $startDate  Start date for the event
     *
     * @return  self
     */ 
    public function setStartDate(string $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get end date for the event
     *
     * @return  string
     */ 
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set end date for the event
     *
     * @param  string  $endDate  End date for the event
     *
     * @return  self
     */ 
    public function setEndDate(string $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get website of the event
     *
     * @return  string
     */ 
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set website of the event
     *
     * @param  string  $website  Website of the event
     *
     * @return  self
     */ 
    public function setWebsite(string $website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get number of projected attendees
     *
     * @return  int
     */ 
    public function getNumberOfAttend()
    {
        return $this->numberOfAttend;
    }

    /**
     * Set number of projected attendees
     *
     * @param  int  $numberOfAttend  Number of projected attendees
     *
     * @return  self
     */ 
    public function setNumberOfAttend(int $numberOfAttend)
    {
        $this->numberOfAttend = $numberOfAttend;

        return $this;
    }

    /**
     * Get requested number of characters
     *
     * @return  int
     */ 
    public function getRequestedNumber()
    {
        return $this->requestedNumber;
    }

    /**
     * Set requested number of characters
     *
     * @param  int  $requestedNumber  Requested number of characters
     *
     * @return  self
     */ 
    public function setRequestedNumber(int $requestedNumber)
    {
        $this->requestedNumber = $requestedNumber;

        return $this;
    }

    /**
     * Get is there provided secure areas?
     *
     * @return  int
     */ 
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Set is there provided secure areas?
     *
     * @param  int  $secure  Is there provided secure areas?
     *
     * @return  self
     */ 
    public function setSecure(int $secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * Get are blasters allowed?
     *
     * @return  int
     */ 
    public function getBlasters()
    {
        return $this->blasters;
    }

    /**
     * Set are blasters allowed?
     *
     * @param  int  $blasters  Are blasters allowed?
     *
     * @return  self
     */ 
    public function setBlasters(int $blasters)
    {
        $this->blasters = $blasters;

        return $this;
    }

    /**
     * Get are lightsabers allowed?
     *
     * @return  int
     */ 
    public function getLightsabers()
    {
        return $this->lightsabers;
    }

    /**
     * Set are lightsabers allowed?
     *
     * @param  int  $lightsabers  Are lightsabers allowed?
     *
     * @return  self
     */ 
    public function setLightsabers(int $lightsabers)
    {
        $this->lightsabers = $lightsabers;

        return $this;
    }

    /**
     * Get is there parking?
     *
     * @return  int
     */ 
    public function getParking()
    {
        return $this->parking;
    }

    /**
     * Set is there parking?
     *
     * @param  int  $parking  Is there parking?
     *
     * @return  self
     */ 
    public function setParking(int $parking)
    {
        $this->parking = $parking;

        return $this;
    }

    /**
     * Get is this venue accessible?
     *
     * @return  int
     */ 
    public function getMobility()
    {
        return $this->mobility;
    }

    /**
     * Set is this venue accessible?
     *
     * @param  int  $mobility  Is this venue accessible?
     *
     * @return  self
     */ 
    public function setMobility(int $mobility)
    {
        $this->mobility = $mobility;

        return $this;
    }

    /**
     * Get amenities of the event
     *
     * @return  string
     */ 
    public function getAmenities()
    {
        return $this->amenities;
    }

    /**
     * Set amenities of the event
     *
     * @param  string  $amenities  Amenities of the event
     *
     * @return  self
     */ 
    public function setAmenities(string $amenities)
    {
        $this->amenities = $amenities;

        return $this;
    }

    /**
     * Get additional information on the troop - BB Code supported
     *
     * @return  string
     */ 
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set additional information on the troop - BB Code supported
     *
     * @param  string  $comments  Additional information on the troop - BB Code supported
     *
     * @return  self
     */ 
    public function setComments(string $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get who referred the event
     *
     * @return  string
     */ 
    public function getReferred()
    {
        return $this->referred;
    }

    /**
     * Set who referred the event
     *
     * @param  string  $referred  Who referred the event
     *
     * @return  self
     */ 
    public function setReferred(string $referred)
    {
        $this->referred = $referred;

        return $this;
    }

    /**
     * Get iD for the event
     *
     * @return  int
     */ 
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set iD for the event
     *
     * @param  int  $eventId  ID for the event
     *
     * @return  self
     */ 
    public function setEventId(int $eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }
}

?>