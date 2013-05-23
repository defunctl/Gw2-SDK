<?php

namespace vesu\SDK\Gw2;

/**
 * Gw2Exception for Guild Wars 2 API SDK for PHP
 * 
 * 
 * @author Justin Frydman
 * @license https://github.com/defunctl/Gw2-SDK/blob/master/LICENSE.md MIT
 */
class Gw2Exception extends \Exception
{
    /** @var string */
    protected $message = 'Unknown exception';

    /** @var integer */
    protected $code;

    /** @var \vesu\SDK\TwitchTV\TwitchException */
    protected $previous;

    public function __construct($message = null, $code = 0, \vesu\SDK\TwitchTV\TwitchException $previous = null)
    {
        $this->code = $code;
        if (!is_null($message)) {
            $this->message = $message;
        }
        $this->previous = $previous;

        parent::__construct($this->message, $this->code, $this->previous);
    }

    /**
     * Formatted string for display
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . ': [' . $this->code . ']: ' . $this->message;
    }
}