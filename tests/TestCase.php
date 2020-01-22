<?php

namespace Sandersj16\SweetScent\Tests;

use Exception;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use ReflectionClass;
use function array_map;
use function count;
use function implode;
use function in_array;
use function preg_replace;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use const PHP_EOL;

/**
 * @codeCoverageIgnore
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $filePath
     * @param (string|int|bool|(string|int|bool)[])[] $sniffProperties
     * @param string[] $codesToCheck
     * @return File
     */
    protected static function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = []): File
    {
        $codeSniffer = new Runner();
        $codeSniffer->config = new Config([
            '-s',
        ]);
        $codeSniffer->init();

        if (count($sniffProperties) > 0) {
            $codeSniffer->ruleset->ruleset[static::getSniffName()]['properties'] = $sniffProperties;
        }

        $sniffClassName = static::getSniffClassName();

        $codeSniffer->ruleset->sniffs = [$sniffClassName => new $sniffClassName()];

        if (count($codesToCheck) > 0) {
            foreach (static::getSniffClassReflection()->getConstants() as $constantName => $constantValue) {
                if (strpos($constantName, 'CODE_') !== 0 || in_array($constantValue, $codesToCheck, true)) {
                    continue;
                }

                $codeSniffer->ruleset->ruleset[sprintf('%s.%s', static::getSniffName(), $constantValue)]['severity'] = 0;
            }
        }

        $codeSniffer->ruleset->populateTokenListeners();

        $file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
        $file->process();

        foreach ($file->getErrors() as $errorsOnLine) {
            foreach ($errorsOnLine as $errorsOnPosition) {
                foreach ($errorsOnPosition as $error) {
                    if (strpos($error['source'], 'Internal.') === 0) {
                        throw new Exception($error['message']);
                    }
                }
            }
        }

        return $file;
    }

    protected static function assertNoSniffErrorInFile(File $phpcsFile): void
    {
        $errors = array();
        foreach ($phpcsFile->getErrors() as $line_number => $error) {
            $errors[] = $error[1][0]['message'] . '::' . $line_number;
        }
        self::assertEmpty($errors, sprintf('No errors expected, but %d errors found:', count($errors)) . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $errors));
    }

    protected static function assertSniffError(File $phpcsFile, int $line, string $code, ?string $message = null): void
    {
        $errors = $phpcsFile->getErrors();
        self::assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

        $sniffCode = sprintf('%s.%s', static::getSniffName(), $code);

        self::assertTrue(
            self::hasError($errors[$line], $sniffCode, $message),
            sprintf(
                'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
                $sniffCode,
                $message !== null ? sprintf(' with message "%s"', $message) : '',
                $line,
                PHP_EOL . PHP_EOL,
                $line,
                PHP_EOL,
                self::getFormattedErrors($errors[$line]),
                PHP_EOL
            )
        );
    }

    protected static function assertNoSniffError(File $phpcsFile, int $line): void
    {
        $errors = $phpcsFile->getErrors();
        self::assertFalse(
            isset($errors[$line]),
            sprintf(
                'Expected no error on line %s, but found:%s%s%s',
                $line,
                PHP_EOL . PHP_EOL,
                isset($errors[$line]) ? self::getFormattedErrors($errors[$line]) : '',
                PHP_EOL
            )
        );
    }

    protected static function assertAllFixedInFile(File $phpcsFile): void
    {
        $phpcsFile->disableCaching();
        $phpcsFile->fixer->fixFile();
        self::assertStringEqualsFile(preg_replace('~(\\.php)$~', '.fixed\\1', $phpcsFile->getFilename()), $phpcsFile->fixer->getContents());
    }

    protected static function getSniffName(): string
    {
        return preg_replace(
            [
                '~\\\~',
                '~\.Sniffs~',
                '~Sniff$~',
            ],
            [
                '.',
                '',
                '',
            ],
            static::getSniffClassName()
        );
    }

    protected static function getSniffClassName(): string
    {
        $class = substr(static::class, 0, -strlen('Test'));
        $class = str_replace('\Tests\\', '\\', $class);
        return $class;
    }

    protected static function getSniffClassReflection(): ReflectionClass
    {
        static $reflections = [];

        /** @phpstan-var class-string $className */
        $className = static::getSniffClassName();

        return $reflections[$className] ?? $reflections[$className] = new ReflectionClass($className);
    }

    /**
     * @param (string|int)[][][] $errorsOnLine
     * @param string $sniffCode
     * @param string|null $message
     * @return bool
     */
    private static function hasError(array $errorsOnLine, string $sniffCode, ?string $message = null): bool
    {
        foreach ($errorsOnLine as $errorsOnPosition) {
            foreach ($errorsOnPosition as $error) {
                /** @var string $errorSource */
                $errorSource = $error['source'];
                /** @var string $errorMessage */
                $errorMessage = $error['message'];

                if (!(
                    $errorSource === $sniffCode
                    && ($message === null || strpos($errorMessage, $message) !== false)
                )) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param (string|int|bool)[][][] $errors
     */
    private static function getFormattedErrors(array $errors): string
    {
        return implode(PHP_EOL, array_map(static function (array $errors): string {
            return implode(PHP_EOL, array_map(static function (array $error): string {
                return sprintf("\t%s: %s", $error['source'], $error['message']);
            }, $errors));
        }, $errors));
    }
}
