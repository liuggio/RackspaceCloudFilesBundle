<?php

namespace Liuggio\RackspaceCloudFilesBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liuggio\RackspaceCloudFilesBundle\FileTypeGuesser;

/**
 * @author liuggio
 */
class FileTypeGuesserTest extends WebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testGuessByFileName($input, $outputToAssert)
    {
        $output = FileTypeGuesser::guessByFileName($input);
        $this->assertTrue(false !== $output);
        $this->assertEquals($output, $outputToAssert);
    }

    public function provider()
    {
        return array(
            array('V:\\filename\\test.css', 'text/css'),
            array('text.txt', 'text/plain'),
            array('http:\\www.google.com\\a.html', 'text/html'),
        );
    }


}
