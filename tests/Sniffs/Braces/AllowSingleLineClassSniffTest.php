<?php

namespace SweetScent\Tests\Sniffs\Braces;

use SweetScent\Tests\TestCase;
use SweetScent\Sniffs\Braces\AllowSingleLineClassSniff;

class AllowSingleLineClassSniffTest extends TestCase
{
    public function testNoErrors(): void
    {
        $report = $this->checkFile(__DIR__ . '/data/classSingleLineDefinitionNoErrors.php');
        self::assertNoSniffErrorInFile($report);
    }

    public function testErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/data/classSingleLineDefinitionWithErrors.php');

        self::assertSame(16, $report->getErrorCount());

        self::assertSniffError($report, 9, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 11, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 13, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 15, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 31, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 35, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 39, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 43, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 48, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 51, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 55, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 59, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 66, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 69, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 72, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
        self::assertSniffError($report, 75, AllowSingleLineClassSniff::INCORRECT_CLASS_BRACKETS);
    }
}
