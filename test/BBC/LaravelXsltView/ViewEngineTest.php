<?php

namespace BBC\LaravelXsltView;

class ViewEngineTest extends \PHPUnit_Framework_TestCase
{
    /** @var $_engine ViewEngine */
    private $_engine;
    private $_mockProcessor;
    private $_testXslFile;

    public function setUp() {
        parent::setUp();
        $this->_mockProcessor = $this->getMockBuilder('XSLTProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('importStyleSheet', 'transformToXML'))
            ->getMock();
        $this->_engine = new ViewEngine($this->_mockProcessor);
        $this->_testXslFile = __DIR__ . '/test.xsl';
    }

    /** @test */
    public function callingGetWithAFileNameButNoDataThrowsException() {
        $this->setExpectedException('\\BBC\\LaravelXsltView\\XsltViewException');
        $this->_engine->get($this->_testXslFile);
    }

    /** @test */
    public function xsltProcessorHasCorrectDocumentLoaded() {
        $this->_mockProcessor->expects($this->once())
            ->method('importStyleSheet')
            ->will(
                $this->returnCallback(
                    function(\DOMDocument $document) {
                        \PHPUnit_Framework_Assert::assertEquals(
                            'Hello World',
                            (string) simplexml_import_dom($document)
                        );
                    }
                )
            );
        $this->renderSimpleDocumentAndTemplate();
    }

    /** @test */
    public function simpleXmlNodeIsPassedToProcessorAsDomDocument() {
        $this->_mockProcessor->expects($this->once())
            ->method('transformToXML')
            ->will(
                $this->returnCallback(
                    function(\DOMDocument $document) {
                        \PHPUnit_Framework_Assert::assertInstanceOf(
                            'DOMDocument',
                            $document
                        );
                    }
                )
            );
        $this->renderSimpleDocumentAndTemplate();
    }

    /** @test */
    public function domDocumentWhichIsPassedToProcessorIsCorrectRepresentation() {
        $this->_mockProcessor->expects($this->once())
            ->method('transformToXML')
            ->will(
                $this->returnCallback(
                    function(\DOMDocument $document) {
                        \PHPUnit_Framework_Assert::assertEquals(
                            'body',
                            $document->documentElement->tagName
                        );
                    }
                )
            );

        $this->renderSimpleDocumentAndTemplate();
    }

    /** @test */
    public function transformedXmlIsReturned() {
        $sentinel = 'sentinel';
        $this->_mockProcessor->expects($this->once())
            ->method('transformToXML')
            ->will($this->returnValue($sentinel));
        $this->assertEquals($this->renderSimpleDocumentAndTemplate(), $sentinel);
    }

    /**
     * @return string
     */
    private function renderSimpleDocumentAndTemplate() {
        return $this->_engine->get(
            $this->_testXslFile, array('document' => simplexml_load_string('<body/>'))
        );
    }
}