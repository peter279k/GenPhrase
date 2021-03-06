<?php

class GenPhrase_WordModifier_MbToggleCaseFirstTest extends \PHPUnit\Framework\TestCase
{
    public function testModifyCapitalizes()
    {
        $word = 'äbcd';
        $expected = 'Äbcd';

        $randomProvider = $this->createMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->once())
            ->method('getElement')
            ->will($this->returnValue(0));
        
        $obj = new GenPhrase\WordModifier\MbToggleCaseFirst($randomProvider);
        $test = $obj->modify($word);
        
        $this->assertEquals($expected, $test);
    }

    public function testModifyLowers()
    {
        $word = 'Äbcd';
        $expected = 'äbcd';

        $randomProvider = $this->createMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->once())
            ->method('getElement')
            ->will($this->returnValue(0));

        $obj = new GenPhrase\WordModifier\MbToggleCaseFirst($randomProvider);
        $test = $obj->modify($word);

        $this->assertEquals($expected, $test);
    }
}