<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use SoftCreatR\MimeDetector\MimeDetector;
use SoftCreatR\MimeDetector\MimeDetectorException;

class CheckMimeFile implements Rule
{
    /**
     * Determine mime type of file.
     * 
     * @var array
     */
    protected $mime = [];

    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mimetype)
    {
        $this->mime = $mimetype;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_array($this->mime)) {
            $mimeDetector = new MimeDetector();
            try {
                $mimeDetector->setFile($value);
            } catch (MimeDetectorException $e) {
                $this->fail(trans('validation.check_mimetype_file', ['values' => 'Error encountered.']));
            }
            if (in_array($mimeDetector->getMimeType(), $this->mime)) {
                return true;
            } else {
                $this->fail(trans('validation.check_mimetype_file', ['values' => join(', ', $this->mime)]));
            };
        } else {
            $this->fail(trans('validation.check_mimetype_file', ['values' => '']));
        }
    }

    protected function fail($message)
    {
        $this->message = $message;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
