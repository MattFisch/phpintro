<?php

namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Utilities\Utilities;

/*
 * the object-oriented and template based Contact implements a contact form.
 * *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package phpintro
 * @version 2017
 */

final class Contact extends AbstractNormForm
{

    /**
     * constants for HTML attributes : <input name='pname' id='pname' ... >,
     * <label for='pname' ... >, keys for $_POST[self::PNAME]..
     *
     * @var string SUBJECT Key for $_POST-Array
     * @var string REQUEST Key for $_POST-Array
     * @var string EMAIL Key for $_POST-Array
     * @var string PRIORITY Key for $_POST-Array
     */
    private const SUBJECT = "subject";

    private const REQUEST = "request";

    private const EMAIL = "email";

    private const PRIORITY = "priority";

    /**
     * Contact Constructor.
     *
     * Uses Class View included by AbstractNormForm to define,
     * which template to use and the names of the HTML input fields
     * Calls the constructor of class AbstractNormform.
     */
    public function __construct()
    {
        // TODO Look into htdocs/templates/contactForm.html.twig and complete the template
        $view = new View(
            "contactMain.html.twig",
            "../templates",
            "../templates_c",
            [
                new PostParameter(Contact::SUBJECT),
                new PostParameter(Contact::REQUEST),
                new PostParameter(Contact::EMAIL),
                new PostParameter(Contact::PRIORITY),
            ]
        );
        parent::__construct($view);
    }

    /**
     * Validates the input after sending the form.
     *
     * Examples for REGEX to validate input can be found in
     * src/Utilities/Utilities.php
     *
     * Abstract methods of class AbstractNormForm have to be implemented here
     *
     * @return bool true, if $errorMessages is empty. Else false
     */
    protected function isValid(): bool
    {
        // TODO Add your own solution here. Keep code that ist already there.
        // TODO Sometimes it will be part of your solution. Sometimes you will have to discard it.
        // TODO Decide before you finish your work
        // TODO @see src/NormFormSkeleton/NormFormDemo.php and change the code,
        // TODO to match the requirements of templates/contactMain.html.twig
        if ($this->isEmptyPostField(Contact::SUBJECT)) {
            $this->errorMessages[Contact::SUBJECT] = "Subject is required.";
        }

        if ($this->isEmptyPostField(Contact::REQUEST)) {
            $this->errorMessages[Contact::REQUEST] = "Request is required.";
        }

        if ($this->isEmptyPostField(Contact::EMAIL)) {
            $this->errorMessages[Contact::EMAIL] = "Email is required.";
        } elseif (!Utilities::isEmail($_POST[Contact::EMAIL])) {
            $this->errorMessages[Contact::EMAIL] = "Email is invalid.";
        }

        if ($this->isEmptyPostField(Contact::PRIORITY)) {
            $this->errorMessages[Contact::PRIORITY] = "Priority is required.";
        }

        //%%contact/isValid
        //TODO keep the next two lines
        $this->currentView->setParameter(new GenericParameter("errorMessages",
            $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * processes data sent via form
     * shows a status message, when processing data succeeded.
     *
     * abstract methods of AbstractNormForm have to be implemented here
     */
    protected function business(): void
    {
        // TODO Add your own solution here. Keep code that ist already there.
        // TODO Sometimes it will be part of your solution. Sometimes you will have to discard it.
        // TODO Decide before you finish your work
        // TODO @see src/NormFormSkeleton/NormFormDemo.php
        //%%contact/business

        $this->currentView->setParameter(new GenericParameter("result",
            $_POST));

        $this->statusMessage = "Processing successful!";
        $this->currentView->setParameter(new GenericParameter("statusMessage",
            $this->statusMessage));
    }
}
