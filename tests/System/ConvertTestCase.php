<?php

use \System\Convert as Convert;
use \System\DbNull as DbNull;
use \System\TypeCode as TypeCode;

/**
 * @group core
*/
class ConvertTestCase extends PHPUnit_Framework_TestCase {

    
    /**
     * @test
     * @expectedException \System\ArgumentOutOfRangeException
     */
    public function FromBase64CharArray_ThrowsExceptionWhenOffSetIsLessThanZero() {
        
        # Arrange:
        $offset = -1;
        
        # Act:
        Convert::fromBase64CharArray(array(), $offset);
    }

    /**
     * @test
     * @expectedException \System\ArgumentOutOfRangeException
     */
    public function FromBase64CharArray_ThrowsExceptionWhenLengthIsLessThanZero() {
        
        # Arrange:
        $length = -1;
    
        # Act:
        Convert::fromBase64CharArray(array(), 0, $length);
    }

    /**
     * @test
     * @expectedException \System\ArgumentOutOfRangeException
     */
    public function FromBase64CharArray_ThrowsExceptionWhenOffsetPlusLengthIsGreaterThanInArray() {
        
        # Arrange:
        $inArray = array('a', 'G');
        $offset = 1;
        $length = 2;
    
        # Act:
        Convert::fromBase64CharArray($inArray, $offset, $length);
    }

    /**
     * @test
     * @expectedException \System\FormatException
     */
    public function FromBase64CharArray_ThrowsExceptionWhenInvalidFormat() {
        
        # Arrange:
        $inArray = array('a', 'G', 'V', 's', 
                         'b', 'G', '8', 'g', 
                         'd', '2', '9', 'y',
                         'b', 'G', 'Q', '!',
                         '@', '#', 'A', 'F');
        # Act:
        $result = Convert::fromBase64CharArray($inArray);
    }

    /**
     * @test
     */
    public function FromBase64CharArray_CanConvertCharArrayToByteArray() {
        
        # Arrange:
        $inArray = array('a', 'G', 'V', 's', 
                         'b', 'G', '8', 'g', 
                         'd', '2', '9', 'y',
                         'b', 'G', 'Q');
        # Act:
        $result = Convert::fromBase64CharArray($inArray);
    
        # Assert:
        $this->assertEquals('01101000', $result[0]);
    }

    /**
     * @test
     */
    public function FromBase64CharArray_ShouldConvertPartOfCharArray() {
        
        # Arrange:
        $inArray = array('a', 'G', 'V', 's', 
                         'b', 'G', '8', 'g', 
                         'd', '2', '9', 'y',
                         'b', 'G', 'Q');
        $offset = 2;
    
        # Act:
        $result = Convert::fromBase64CharArray($inArray, $offset);

        # Assert:
        $this->assertEquals("01010110", $result[0]);
    }

    /**
     * @test
     */
    public function FromBase64CharArray_ShouldConvertFixedLength() {
        
        # Arrange:
        $inArray = array('a', 'G', 'V', 's', 
                         'b', 'G', '8', 'g', 
                         'd', '2', '9', 'y',
                         'b', 'G', 'Q');
        $offset = 0;
        $length = 4;
    
        # Act:
        $result = Convert::fromBase64CharArray($inArray, $offset, $length);
    
        # Assert:
        $this->assertEquals("01101000", $result[0]);
    }


    /**
     * @test
     * @expectedException \System\ArgumentNullException
     */
    public function FromBase64String_ThrowsExceptionWhenParameterIsNull() {

        # Arrange:
        # Act:
        Convert::fromBase64String(null);
    }

    /**
     * @test
     * @expectedException \System\ArgumentNullException
     */
    public function FromBase64String_ThrowsExceptionWhenEmpty() {
        
        # Arrange:
        # Act:
        Convert::fromBase64String('');
    }

    /**
     * @test
     * @expectedException \System\FormatException
     */
    public function FromBase64String_ThrowsExceptionWhenHasInvalidFormat() {
        
        # Arrange:
        $s = "aGVsbG8gd29ybGQKk!@#AF";

        # Act:
        Convert::fromBase64String($s);
    }

    /**
     * @test
     */
    public function FromBase64String_ShouldGetStringFromBase64() {
        
        # Arrange:
        $s = "aGVsbG8gd29ybGQ";
        $h_binary = '01101000';

        # Act:
        $result = Convert::fromBase64String($s);

        # Assert:
        $this->assertEquals($h_binary, $result[0]);
    }

    /**
     * @test
    */
    public function GetTypeCode_GetEmptyTypeWhenIsNull() {
        
        # Arrange:
        $instance = null;
    
        # Act:
        $type_code = Convert::getTypeCode($instance);
    
        # Assert:
        $this->assertEquals(TypeCode::nullable(), $type_code);
    }

    /**
     * @test
    */
    public function GetTypeCode_GetTypeOfCode() {
        
        # Arrange:
        $instance = \System\DateTime::now();
    
        # Act:
        $type_code = Convert::getTypeCode($instance);
    
        # Assert:
        $this->assertEquals('System\\DateTime', $type_code);
    }

    /**
     * @test
    */
    public function IsDbNull_ShouldBeFalseWhenValueIsNotDbNull() {
        
        # Arrange:
        $obj = 1;
    
        # Act:
        $result = Convert::isDbNull($obj);
    
        # Assert:
        $this->assertFalse($result);
    }

    /**
     * @test
    */
    public function IsDbNull_ShouldBeTrueWhenValueIsDbNull() {
        
        # Arrange:
        $obj = DbNull::value();
    
        # Act:
        $result = Convert::isDbNull($obj);

        # Assert:
        $this->assertTrue($result);
    }


    /**
     * @test
    */
    public function ToBase64String_CanConvertBitArrayToBase64() {
        
        # Arrange:
        $inArray = array('01101000', '01101000',
                         '01101000', '01101000',
                         '01101000', '01101000');
    
        # Act:
        $base64_string = Convert::toBase64String($inArray);
    
        # Assert:
        $this->assertEquals('aGhoaGho', $base64_string);
    }
}
