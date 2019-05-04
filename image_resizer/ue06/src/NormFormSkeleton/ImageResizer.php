<?php

namespace NormFormSkeleton;

use DateTime;
use DateTimeZone;
use Exception;
use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Bulletproof\Image as BulletproofImg;

class ImageResizer extends AbstractNormForm
{

    /** Form field constant that is hidden - defining the maximum filesize*/
    public const MAX_UPLOAD_FILESIZE = "maximumFileSize";

    public const SELECTED_IMAGE = "selectedImage";

    public const SCALE_FACTOR = "scaleFactor";

    /**
     * Holds the results of the form submission (assigned in business()).
     *
     * @var array
     */
    private $result;

    /**
     * Holds the image uploaded by the user using the form
     *
     * @var BulletproofImg
     */
    private $image;

    /**
     * Constructor for creating a new object. Use this to perform initializations of properties you need throughout your
     * application, otherwise leave it as is. Do not remove the call to the parent constructor.
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     */
    public function __construct(View $defaultView, string $fileUploadPath)
    {
        parent::__construct($defaultView);
        $this->image = new BulletproofImg($_FILES);
        $this->image->setLocation($fileUploadPath);
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
        if ($this->image[self::SELECTED_IMAGE]) {
            //set maximum filesize
            $this->image->setSize(0, $_POST[self::MAX_UPLOAD_FILESIZE]);

            //set allowed filetype
            $this->image->setMime(["png", "jpeg", "jpg", "gif"]);

            try {
                //create new DateTime object
                $dateTime = new DateTime("now", new DateTimeZone("Europe/Vienna"));
                //assign image name to file
                $this->image->setName("image_" . $dateTime->format("Y-m-d_H-i-s") . "." . $this->image->getMime());

                $upload = $this->image->upload();
                if (!$upload) {
                    $this->errorMessages[self::SELECTED_IMAGE] = $this->image->getError();
                }
            } catch (Exception $e) {
                $this->errorMessages[self::SELECTED_IMAGE] = $e->getMessage();
            }
        }

        $selectedScalefactor = $_POST[self::SCALE_FACTOR];
        if ($this->isEmptyPostField(self::SCALE_FACTOR)) {
            $this->errorMessages[self::SCALE_FACTOR] = "No scale factor selected.";
        } elseif ($selectedScalefactor <= 0 || $selectedScalefactor > 200) {
            $this->errorMessages[self::SCALE_FACTOR] = "Please select a scale factor between 1 and 200";
        }

        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Business logic method used to process the data that was used after a successful validation. In this example the
     * received data is stored in result and passed on to the view. In more complex scenarios this would be the
     * place to add things to a database or perform other tasks before displaying the data.
     */
    protected
    function business(): void
    {

        $this->currentView->setParameter(new GenericParameter("result", $this->image));

        $this->statusMessage = "Processing successful!";
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));

        // Update the three form parameters with empty content so that the form fields are empty upon result display.
        $this->currentView->setParameter(new PostParameter(self::SELECTED_IMAGE, true));
        $this->currentView->setParameter(new PostParameter(self::SCALE_FACTOR, true));

        $scaleFactor = $_POST[self::SCALE_FACTOR] / 100;

        $width = $this->image->getWidth();
        $height = $this->image->getHeight();
        $newWidth = $width * $scaleFactor;
        $newHeight = $height * $scaleFactor;

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $image = imagecreatefromstring(file_get_contents($this->image->getFullPath()));

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($this->image->getMime()) {
            case ("png"):
                imagepng($newImage, $this->image->getFullPath());
                break;
            case("jpg"):
            case ("jpeg"):
                imagejpeg($newImage, $this->image->getFullPath());
                break;
            case("gif"):
                imagegif($newImage, $this->image->getFullPath());
                break;
        }

        imagedestroy($newImage);
        imagedestroy($image);
    }
}
