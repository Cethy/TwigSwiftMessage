Cethyworks/TwigSwiftMessage
===

Provides a Swift_Message builder using twig.

[![CircleCI](https://circleci.com/gh/Cethy/TwigSwiftMessage/tree/master.svg?style=shield)](https://circleci.com/gh/Cethy/TwigSwiftMessage/tree/master)

## Install

1\. Composer require

    $ composer require cethyworks/twig-swift-message

## How to use
1\. Create your twig template using the 3 blocks `subject`, `body_html` and/or `body_txt` (**all** blocks are optional).
 
    {% block subject %}subject{% endblock subject %}
    
    {% block body_html %}
        {{ foo }}<br>
        bar
    {% endblock body_html %}
    
    {% block body_txt %}
        {{ foo }}
        bar
    {% endblock body_txt %}
 
2\. Call TwigSwiftMessageBuilder::buildMessage()

    /** @var Swift_Message $swiftMessage */
    $swiftMessage = $messageBuilder->buildMessage($templateName, $templateParameters);

3\. Add your recipient(s). And sent it !
