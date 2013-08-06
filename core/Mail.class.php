<?php
if(!defined('xDEC')) exit;
/**
 * Class Mail
 */
class Mail {
    /**
     *
     */
    function __construct()
    {
        $this->eol = '\r\n';
    }

    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    var $eol, $to, $message, $headers, $params;

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param $from
     * @param null $cc
     * @param null $bcc
     */
    function html_mail($to, $subject, $message, $from, $cc = null, $bcc = null){
        $this->to = $to;
        $this->message = $message;
        $this->headers = "From: $from".$this->eol.
                         "Mime-Version: 1.0".$this->eol.
                         "Content-type: text/html; charset=iso-8859-1Content-type: text/html; charset=iso-8859-1".$this->eol.
                         "To: $to".$this->eol.
                         (($cc)?"Cc: $cc".$this->eol:'').
                         (($bcc)?"Bcc: $bcc".$this->eol:'');
        mail($to, $subject, $message, $this->headers);
    }

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param $from
     * @param null $cc
     * @param null $bcc
     */
    function mail($to, $subject, $message, $from, $cc = null, $bcc = null){
        $this->to = $to;
        $this->message = $message;
        $this->headers = "From: $from".$this->eol.
            "To: $to".$this->eol.
            (($cc)?"Cc: $cc".$this->eol:'').
            (($bcc)?"Bcc: $bcc".$this->eol:'');
        mail($to, $subject, $message, $this->headers);
    }
}