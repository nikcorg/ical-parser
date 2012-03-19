<?php
namespace ical;

class Calendar
{
    static public function createFromChunk(\ical\parser\Chunk $chunk) {
        $calendar = new \ical\Calendar;
        $calendar->parseProperties($chunk);

        return $calendar;
    }

    /* Calendar properties */
    protected $version = null;
    protected $prodid = null;
    protected $calscale = null;
    protected $method = null;
    protected $extended = array();

    /**
     * @var array \ical\Event
     */
    protected $events = array();

    public function addEvent(\ical\Event $event) {
        $this->events[] = $event;
    }

    /**
     * Parses row values from a chunk
     * @param ical\parser\Chunk $chunk
     * @return void
     * @throws UnexpectedValueException
     */
    public function parseProperties(\ical\parser\Chunk $chunk) {
        if ($chunk->type !== \ical\parser\Chunk::TYPE_CALENDAR) {
            throw new \UnexpectedValueException("Invalid chunk type:" . $chunk->type . ", expected:" . \ical\parser\Chunk::TYPE_CALENDAR);
        }

        foreach ($chunk->getLines() as $line) {
            switch ($line->name) {
                case 'VERSION':
                    $this->version = $line->value;
                    break;

                case 'PRODID':
                    $this->prodid = $line->value;
                    break;

                case 'CALSCALE':
                    $this->calscale = $line->value;
                    break;

                case 'METHOD':
                    $this->method = $line->value;
                    break;

                case 'X-WR-TIMEZONE':
                    \ical\Event::setDefaultTimezone(new \DateTimeZone($line->value));
                    break;

                default:
                    if ('X-' === substr($line->name, 0, 2)) {
                        $this->extended[$line->name] = $line->value;
                    }
                    break;
            }
        }
    }
}