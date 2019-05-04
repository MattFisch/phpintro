<?php

require "../vendor/autoload.php";

use NormFormSkeleton\ImageResizer;
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
    "imageResizer.html.twig",
    "../templates",
    "../templates_c",
    [
        new PostParameter(ImageResizer::MAX_UPLOAD_FILESIZE),
        new PostParameter(ImageResizer::SELECTED_IMAGE),
        new PostParameter(ImageResizer::SCALE_FACTOR),
    ]
);

$form = new ImageResizer($view, "images");
$form->normForm();
