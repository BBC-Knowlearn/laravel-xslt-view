<?php

namespace BBC\LaravelXsltView;

class ViewEngine implements \Illuminate\View\Engines\EngineInterface
{
    /** @var \XSLTProcessor  */
    private $_processor;

    public function __construct(\XSLTProcessor $processor) {
        $this->_processor = $processor;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array  $data
     *
     * @throws XsltViewException
     * @return string
     */
    public function get($path, array $data = array()) {
        if (!isset($data['document'])) {
            throw new XsltViewException("Document to render was not passed in");
        }
        $this->_processor->importStylesheet($this->loadStyleSheet($path));
        return $this->_processor->transformToXML(
            $this->convertSimpleXmlToDomDocument($data['document'])
        );
    }

    private function loadStyleSheet($path) {
        $xsl = new \DOMDocument();
        $xsl->load($path);
        return $xsl;
    }

    private function convertSimpleXmlToDomDocument($simpleXmlElement) {
        $document = new \DOMDocument();
        $document->appendChild(
            $document->importNode(dom_import_simplexml($simpleXmlElement), true)
        );
        return $document;
    }
}