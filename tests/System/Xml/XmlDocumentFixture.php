<?php

use \System\Xml\XmlCDataSection as XmlCDataSection;
use \System\Xml\XmlComment as XmlComment;
use \System\Xml\XmlDocument as XmlDocument;
use \System\Xml\XmlDocumentFragment as XmlDocumentFragment;
use \System\Xml\XmlDeclaration as XmlDeclaration;
use \System\Xml\XmlEntityReference as XmlEntityReference;
use \System\Xml\XmlException as XmlException;
use \System\Xml\XmlSignificantWhitespace as XmlSignificantWhitespace;
use \System\Xml\XmlWhitespace as XmlWhitespace;

use \System\IO\FileStream as FileStream;
use \System\IO\StreamReader as StreamReader;

class XmlDocumentFixture extends PHPUnit_Framework_TestCase 
{

    private $xml = array(
        'well-formed' => '<?xml version="1.0"?><books xmlns:b="http://www.books.com"><book ISBN="1-861001-57-5"><title><![CDATA[Pride And Prejudice]]></title><price>19</price></book></books>',
        'bad-formed'  => "<?xml version='1.0'?><books><book></books><book>"
    );

    /**
     * @test
    */
    public function CreateAttribute_CanCreateAttributeByPrefix() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<book b:id=""/>';

