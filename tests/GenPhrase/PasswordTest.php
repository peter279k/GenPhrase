<?php

class GenPhrase_PasswordTest extends PHPUnit_Framework_TestCase
{
    public $entropyLowBits = 25;
    
    public $entropyHighBits = 121;
    
    public $testWords = array('test','test','test','test','test',
                              'test','test','test','test','test',
                              'test','test','test','test','test',
                              'test','test','test','test','test');
    
    public function testConstructWithoutArguments()
    {
        $obj = new GenPhrase\Password();
    }
    
    public function testGetDefaultSeparators()
    {
        $obj = new GenPhrase\Password();
        $separators = $obj->getSeparators();
        
        $this->assertEquals('-_!$&*+=23456789', $separators);
    }
    
    public function testCanSetSeparators()
    {
        $newSeparators = '1234';
        $obj = new GenPhrase\Password();
        $obj->setSeparators($newSeparators);
        
        $this->assertEquals($newSeparators, $obj->getSeparators());
    }
    
    public function testGetDefaultEncoding()
    {
        $obj = new GenPhrase\Password();
        
        $this->assertEquals('utf-8', $obj->getEncoding());
    }
    
    public function testCanSetEncoding()
    {
        $newEncoding = 'iso-8859-1';
        $obj = new GenPhrase\Password();
        $obj->setEncoding($newEncoding);
        
        $this->assertEquals($newEncoding, $obj->getEncoding());
    }
    
    public function testGetDefaultConstructorDependencies()
    {
        $obj = new GenPhrase\Password();
        
        $this->assertTrue($obj->getWordlistHandler() instanceof GenPhrase\WordlistHandler\Filesystem);
        $this->assertTrue($obj->getWordmodifier() instanceof GenPhrase\WordModifier\MbCapitalizeFirst);
        $this->assertTrue($obj->getRandomProvider() instanceof GenPhrase\Random\Random);
    }
    
    public function testGenerateReturnsNonEmptyString()
    {
        $obj = new GenPhrase\Password();
        $password = $obj->generate(30);
        
        $this->assertTrue(is_string($password));
        $this->assertTrue(strlen($password) > 0);
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testGenerateWithLowBitsThrowsException()
    {
        $obj = new GenPhrase\Password();
        $password = $obj->generate($this->entropyLowBits);
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testGenerateWithHighBitsThrowsException()
    {
        $obj = new GenPhrase\Password();
        $password = $obj->generate($this->entropyHighBits);
    }
    
    public function testGenerateReturnsExpectedStrings()
    {
        $wordlistHandler = $this->getMock('GenPhrase\\WordlistHandler\\Filesystem');
        $wordlistHandler->expects($this->any())
                ->method('getWordsAsArray')
                ->will($this->returnValue($this->testWords));
        
        $wordModifier = $this->getMock('GenPhrase\\WordModifier\\MbCapitalizeFirst');
        $wordModifier->expects($this->any())
                ->method('modify')
                ->will($this->returnValue('test'));
        $wordModifier->expects($this->any())
                ->method('getWordCountMultipier')
                ->will($this->returnValue(1));
        
        $randomProvider = $this->getMock('GenPhrase\\Random\\Random');
        $randomProvider->expects($this->any())
                ->method('getElement')
                ->will($this->returnValue(0));
        
        $obj = new GenPhrase\Password($wordlistHandler, $wordModifier, $randomProvider);
        $obj->disableSeparators(true);
        
        $password = $obj->generate(26);
        $this->assertEquals('testtesttesttesttesttesttest', $password);
        
        $password = $obj->generate(36);
        $this->assertEquals('testtesttesttesttesttesttesttesttest', $password);
        
        $password = $obj->generate(50);
        $this->assertEquals('testtesttesttesttesttesttesttesttesttesttesttest', $password);
    }
    
    public function testMakesSenseToUseSeparators()
    {
        $separatorTestData = array();
        $separatorTestData[] = array(26, 13, 4, false);
        $separatorTestData[] = array(27, 13, 4, true);
        $separatorTestData[] = array(28, 13, 4, true);
        $separatorTestData[] = array(29, 13, 4, true);
        $separatorTestData[] = array(30, 13, 4, true);
        $separatorTestData[] = array(31, 13, 4, false);
        $separatorTestData[] = array(32, 13, 4, false);
        $separatorTestData[] = array(33, 13, 4, false);
        $separatorTestData[] = array(34, 13, 4, false);
        $separatorTestData[] = array(35, 13, 4, false);
        $separatorTestData[] = array(36, 13, 4, false);
        $separatorTestData[] = array(37, 13, 4, false);
        
        $obj = new GenPhrase\Password();
        
        foreach ($separatorTestData as $data)
        {
            $bits = $data[0];
            $wordBits = $data[1];
            $separatorBits = $data[2];
            $shouldUse = $data[3];
            
            $this->assertEquals($shouldUse, $obj->makesSenseToUseSeparators($bits, $wordBits, $separatorBits), 'Failed for bits:' . $bits);
        }
    }
}