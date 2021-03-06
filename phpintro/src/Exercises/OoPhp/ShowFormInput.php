<?php

namespace phpintro\src\exercises\oophp;

/**
 * This class should not be able to do more than printing the content of $_POST
 * sent by index.html. Additionally a class constant and a class property is
 * defined. Both are printed in different context within and outside a class to
 * see differences in the syntax. An additional objective of this exercise is
 * to understand the scope of class constants, class properties and global
 * constants like DEBUG and see differences in syntax.
 *
 * This class implements one method to process the content of $_POST and return
 * it.
 */

/**
 * Include error handling, to display errors in browser for easier debugging
 * during development.
 */
require_once 'error_handling.php';
/**
 * TODO Have a look at the examples in Examples/OoPhp
 * TODO Start with DefineAndConst.php and Inheritance.php
 * TODO Then look at Methods.php and call_methods.php, that demonstrate, how to
 * become fully PSR2 compliant. TODO PSR2 compliance is not part of this
 * exercise, but needed for those following.
 *
 */

/**
 * Defining the class
 *
 * TODO Define a class with a class name in StudlyCaps according to PSR1
 */
class ThisIsMyClass
{

    //TODO Within this class

    /**
     * TODO Define a class constant
     * TODO @see Examples/OoPhp/DefineAndConst.php
     */
    const CONSTANT_VALUE = "CONSTANT_VALUE";

    /**
     * TODO Define a class property with scope public
     * TODO @see Examples/OoPhp/PublicProtectedPrivateProperties.php
     */
    public $classProperty = "classProperty";

    /**
     * TODO Define a constructor
     * TODO @see Examples/OoPhp/DefindeAndConst.php
     */
    public function __construct()
    {
        // TODO Print the class constant defined above with echo or print.
        // TODO Enclose the value with a <p> tag using string concatenation.
        // Example: echo "<p>". variable or constant ."</p>"
        echo "<p>" . self::CONSTANT_VALUE . "</p>";

        // TODO Print the class property defined above with echo or print.
        // TODO Enclose the value with a <p> tag.
        echo "<p>" . $this->classProperty . "</p>";

        // TODO Print the value of the global constant DEBUG defined in error_handling.php with echo or print.
        // TODO Enclose the value with a <p> tag.
        echo "<p>" . DEBUG . "</p>";

        // TODO end of constructor (closing curly brake)
    }

    /**
     * TODO Define a method with scope public
     * TODO @see Examples/OoPhp/Inheritance.php
     */
    public function myFirstMethod(): void
    {
        // TODO Print the content of the $_POST-Array sent by index.html.
        // TODO Prevent XSS for all browsers.
        // TODO Use htmlentities(), htmlspecialchars() or strip_tags() to do so.
        $email = $_POST['email'];
        echo "<p>" . htmlentities($email) . "</p>";

        // TODO end of method (closing curly brake)
    }

    // TODO end of class (closing curly brake)
}

/**
 * Using the class
 * These steps are normally implemented in an additional file. They cause side
 * effects.
 *
 * @see PSR2 and Examples/oophp/Methods.php and call_methods.php to see PSR2
 *   compliance.
 */

// TODO Initialize a first object of the class
// TODO Name the object according to the class name and to PSR1 in camelCase.
$thisIsMyClassInstanceA = new ThisIsMyClass();

// TODO Call the method with scope public of the class
$thisIsMyClassInstanceA->myFirstMethod();

// TODO Print the first object with var_dump()
var_dump($thisIsMyClassInstanceA);

// TODO Print the class constant defined above with echo or print.
// TODO Enclose the value with a <p> tag.
echo "<p>" . ThisIsMyClass::CONSTANT_VALUE . "</p>";

// TODO Initialize a second object of the class
// TODO Name the object according to the class name and to PSR1 in camelCase.
$thisIsMyClassInstanceB = new ThisIsMyClass();

// TODO Print the second object with var_dump() here
var_dump($thisIsMyClassInstanceB);

// TODO Print the class property defined above with echo or print.
// TODO Enclose the value with a <p> tag.
echo "<p>" . $thisIsMyClassInstanceB->classProperty . "</p>";

// TODO Print the value of the global constant DEBUG defined in error_handling.php with echo.
// TODO Enclose the value with a <p> tag.
echo "<p>" . DEBUG . "</p>";

// At the end of a PHP file there is only a line break and no closing PHP processing instruction according to PSR2
