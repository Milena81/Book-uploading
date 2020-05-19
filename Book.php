<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Http\Message\UploadedFile;


class Book extends Model
{
    public function initialize()
    {
        $this->setSource('book');

        $this->hasMany(
            'id',
            Filesbook::class,
            'book_id',
            array
            (
                'foreignKey'=> true
            )
        );
    }

    public function beforeValidationOnCreate()
    {
        $this->date_added   = time();
        return true;
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'bookname',
                new Uniqueness(
                    [
                        "message"=> "The book title must be unique!",
                    ]
                )
        );

        /*Simply validates specified string length constraints*/
        $validator->add(['bookname', 'authorname', 'publisher'],
            new StringLengthValidator(
                [
                    "max"             => 50,
                    "min"             => 2,
                    "messageMaximum"  => "We don't like really long names",
                    "messageMinimum"  => "We want more than just their initials",
                ]
            )
        );

        $validator->add(['bookname', 'authorname', 'publisher'],
            new PresenceOf(
                [
                    "message" => ":field is required"
                ]
            ));

        return  $this->validate($validator);

        if ($this->validationHasFailed() === true){
            return false;
        }
    }
}