<?php

namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\View\View;
use FileAccess\FileAccess;
use FileAccess\FileAccessException;
use Utilities\Utilities;

/**
 * The registration page of the IMAR image archive.
 *
 * This class enables users to create a new account for the IMAR system. By choosing a user name, providing an e-mail
 * address and a password (as well as a repetition of the latter) a new user is created. Before adding the user to the
 * list of existing accounts the system checks if user name and e-mail address are unique and if (simple) password
 * criteria are met.
 *
 * @author  Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 2017
 */
final class Register extends AbstractNormForm
{

    /**
     * @var string USERNAME Form field constant that defines how the form field for holding the username is called
     * (id/name).
     */
    const USERNAME = "username";

    /**
     * @var string EMAIL Form field constant that defines how the form field for holding the e-mail address is called
     * (id/name).
     */
    const EMAIL = "email";

    /**
     * @var string PASSWORD1 Form field constant that defines how the form field for holding the password is called
     * (id/name).
     */
    const PASSWORD = "password";

    /**
     * @var string PASSWORD2 Form field constant that defines how the form field for holding the password repetition is
     * called (id/name).
     */
    const PASSWORD_RETYPE = "passwordretype";

    /**
     * @var string USER_ID Constant used to specify the name of the auto-increment key.
     */
    const USER_ID = "userid";

    /**
     * @var string USER_DATA_PATH The full path for the user meta data JSON file.
     */
    const USER_DATA_PATH = DATA_DIRECTORY . "userdata.json";

    /**
     * @var FileAccess $fileAccess The object handling all file access operations.
     */
    private $fileAccess;

