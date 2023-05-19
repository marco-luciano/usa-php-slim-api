<?php

/**
 * API Output formatter
 *
 * @param array payload
 *   Payload to format
 * @return string
 */

function formatOutput($payload)
{
    return json_encode($payload, JSON_PRETTY_PRINT);
}