        # Act:
        $element = $doc->createElement('book');
        $doc->appendChild($element);
        $attr = $doc->createAttribute('id', 'http://www.books.com', 'b');
        $element->setAttributeNode($attr);

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
    }

    /**
     * @test
    */
    public function CreateAttribute_CanCreateAttributeByName() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<book id=""/>';

        # Act:
        $element = $doc->createElement('book');
        $doc->appendChild($element);
        $attr = $doc->createAttribute("id");
        $element->setAttributeNode($attr);

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
    }

    /**
     * @test
    */
    public function CreateCDataSection_CanCDataSection() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<book><![CDATA[text]]></book>';

        # Act:
        $element = $doc->createElement('book');
        $doc->appendChild($element);
        $cdata = $doc->createCDataSection('text');
        $element->appendChild($cdata);

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
        $this->assertTrue($cdata instanceOf XmlCDataSection);
    }

    /**
     * @test
    */
    public function CreateElement_CanCreateComment() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<book><!--Any comment--></book>';

        # Act:
        $element = $doc->createElement('book');
        $comment = $doc->createComment('Any comment');
        $element->appendChild($comment);

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
        $this->assertTrue($comment instanceOf XmlComment);
    }

    /**
     * @test
    */
    public function CreateDocumentFragment_CanCreateDocumentFragment() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<?xml version="1.0"?><book/>';

        # Act:
        $frag = $doc->createDocumentFragment();
        $frag->appendChild($doc->createElement('book'));
        $doc->appendChild($frag);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
        $this->assertTrue($frag instanceOf XmlDocumentFragment);
    }

    /**
     * @test
    */
    public function CreateDocumentType_CanCreateDocumentType() 
    {
        $this->markTestIncomplete('php not support this operation');
    }

    /**
     * @test
    */
    public function CreateEntityReference_CanCreateEntityReference() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml('<!DOCTYPE book [<!ENTITY h "hardcover">]><book genre="novel" ISBN="1-861001-57-5"><title>Pride And Prejudice</title><misc/></book>');
        $expected = '<?xml version="1.0"?><!DOCTYPE book [<!ENTITY h "hardcover">]><book genre="novel" ISBN="1-861001-57-5"><title>Pride And Prejudice</title><misc>&h;</misc></book>';

        # Act:
        $entity = $doc->createEntityReference('h');
        $documentElement = $doc->documentElement()->lastChild()->appendChild($entity);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
        $this->assertTrue($entity instanceOf XmlEntityReference);
    }

    /**
     * @test
    */
    public function CreateElement_CanCreateElementByPrefix() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<b:book xmlns:b="http://www.books.com"/>';

        # Act:
        $element = $doc->createElement('book', 'http://www.books.com', 'b');

        $this->assertEquals($expected, $element->outerXml());
    }

    /**
     * @test
    */
    public function CreateElement_CanCreateElementByName() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<book/>';

        # Act:
        $element = $doc->createElement('book');

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
    }

    /**
     * @test
    */
    public function CreateProcessingInstruction_CanCreateProcessingInstruction() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<?xml version="1.0"?><?xml-stylesheet type="text/xsl" href="book.xsl"?>';

        # Act:
        $instruction = $doc->createProcessingInstruction("xml-stylesheet", 'type="text/xsl" href="book.xsl"');
        $doc->appendChild($instruction);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
    }

    /**
     * @test
    */
    public function CreateSignificantWhitespace_ThrowsExceptionWhenDataNotContainsSpaceCharacters() 
    {
        # Arrange:
        $this->setExpectedException('\System\ArgumentException');
        $doc = new XmlDocument;

        # Act:
        $whitespace = $doc->createSignificantWhitespace("asdf");
    }

    /**
     * @test
    */
    public function CreateSignificantWhitespace_CanCreateSignificantWhitespace() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->preserveWhitespace(true);
        $expected = "\t";

        # Act:
        $whitespace = $doc->createSignificantWhitespace("\t");
        $doc->appendChild($whitespace);

        # Assert:
        $this->assertEquals($expected, $whitespace->value());
        $this->assertTrue($whitespace instanceOf XmlSignificantWhitespace);
    }

    /**
     * @test
    */
    public function CreateTextNode_CanCreateTextNode() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = "asdf";

        # Act:
        $textNode = $doc->createTextNode("asdf");
        $doc->appendChild($textNode);

        # Assert:
        $this->assertEquals($expected, $textNode->outerXml());
    }

    /**
     * @test
    */
    public function CreateWhitespace_CanCreateWhitespace() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = "\t";

        # Act:
        $whitespace = $doc->createWhitespace("\t");
        $doc->appendChild($whitespace);

        # Assert:
        $this->assertEquals($expected, $whitespace->value());
        $this->assertTrue($whitespace instanceOf XmlWhitespace);
    }

    /**
     * @test
    */
    public function CreateXmlDeclaration_CanCreateXmlDeclaration() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $expected = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

        # Act:
        $declaration = $doc->createXmlDeclaration("1.0", "UTF-8", "yes");
        $doc->appendChild($declaration);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
        $this->assertTrue($declaration instanceOf XmlDeclaration);
    }


    /**
     * @test
    */
    public function GetElementById_ReturnNullWhenIdDoesNotExists() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml($this->xml['well-formed']);

        # Act:
        $element = $doc->getElementById("new-book");

        # Assert:
        $this->assertEquals(null, $element);
    }

    /**
     * @test
    */
    public function GetElementById_CanGetElementById() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml('<?xml version="1.0"?><books><book id="new-book"/><book id="old-book"/></books>');
        $expected = '<book id="new-book"/>';

        # Act:
        $element = $doc->getElementById("new-book");

        # Assert:
        $this->assertEquals($expected, $element->outerXml());
    }

    /**
     * @test
    */
    public function GetElementsByTagName_DontFindAnyTag() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml($this->xml['well-formed']);

        # Act:
        $elements = $doc->getElementsByTagName("lp");

        # Assert:
        $this->assertEquals(0, $elements->count());
    }

    /**
     * @test
    */
    public function GetElementsByTagName_CanAllElements() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml($this->xml['well-formed']);

        # Act:
        $elements = $doc->getElementsByTagName("*");

        # Assert:
        $this->assertEquals(4, $elements->count());
    }

    /**
     * @test
    */
    public function GetElementsByTagName_CanGetElementsByTagName() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml($this->xml['well-formed']);

        # Act:
        $elements = $doc->getElementsByTagName("book");

        # Assert:
        $this->assertEquals(1, $elements->count());
    }

    /**
     * @test
    */
    public function ImportNode_CanImportNodeInDeep() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml("<bookstore><book genre='novel' ISBN='1-861001-57-5'><title>Pride And Prejudice</title></book></bookstore>");

        $doc2 = new XmlDocument;
        $doc2->loadXml("<bookstore><book genre='thriller' ISBN='1-588142-33-7'><title>Black Tower</title></book></bookstore>");

        $expected = '<?xml version="1.0"?><bookstore><book genre="novel" ISBN="1-861001-57-5"><title>Pride And Prejudice</title></book><book genre="thriller" ISBN="1-588142-33-7"><title>Black Tower</title></book></bookstore>';

        # Act:
        $newBook = $doc->importNode($doc2->documentElement()->lastChild(), true);
        $doc->documentElement()->appendChild($newBook);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
    }

    /*/**
     * @test
    */
    public function ImportNode_CanImportNodeWithoutDeep() 
    {
        # Arrange:
        $doc = new XmlDocument;
        $doc->loadXml("<bookstore><book genre='novel' ISBN='1-861001-57-5'><title>Pride And Prejudice</title></book></bookstore>");

        $doc2 = new XmlDocument;
        $doc2->loadXml("<bookstore><book genre='thriller' ISBN='1-588142-33-7'><title>Black Tower</title></book></bookstore>");

        $expected = '<?xml version="1.0"?><bookstore><book genre="novel" ISBN="1-861001-57-5"><title>Pride And Prejudice</title></book><book genre="thriller" ISBN="1-588142-33-7"/></bookstore>';

        # Act:
        $newBook = $doc->importNode($doc2->documentElement()->lastChild(), false);
        $doc->documentElement()->appendChild($newBook);

        # Assert:
        $this->assertEquals($expected, $doc->outerXml());
    }

    /**
     * @test
    */
    public function Load_CanLoadXmlFromStream() 
    {
        # Arrange:
        $fileName = dirname(__FILE__).'/../../resources/system.xml.document-well-formed.xml';
        $stream = new FileStream($fileName);
        $xml = new XmlDocument;
        
        # Act:
        $xml->load($stream);

        # Assert:
        $this->assertEquals(5, $xml->documentElement()->childNodeS()->count());
    }

    /**
     * @test
    */
    public function Load_CanLoadXmlFromString() 
    {
        # Arrange:
        $url = "https://raw.github.com/fakeezz/dotnetonphp/xml/tests/resources/system.xml.document-well-formed.xml";
        $xml = new XmlDocument;

        # Act:
        $xml->load($url);
        
        # Assert:
        $this->assertEquals(5, $xml->documentElement()->childNodeS()->count());
    }

    /**
     * @test
    */
    public function Load_FromTextReaderCanLoadXml() 
    {
        # Arrange:
        $fileName = dirname(__FILE__).'/../../resources/system.xml.document-well-formed.xml';
        $reader = new StreamReader($fileName);
        $xml = new XmlDocument;
        
        # Act:
        $xml->load($reader);
        
        # Assert:
        $this->assertEquals(5, $xml->documentElement()->childNodeS()->count());
    }

    /**
     * @test
    */
    public function Load_FromXmlReaderCanLoadXml() 
    {
        $this->markTestIncomplete("Implement XmlReader");
    }

    /**
     * @test
     * @expectedException \System\Xml\Exception
    */
    public function LoadXml_ThrowsExceptionWhenXmlWasNotWellFormed() 
    {
        # Arrange:
        $xml = new XmlDocument();
        
        # Act:
        $xml->loadXml($this->xml['bad-formed']);
    }

    /**
     * @test
    */
    public function LoadXml_CanLoadXmlFromString() 
    {
        # Arrange:
        $doc = new XmlDocument();
        
        # Act:
        $doc->loadXml($this->xml['well-formed']);
        
        # Assert:
        $this->assertEquals(1, $doc->childNodes()->count());
    }

    /**
     * @test
    */
    public function ReadNode_CanReadNodeFromXmlReader() 
    {
        $this->markTestIncomplete('Implement XmlReader');
    }

    /**
     * @test
    */
    public function WriteContentTo_CanWriteContent() 
    {
        $this->markTestIncomplete('Implement XmlWriter');
    }

    /**
     * @test
    */
    public function WriteTo_CanWriteContent() 
    {
        $this->markTestIncomplete('Implement XmlWriter');
    }
}
