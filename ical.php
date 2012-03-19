<?php
define('ICAL_BASEDIR', __DIR__);

include ICAL_BASEDIR . '/Parser.php';
include ICAL_BASEDIR . '/parser/Row.php';
include ICAL_BASEDIR . '/parser/Chunk.php';
include ICAL_BASEDIR . '/Calendar.php';
include ICAL_BASEDIR . '/Event.php';
include ICAL_BASEDIR . '/event/Rrule.php';
include ICAL_BASEDIR . '/util/DateTimeFactory.php';