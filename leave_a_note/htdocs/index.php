<?php

require "../vendor/autoload.php";

use NormForm\LeaveANote;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;

/*
 * Toggles debugging mode on or off
 */
define("DEBUG", true);

/*
 * Activate debugging to display HTML errors in browser
 */
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set("html_errors", "1");
    ini_set("display_errors", "1");
    ini_set("display_startup_errors", "1");
}

$view = new View(
    "leaveANote.html.twig",
    "../templates",
    "../templates_c",
    [
        new PostParameter(LeaveANote::FIRST_NAME),
        new PostParameter(LeaveANote::LAST_NAME),
        new PostParameter(LeaveANote::MESSAGE),
    ]
);

$form = new LeaveANote($view);
$form->normForm();
