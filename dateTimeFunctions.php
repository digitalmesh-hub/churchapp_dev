<?php

function strtotimeNew($datetime = '', $baseTimestamp = null): int|false
{
        $datetime  = is_null($datetime) ? '' : $datetime;
        return strtotime($datetime, $baseTimestamp);
}
