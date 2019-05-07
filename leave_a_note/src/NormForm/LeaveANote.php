<?php

namespace NormForm;

use DateTime;
use DateTimeZone;
use Exception;
use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Note\Note;

class LeaveANote extends AbstractNormForm
{

    /** Form field constant that defines how the form field for holding a first name is called (id/name). */
    public const FIRST_NAME = "firstname";

    /** Form field constant that defines how the form field for holding a last name is called (id/name). */
    public const LAST_NAME = "lastname";

    /** Form field constant that defines how the form field for holding a message is called (id/name). */
    public const MESSAGE = "message";

    public const TIMEZONE = "Europe/Vienna";

    private $note;

    /**
     * Constructor for creating a new object. Use this to perform initializations of properties you need throughout your
     * application, otherwise leave it as is. Do not remove the call to the parent constructor.
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);

        if (Note::isNoteStored()) {
            $this->note = Note::readNote();
            $this->currentView->setParameter(new GenericParameter("note", $this->note));
        }
    }

    /**
     * Validates the form submission. The criteria for this example are non-empty fields for first and last name.
     * These are checked using isEmptyPostField() in two separate if-clauses.
     * If a criterion is violated, an entry in errorMessages is created.
     * The array holding these error messages is then added to the parameters of the current view. If no error
     * messages where created, validation is seen as successful.
     *
     * @return bool Returns true if validation was successful, otherwise false.
     */
    protected function isValid(): bool
    {
        if ($this->isEmptyPostField(self::FIRST_NAME)) {
            $this->errorMessages[self::FIRST_NAME] = "First name is required.";
        }
        if ($this->isEmptyPostField(self::LAST_NAME)) {
            $this->errorMessages[self::LAST_NAME] = "Last name is required.";
        }
        if ($this->isEmptyPostField(self::MESSAGE)) {
            $this->errorMessages[self::LAST_NAME] = "Message is required.";
        }

        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));

        return (count($this->errorMessages) === 0);
    }

    /**
     * Business logic method used to process the data that was used after a successful validation. In this example the
     * received data is stored in result and passed on to the view. In more complex scenarios this would be the
     * place to add things to a database or perform other tasks before displaying the data.
     */
    protected function business(): void
    {
        try {
            $timestamp = new DateTime("now", new DateTimeZone(self::TIMEZONE));
        } catch (Exception $e) {
        }
        $this->note = new Note($_POST[self::FIRST_NAME], $_POST[self::LAST_NAME], $_POST[self::MESSAGE], $timestamp);
        $this->note->storeNote();

        $this->currentView->setParameter(new GenericParameter("note", $this->note));

        $this->statusMessage = "Processing successful!";
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));

        // Update the three form parameters with empty content so that the form fields are empty upon result display.
        $this->currentView->setParameter(new PostParameter(self::FIRST_NAME, true));
        $this->currentView->setParameter(new PostParameter(self::LAST_NAME, true));
        $this->currentView->setParameter(new PostParameter(self::MESSAGE, true));
    }
}
