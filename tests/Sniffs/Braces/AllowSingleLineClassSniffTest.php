<?php

namespace Sandersj16\SweetScent\Tests\Sniffs\Braces;

use Sandersj16\SweetScent\Tests\TestCase;

class AllowSingleLineClassSniffTest extends TestCase
{
    public function testNoErrors(): void
    {
        $report = $this->checkFile(__DIR__ . '/data/classSingleLineDefinitionNoErrors.php');
        self::assertNoSniffErrorInFile($report);
    }
}
