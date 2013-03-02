<?php

require_once dirname(__FILE__) . '/../../../src/Autoloader.php';

use \System\IO\FileStream as FileStream;
use \System\IO\FileMode as FileMode;
use \System\IO\FileAccess as FileAccess;
use \System\IO\SeekOrigin as SeekOrigin;
 
class FileStreamFixture extends PHPUnit_Framework_TestCase {

    protected $streamPath;

    public function setUp() {
        $this->streamPath = dirname(__FILE__) . "/../../resources/system.io.FileStream.txt";
    }

    public function test_Constructor_ThrowsExceptionWhenPathIsNull() {
        $this->setExpectedException("\\System\\ArgumentNullException");
        new FileStream(null);
    }

    public function test_Constructor_ThrowsExceptionWhenPathIsEmpty() {
        $this->setExpectedException("\\System\\ArgumentException");
        new FileStream("");
    }

    public function test_Constructor_ThrowsExceptionWhenFileNotFound() {
        $this->setExpectedException("\\System\\IO\\FileNotFoundException");
        new FileStream('file_not_found.txt');
    }

    public function test_CanRead_ShouldBeTrueIfFileWasOpenedInReadMode() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertTrue($fs->canRead());
    }

    public function test_CanRead_ShouldBeFalseIfFileWasOpenedToWriteOnly() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::write());
        $this->assertFalse($fs->canRead());
    }

    public function test_CanSeek_ShouldBeTrueIfStreamIsOpened() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertTrue($fs->canSeek());
    }

    public function test_CanSeek_ShouldBeFalseIfStreamIsClosed() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->close();
        $this->assertFalse($fs->canSeek());
    }

    public function test_CanTimeOut_AlwaysBeFalseBecauseFileStreamNotSupported() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertFalse($fs->canTimeout());
    }

    public function test_CanWrite_ShouldBeFalseIfFileWasOpenedInReadMode() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertFalse($fs->canWrite());
    }

    public function test_CanWrite_ShouldBeTrueIfFileWasOpenedInWriteMode() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $this->assertTrue($fs->canWrite());
    }

    public function test_Length_CanGetLengthOfStream() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertGreaterThan(0, $fs->length());
    }

    public function test_Position_ThrowsExceptionWhenValueIsLessThanZero() {
        $this->setExpectedException("\\System\\ArgumentOutOfRangeException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->position(-1);
    }

    public function test_Position_ThrowsExceptionWhenStreamIsClosed() {
        $this->setExpectedException("\\System\\IO\\IOException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->close();
        $fs->position();
    }

    public function test_Position_ShouldBeZeroWhenOpenedFile() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertEquals(0, $fs->position());
    }

    public function test_Position_ShouldChangePosition() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->position(2);
        $this->assertEquals(2, $fs->position());
    }

    public function test_Lock_ThrowsExceptionWhenFileIsClosed() {
        $this->setExpectedException("\\System\\ObjectDisposedException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->close();
        $fs->lock();
    }

    public function test_Lock_CanLockFile() {
        $this->markTestSkipped();
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->lock();
        $fs->unlock();
    }

    public function test_Read_ThrowsExceptionWhenStreamIsClosed() {
        $this->setExpectedException("\\System\\IO\\IOException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->close();
        $array = array();
        $fs->read($array, 0, 1);
    }

    public function test_Read_ThrowsExceptionWhenOffsetIsNegative() {
        $this->setExpectedException("\\System\\ArgumentException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $array = array();
        $fs->read($array, -1, 1);
    }

    public function test_Read_CanReadBuffer() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $array = array();
        $fs->read($array, 0, 4);
        $this->assertEquals(4, sizeof($array));
    }

    public function test_ReadByte_ThrowsExceptionIfStreamNotSupportRead() {
        $this->setExpectedException("\\System\\NotSupportedException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $fs->readByte();
    }

    public function test_ReadByte_ThrowsExceptionWhenStreamIsClosed() {
        $this->setExpectedException("\\System\\ObjectDisposedException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::read());
        $fs->close();
        $fs->readByte();
    }

    public function test_ReadByte_CanReadNextCharacter() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $this->assertNotNull($fs->readByte());
    }

    public function test_ReadTimeOut_ThrowsExceptionInvalidOperation() {
        $this->setExpectedException("\\System\\InvalidOperationException");
        $fs = new FileStream($this->streamPath, FileMode::append(), FileAccess::readWrite());
        $fs->readTimeout();
    }

    public function test_Seek_ThrowsExceptionWhenStreamIsClosed() {
        $this->setExpectedException("\\System\\ObjectDisposedException");
        $fs = new FileStream($this->streamPath, FileMode::append(), FileAccess::readWrite());
        $fs->close();
        $fs->seek(2);
    }

    public function test_Seek_PositionShouldBeEqualTwo() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::readWrite());
        $fs->seek(2, SeekOrigin::Begin);
        $this->assertEquals(2, $fs->position());
    }

    public function test_Seek_PositionShouldBeEqualFour() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::readWrite());
        $fs->seek(2, SeekOrigin::Begin);
        $fs->seek(2, SeekOrigin::Current);
        $this->assertEquals(4, $fs->position());
    }

    public function test_Seek_PositionShouldBeEqualFortyThree() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::readWrite());
        $fs->seek(1, SeekOrigin::End);
        $this->assertEquals(43, $fs->position());
    }

    public function test_SetLength_ThrowsExceptionWhenFileIsReadMode() {
        $this->setExpectedException("\\System\\NotSupportedException");
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->setLength(10);
    }

    public function test_SetLength_ThrowsExceptionWhenValueIsLessThanZero() {
        $this->setExpectedException("\\System\\ArgumentOutOfRangeException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $fs->setLength(-1);
    }

    public function test_SetLength_CanTruncateFileStream() {
        $this->markTestSkipped("This method should be run once time.");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $fs->setLength(2);
    }

    public function test_ToArray_ShouldReturnEmptyWhenStreamIsClosed() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $fs->close();
        $buffer = $fs->toArray();
        $this->assertEquals(array(), $buffer);
    }

    public function test_ToArray_ShouldReturnBuffer() {
        $fs = new FileStream($this->streamPath, FileMode::open(), FileAccess::read());
        $buffer = $fs->toArray();
        $this->assertNotNull($buffer);
    }

    public function test_Write_ThrowsExceptionWhenArrayIsNull() {
        $this->setExpectedException("\\System\\ArgumentNullException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $fs->write(null, 0, 10);
    }

    public function test_Write_ThrowsExceptionWhenOffsetIsInvalidRage() {
        $this->setExpectedException("\\System\\ArgumentException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $array = array('d', 'o', 't', 'n', 'e', 't', 'o', 'n', 'p', 'h', 'p');
        $fs->write($array, 55, 10);
    }

    public function test_Write_ThrowsExceptionWhenOffsetIsNegative() {
        $this->setExpectedException("\\System\\ArgumentOutOfRangeException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $array = array('d', 'o', 't', 'n', 'e', 't', 'o', 'n', 'p', 'h', 'p');
        $fs->write($array, -1, 10);
    }

    public function test_Write_CanWriteInRange() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::readWrite());
        $array = array('d', 'o', 't', 'n', 'e', 't', 'o', 'n', 'p', 'h', 'p');
        $fs->write($array, 0, 3);
        $fs->seek(0);
        $this->assertEquals('d', $fs->readByte());
        $this->assertEquals('o', $fs->readByte());
        $this->assertEquals('t', $fs->readByte());
    }

    public function test_WriteByte_ThrowsExceptionWhenFileIsOpenedInReadMode() {
        $this->setExpectedException("\\System\\NotSupportedException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::read());
        $fs->writeByte('a');
    }

    public function test_WriteByte_ThrowsExceptionWhenFileWasDisposed() {
        $this->setExpectedException("\\System\\ObjectDisposedException");
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::write());
        $fs->close();
        $fs->writeByte('a');
    }

    public function test_WriteByte_CanWriteByte() {
        $fs = new FileStream($this->streamPath, FileMode::openOrCreate(), FileAccess::readWrite());
        $fs->writeByte('a');
        $fs->seek(0);
        $this->assertEquals('a', $fs->readByte());
    }

    public function test_WriteTimeOut_ThrowsExceptionInvalidOperation() {
        $this->setExpectedException("\\System\\InvalidOperationException");
        $fs = new FileStream($this->streamPath, FileMode::append(), FileAccess::readWrite());
        $fs->writeTimeout();
    }

}
?>