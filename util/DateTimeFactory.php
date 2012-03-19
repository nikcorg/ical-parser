<?php
namespace ical\util;

class DateTimeFactory
{
    /**
     * Constructs a DateTime object from a parsed row
     * @param ical\parser\Row $row
     * @param DateTimeZone $defaultTimezone (optional)
     * @return DateTime
     */
    static public function createFromRow(\ical\parser\Row $row, \DateTimeZone $defaultTimezone = null) {
        $tzid = $row->getParam("TZID");
        $tz = null;

        if (! is_null($tzid)) {
            $tz = new \DateTimeZone($tzid);
        } elseif ("Z" === substr($row->value, -1)) {
            $tz = new \DateTimeZone("UTC");
        }

        return new \DateTime($row->value, $tz ?: $defaultTimezone);
    }
}