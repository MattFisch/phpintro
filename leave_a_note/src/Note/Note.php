<?php

namespace Note;

use DateTime;
use DateTimeZone;
use Exception;
use XMLReader;
use XMLWriter;

class Note
{

    private const NOTES_STORAGE_FILEPATH = "data/note.xml";

    private $timestamp;

    private $firstName;

    private $lastName;

    private $message;

    public function __construct(string $firstName, string $lastName, string $message, DateTime $timestamp)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->message = $message;
        $this->timestamp = $timestamp;
    }

    public static function isNoteStored(): bool
    {
        return file_exists(self::NOTES_STORAGE_FILEPATH);
    }

    public function storeNote(): void
    {
        $xmlWriter = new XMLWriter();

        if (!file_exists(dirname(self::NOTES_STORAGE_FILEPATH))) {
            mkdir(dirname(self::NOTES_STORAGE_FILEPATH));
        }

        $xmlWriter->openUri(self::NOTES_STORAGE_FILEPATH);
        $xmlWriter->startDocument('1.0', 'utf-8');
        $xmlWriter->startElement("note");
        $xmlWriter->startElement("timestamp");
        $xmlWriter->writeAttribute("timezone", $this->timestamp->getTimezone()->getName());
        $xmlWriter->writeElement("date", $this->timestamp->format("d.m.Y"));
        $xmlWriter->writeElement("time", $this->timestamp->format("H.i.s"));
        $xmlWriter->endElement();
        $xmlWriter->writeElement("firstname", $this->firstName);
        $xmlWriter->writeElement("lastname", $this->lastName);
        $xmlWriter->writeElement("message", $this->message);
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        $xmlWriter->flush(true);
    }

    public static function readNote(): Note
    {
        $xmlReader = new XMLReader();
        $xmlReader->open(self::NOTES_STORAGE_FILEPATH);

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType === XMLReader::ELEMENT) {
                switch ($xmlReader->name) {
                    case "timestamp":
                        {
                            $timezone = $xmlReader->getAttribute("timezone");
                            break;
                        }
                    case "date":
                        {
                            $date = $xmlReader->readString();
                            break;
                        }
                    case "time":
                        {
                            $time = $xmlReader->readString();
                            break;
                        }
                    case "firstname":
                        {
                            $firstName = $xmlReader->readString();
                            break;
                        }
                    case "lastname":
                        {
                            $lastName = $xmlReader->readString();
                            break;
                        }
                    case "message":
                        {
                            $message = $xmlReader->readString();
                            break;
                        }
                }
            }
        }

        try {
            $timestamp = new DateTime($date . " " . $time, new DateTimeZone($timezone));
        } catch (Exception $e) {
            echo $e;
        }

        return $note = new Note($firstName, $lastName, $message, $timestamp);
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}