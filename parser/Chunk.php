<?php
namespace ical\parser;

class Chunk
{
    const TYPE_CALENDAR = 'VCALENDAR';
    const TYPE_TIMEZONE = 'VTIMEZONE';
    const TYPE_TZ_DST   = 'DAYLIGHT';
    const TYPE_TZ_STD   = 'STANDARD';
    const TYPE_EVENT    = 'VEVENT';

    public $type;
    public $rows;

    public function __construct(\ical\parser\Row $firstrow) {
        $this->type = $firstrow->value;
        $this->rows = array();
        $this->addRow($firstrow);
    }

    public function addRow(\ical\parser\Row $row) {
        $this->rows[] = $row;
    }

    public function getLines() {
        return $this->rows;
    }

    public function getLine($name = null) {
        if (! is_null($name)) {
            foreach ($this->rows as $row) {
                if ($row->name === $name) {
                    return $row;
                }
            }
        }

        return null;
    }
}