    /**
     * Creates a new Register object based on AbstractNormForm. Takes a View object that holds the information about
     * which template will be shown and which parameters (e.g. for form fields) are passed on to the template.
     * The constructor needs to initialize the object for file handling.
     *
     * @param View   $defaultView The default View object with information on what will be displayed.
     * @param string $templateDir The Smarty template directory.
     * @param string $compileDir  The Smarty compiled template directory.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);

        // TODO: Create a FileAccess object and assign it to $this->fileAccess;
        // TODO: @see src/FAdemo.php for this
        $this->fileAccess = new FileAccess();

        //%%Register/construct
    }

    /**
     * Validates user input after submitting registration information. The function has to check if all fields
     * were filled out and then checks for uniqueness of username and e-mail address. Email has to be a valid
     * address. Passwords need to correspond to certain criteria and also match.
     *
     * @return bool Returns true if no errors occurred and therefore no error messages were set, otherwise false.
     */
    protected function isValid(): bool
    {
        // TODO: The code for correct form validation goes here.
        // TODO: Check for empty fields, correct e-mail and passwords and username
        // TODO: Look into src/Utilities.php to find Regex examples.
        // TODO: You can either call this examples by Utilitis::method() or copy them to this class and adapt them
        // TODO: When copying, make the method private instead of public static
        // TODO: @see src/FAdemo.php for this
        if ($this->isEmptyPostField(Register::USERNAME) || Utilities::isEmptyString($_POST[Register::USERNAME])) {
            $this->errorMessages[Register::USERNAME] = "Username is required.";
        } elseif (!$this->isUnique("username", $_POST[Register::USERNAME])) {
            $this->errorMessages[Register::USERNAME] = "Username is already used.";
        }

        if ($this->isEmptyPostField(Register::EMAIL) || Utilities::isEmptyString($_POST[Register::EMAIL])) {
            $this->errorMessages[Register::EMAIL] = "Email is required.";
        } elseif (!Utilities::isEmail($_POST[Register::EMAIL])) {
            $this->errorMessages[Register::EMAIL] = "Email is invalid.";
        } elseif (!$this->isUnique("email", $_POST[Register::EMAIL])) {
            $this->errorMessages[Register::EMAIL] = "Email is already used.";
        }

        if ($this->isEmptyPostField(Register::PASSWORD) || Utilities::isEmptyString($_POST[Register::PASSWORD])) {
            $this->errorMessages[Register::PASSWORD] = "Password is required.";
        } elseif (!Utilities::isPassword($_POST[Register::PASSWORD], 8, 12)) {
            $this->errorMessages[Register::PASSWORD] = "Password is invalid (should be 8 to 12 characters long)";
        }

        if ($this->isEmptyPostField(Register::PASSWORD_RETYPE) || Utilities::isEmptyString($_POST[Register::PASSWORD_RETYPE])) {
            $this->errorMessages[Register::PASSWORD_RETYPE] = "Password retype is required.";
        } elseif (!Utilities::isPassword($_POST[Register::PASSWORD_RETYPE], 8, 12)) {
            $this->errorMessages[Register::PASSWORD_RETYPE] = "Password retype is invalid (should be 8 to 12 characters long)";
        } elseif (strcmp($_POST[Register::PASSWORD], $_POST[Register::PASSWORD_RETYPE]) !== 0) {
            $this->errorMessages[Register::PASSWORD_RETYPE] = "Both passwords should be the same";
        }

        //%%Register/isValid

        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));

        return (count($this->errorMessages) === 0);
    }

    /**
     * This method is only called when the form input was validated successfully. It adds the newly entered user to the
     * list of existing users and then forwards to the LOGIN page.
     */
    protected function business(): void
    {
        if ($this->addUser()) {
            View::redirectTo('Login.php');
        } else {
            $this->errorMessages["addingUser"] = "The user could not be added.";
        }
    }

    /**
     * Checks for uniqueness of a certain value in the $_POST array. This method is used to check if the user name or
     * e-mail address are unique or already existing. Therefore the existing users are loaded and the array is searched
     * for the supplied value.
     *
     * @param string $field
     * @param string $value
     *
     * @return bool Returns true if no match is found, otherwise false.
     */
    private function isUnique(string $field, string $value): bool
    {
        // TODO: Check if the provided username or email is unique (meaning not already in the data).
        // TODO: @see src/FAdemo.php for this. Use self::USER_DATA_PATH instead of self::TEST_DATA_PATH
        // TODO: Read whole array and step through it comparing each username or email with the entry in $_POST
        // TODO: with foreach or use in_array() combined with array_column() see PHP Documentation
        // TODO: Handle the special case, that the array is empty.
        try {
            $registeredUsers = $this->fileAccess->loadContents(self::USER_DATA_PATH);
        } catch (FileAccessException $e) {
            echo $e;
        }

        foreach ($registeredUsers as $user) {
            if ($user[$field] == $value) {
                return false;
            }
        }

        //##%%
        return true;
        //#%#%
        //%%Register/isUnique
    }

    /**
     * Adds a new user to the list of existing users. An entry for the two-dimensional user array is created and values
     * for user name, e-mail-address and password are taken from the values in $_POST. Additionally an auto-increment ID
     * is generated and added as well in order to assign a unique user id. After the entry is created, the updated
     * two-dimensional array is stored again in the JSON file.
     *
     * @return bool Returns true if the operation was successful, otherwise false.
     */
    private function addUser(): bool
    {
        // TODO: Add the user (ID, user name, e-mail, password) to the two-dimensional array and store it.
        // TODO: @see src/FAdemo.php for this. Use self::USER_DATA_PATH instead of self::TEST_DATA_PATH
        // TODO: add the fields userid, username, email and password
        // TODO: use FileAccess::AutoIncrement for userid
        // TODO: use Utilities::sanitizeFilter for username, email is validated by isValid() with Regex, that doesn't
        // TODO: allow XSS
        // TODO: use password_hash() for the password
        $newUser = [];
        try {
            $registeredUsers = $this->fileAccess->loadContents(self::USER_DATA_PATH);
            $newUser["userid"] = $this->fileAccess->autoincrementID(self::USER_DATA_PATH, "userid");
            $newUser["username"] = Utilities::sanitizeFilter($_POST[Register::USERNAME]);
            $newUser["email"] = Utilities::sanitizeFilter($_POST[Register::EMAIL]);
            $newUser["password"] = password_hash($_POST[Register::PASSWORD], PASSWORD_DEFAULT);

            array_push($registeredUsers, $newUser);
            if ($this->fileAccess->storeContents(self::USER_DATA_PATH, $registeredUsers) !== true) {
                return false;
            }
        } catch (FileAccessException $e) {
            echo $e;
        }

        //##%%
        return true;
        //#%#%
        //%%Register/addUser
    }
}
