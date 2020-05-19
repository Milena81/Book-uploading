<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Http\Message\UploadedFile;
use Phalcon\Http\Request\File;

class Filesbook extends Model
{
    public $id;
    public $book_id;

    public function initialize()
    {
        $this->setSource('file_book');

        $this->belongsTo(
            'book_id',
            Book::class,
            'id',
            array(
                'alias'=>'book',
                'reusable'=>true
            )
        );
    }

    public function validation()
    {
        $validator = new validation();

        $fileValidator = new Filesbook(
          [
              "maxSize"              => "2M",
              "messageSize"          => ":field exceeds the max filesize (:max)",
              "allowedTypes"         => [
                  "image/png",
                  "image/jpeg",
                  "image/jpg"
                  ],
              "messageType"          => "Allowed file types are :filetypes",
              "maxResolution"        => "800x600",
              "messageMaxResolution" => "Max resolution of :field is :max",
//              "fileUploaded"         => "File must be uploaded",
          ]
        );

//        $validator->add('filebook', $fileValidator);


        $messages = $validator->validate($_FILES);
    }

}