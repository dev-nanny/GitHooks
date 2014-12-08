<?php

namespace DevNanny\Git;

/**
 * @coversDefaultClass DevNanny\Git\CommitDiff
 * @covers ::<!public>
 * @covers ::__construct
 * @covers ::setRepository
 * @covers ::getRepository
 */
final class CommitDiffTest extends \PHPUnit_Framework_TestCase
{
    ////////////////////////////////// FIXTURES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /** @var CommitDiff */
    private $commitDiff;
    /** @var RepositoryContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRepository;

    protected function setUp()
    {
        $this->mockRepository = $this->getMockRepositoryContainerInterface();
        $this->commitDiff = new CommitDiff($this->mockRepository);
    }

    /////////////////////////////////// TESTS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @test
     */
    final public function commitDiffShouldRememberTheRepositoryItWasGivenWhenInstantiated()
    {
        $container = $this->commitDiff;

        $expected = $this->mockRepository;
        $actual = $container->getRepository();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     *
     * @covers ::getFileList
     */
    final public function commitDiffShouldAlwaysReturnFileListWhenAskedToGetFileList()
    {
        $container = $this->commitDiff;

        $fileList = $container->getFileList();

        $this->assertInternalType('array', $fileList);

        return $fileList;
    }

    /**
     * @test
     *
     * @depends commitDiffShouldAlwaysReturnFileListWhenAskedToGetFileList
     *
     * @param $fileList
     */
    final public function commitDiffShouldReturnAnEmptyFileListWhenNoFilesChanged(array $fileList)
    {
        $this->assertEmpty($fileList);
    }

    /**
     * @test
     *
     * @covers ::getFileList
     */
    final public function commitDiffShouldReturnPopulatedFileListWhenFilesChanged()
    {
        $container = $this->commitDiff;

        $mockList = array(
            'A', 'src/Foo.php',
            'C', 'src/Foo/Bar/Bar.txt',
            'X', 'baz'
        );

        $expected = array(
            'src/Foo.php',
            'src/Foo/Bar/Bar.txt',
            'baz'
        );

        $rawOutput = implode("\x00", $mockList) . "\x00";

        $this->mockRepository->expects($this->exactly(1))
            ->method('getCommittedFiles')
            ->willReturn($rawOutput)
        ;

        $actual = $container->getFileList();

        $this->assertEquals($expected, $actual);
    }

    ////////////////////////////// MOCKS AND STUBS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return RepositoryContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRepositoryContainerInterface()
    {
        return $this->getMockBuilder(RepositoryContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /////////////////////////////// DATAPROVIDERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
}

/*EOF*/