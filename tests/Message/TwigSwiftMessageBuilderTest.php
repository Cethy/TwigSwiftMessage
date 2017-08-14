<?php

namespace Cethyworks\TwigSwiftMessage\Tests\Message;

use Cethyworks\TwigSwiftMessage\Message\TwigSwiftMessageBuilder;
use PHPUnit\Framework\TestCase;
use Swift_Message;

class TwigSwiftMessageBuilderTest extends TestCase
{
    public function dataTestBuildMessage()
    {
        return [
            'empty template' => [
                'i_am_empty.html.twig',
                [],
                (new Swift_Message(''))
                    ->setBody("", 'text/plain')
            ],
            'partial template (html body)' => [
                'partial_html.html.twig',
                ['foo' => 'bar'],
                (new Swift_Message())
                    ->setBody('bar<br>bar', 'text/html')
            ],
            'partial template (txt body)' => [
                'partial_txt.html.twig',
                ['foo' => 'bar'],
                (new Swift_Message())
                    ->setBody("bar\nbar", 'text/plain')
            ],
            'partial template (subject+html)' => [
                'partial_subject_html.html.twig',
                ['foo' => 'bar'],
                (new Swift_Message('bar subject'))
                    ->setBody('bar<br>bar', 'text/html')
            ],
            'full template (subject+html+txt)' => [
                'full.html.twig',
                ['foo' => 'bar'],
                (new Swift_Message('bar subject'))
                    ->setBody('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body>
<p>bar<br></p>
<p class="baz" style="color: red;">bar</p>
</body></html>', 'text/html')
                    ->addPart("bar\nbar", 'text/plain')
            ],
        ];
    }

    /**
     * @dataProvider dataTestBuildMessage
     */
    public function testBuildMessage($templateName, array $templateParameters, Swift_Message $expectedMessage)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../data/');
        $twig   = new \Twig_Environment($loader);

        $messageBuilder = new TwigSwiftMessageBuilder($twig);

        $message = $messageBuilder->buildMessage($templateName, $templateParameters);

        $this->assertInstanceOf(Swift_Message::class, $message);

        $this->assertEquals(
            $this->getCleanedMessageString($expectedMessage),
            $this->getCleanedMessageString($message)
        );
    }

    protected function getCleanedMessageString(Swift_Message $message)
    {
        /*
        MessageTemplate-ID: <882d18b1a3060a9c7ff11b45c8619a6e@swift.generated>
        Date: Fri, 21 Jul 2017 19:01:33 +0200
        Subject: bar subject
        From:
        MIME-Version: 1.0
        Content-Type: multipart/alternative;
         boundary="_=_swift_v4_1500657328_3f5a45f103adacf975c294a3d4f2fb63_=_"


        --_=_swift_v4_1500657328_3f5a45f103adacf975c294a3d4f2fb63_=_
        Content-Type: text/plain; charset=utf-8
        Content-Transfer-Encoding: quoted-printable

        bar
        bar

        --_=_swift_v4_1500657328_3f5a45f103adacf975c294a3d4f2fb63_=_
        Content-Type: text/html; charset=utf-8
        Content-Transfer-Encoding: quoted-printable

        bar<br>bar

        --_=_swift_v4_1500657328_3f5a45f103adacf975c294a3d4f2fb63_=_--
        */

        $text = $message->toString();
        // remove first line (MessageTemplate-ID: <882d18b1a3060a9c7ff11b45c8619a6e@swift.generated>)
        $text = substr( $text, strpos($text, "\n")+1 );
        // remove second line (Date: Fri, 21 Jul 2017 19:01:33 +0200)
        $text = substr( $text, strpos($text, "\n")+1 );

        // remove all lines containing boundaries
        $text = preg_replace('/(\ boundary\=.+)\n/', '', $text);
        $text = preg_replace('/(\-\-\_\=\_.+)\n/', '', $text);

        /*
        Subject: bar subject
        From:
        MIME-Version: 1.0
        Content-Type: multipart/alternative;


        Content-Type: text/plain; charset=utf-8
        Content-Transfer-Encoding: quoted-printable

        bar
        bar

        Content-Type: text/html; charset=utf-8
        Content-Transfer-Encoding: quoted-printable

        bar<br>bar
        */

        return $text;
    }
}
