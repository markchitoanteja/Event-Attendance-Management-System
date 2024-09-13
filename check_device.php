<?php
function isMobile()
{
    $mobileAgents = ['iPhone', 'iPad', 'Android', 'webOS', 'BlackBerry', 'iPod', 'Opera Mini', 'IEMobile', 'Mobile'];

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        foreach ($mobileAgents as $agent) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
                return true;
            }
        }
    }
    return false;
}

$device = "desktop";

if (isMobile()) {
    $device = "mobile";
}
