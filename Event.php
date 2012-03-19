<?php
namespace ical;

class Event
{
    static protected $events = array();
    static protected $defaultTimezone = null;

    static public function setDefaultTimezone($tz) {
        self::$defaultTimezone = $tz;
    }

    /**
     * Create an event from a parsed chunk
     * @param \ical\parser\Chunk $chunk
     * @return \ical\Event
     */
    static public function createFromChunk(\ical\parser\Chunk $chunk) {
        $event = new \ical\Event();
        $event->parseProperties($chunk);

        self::registerEvent($event);

        return $event;
    }

    /**
     * Check whether an event with uid already exists
     * @param string $uid
     * @return bool
     */
    static protected function eventExists($uid) {
        return array_key_exists($uid, self::$events);
    }

    /**
     * Register an event
     * @param \ical\Event $event
     * @return void
     */
    static protected function registerEvent(\ical\Event $event) {
        if (! self::eventExists($event->uid)) {
            self::$events[$event->uid] = $event;
        } else {
            self::$events[$event->uid] = $event->merge(self::$events[$event->uid]);
        }
    }

    protected $uid;
    protected $sequence;
    protected $dtstart;
    protected $dtend;
    protected $duration;
    protected $dtstamp;
    protected $rrule;
    protected $exrules = array();

    protected $summary;
    protected $description;
    protected $location;

    /**
     * @param string $uid
     */
    public function __construct($uid = null) {
        if (! is_null($uid)) {
            $this->setUID($uid);
        }
    }

    public function setUID($uid) {
        $this->uid = $uid;

        self::registerEvent($this);
    }

    /**
     * Add an exception to recurrence
     * @param DateTime $datetime
     * @return void
     */
    public function addException(\DateTime $datetime) {
        $this->exrules[] = $datetime;
    }

    /**
     * Merge properties of two event objects. Properties are only
     * overwritten if the sequence property's value is higher.
     * @param ical\Event $event
     * @return void
     */
    protected function merge(\ical\Event $event) {
        $overwrite = $event->sequence > $this->sequence;
        $props = array_keys(get_object_vars($event));

        foreach ($props as $prop) {
            $this->setProperty($prop, $event->$prop, $overwrite);
        }
    }

    /**
     * Parse properties from a chunk
     * @param \ical\parser\Chunk $chunk
     * @return void
     */
    protected function parseProperties(\ical\parser\Chunk $chunk) {
        if ($chunk->type !== \ical\parser\Chunk::TYPE_EVENT) {
            throw new \InvalidValueException("Invalid chunk type:" . $chunk->type . ", expected:" . \ical\parser\Chunk::TYPE_EVENT);
        }

        foreach ($chunk->getLines() as $line) {
            switch ($line->name) {
                case "UID":
                    $this->setProperty("uid", $line->value);
                    break;

                case "SEQUENCE":
                    $this->setProperty("sequence", intval($line->value));
                    break;

                case "DTSTART":
                    if ($line->getParam("VALUE") === "DATE") {
                        $this->setProperty("dtstart", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    } else {
                        $this->setProperty("dtstart", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    }
                    break;

                case "DTEND":
                    if ($line->getParam("VALUE") === "DATE") {
                        $this->setProperty("dtend", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    } else {
                        $this->setProperty("dtend", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    }
                    break;

                case "CREATED":
                    $this->setProperty("created", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    break;

                case "LAST-MODIFIED":
                    $this->setProperty("lastModified", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    break;

                case "DTSTAMP":
                    $this->setProperty("dtstamp", \ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    break;

                case "RRULE":
                    $this->setProperty("rrule", new \ical\event\Rrule($line));
                    break;

                case "EXDATE":
                    foreach (explode(",", $line->value) as $date) {
                        $this->addException(\ical\event\DateTimeFactory::createFromRow($line, self::$defaultTimezone));
                    }
                    break;

                case "EXRULE":
                    $this->setProperty("exrule", new \ical\event\Rrule($line));
                    break;

                case "SUMMARY":
                    $this->setProperty("summary", $line->value);
                    break;

                case "DESCRIPTION":
                    $this->setProperty("description", $line->value);
                    break;

                case "LOCATION":
                    $this->setProperty("location", $line->value);
                    break;

                case "STATUS":
                    $this->setProperty("status", $line->value);
                    break;

                /*
                 * @todo
                 */
                case "DURATION":
                    break;

                /*
                 * @todo
                 */
                case "TRANSP":
                    break;

                /*
                 * @todo
                 */
                case "ATTENDEE":
                    break;

                /*
                 * @todo
                 */
                case "CATEGORIES":
                    break;

                /*
                 * @todo
                 */
                case "RECURRENCE-ID":
                    break;

                default:
                    if ("X-" === substr($line->name, 0, 2)) {
                        $this->extended[$line->name] = $line->value;
                    }
                    break;
            }
        }
    }

    /**
     * Set a property, with an optional parameter to overwrite existing values
     *
     * @param str $name
     * @param mixed $value
     * @param bool $overwrite
     * @return void
     */
    protected function setProperty($name, $value, $overwrite = false) {
        if (! $overwrite || ! isset($this->$name)) {
            $this->$name = $value;
        }
    }
}