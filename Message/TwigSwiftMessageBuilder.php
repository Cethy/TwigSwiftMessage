<?php

namespace Cethyworks\TwigSwiftMessage\Message;

use Twig_Environment;
use Twig_TemplateWrapper;
use Swift_Message;

class TwigSwiftMessageBuilder
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * TwigSwiftMessageBuilder constructor.
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param $templateName
     * @param array $templateParameters
     *
     * @return Swift_Message
     */
    public function buildMessage($templateName, array $templateParameters = [])
    {
        $template = $this->twig->load($templateName);

        $subject  = trim($this->getBlock('subject', $template, $templateParameters));
        $bodyHtml = trim($this->getBlock('body_html', $template, $templateParameters));
        $bodyTxt  = trim($this->getBlock('body_txt', $template, $templateParameters));

        $message = new Swift_Message($subject);

        if(strlen($bodyHtml)) {
            $message->setBody($bodyHtml, 'text/html');

            if(strlen($bodyTxt)) {
                $message->addPart($bodyTxt, 'text/plain');
            }
        }
        else {
            $message->setBody($bodyTxt, 'text/plain');
        }

        return $message;
    }

    /**
     * @param string $blockName
     * @param Twig_TemplateWrapper $template
     * @param array $parameters
     * @return string
     */
    protected function getBlock($blockName, Twig_TemplateWrapper $template, array $parameters = [])
    {
        return $template->hasBlock($blockName)
            ? $template->renderBlock($blockName, $parameters)
            : '';
    }
}
