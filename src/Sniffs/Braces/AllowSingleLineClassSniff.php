<?php

namespace SweetScent\Sniffs\Braces;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class AllowSingleLineClassSniff implements Sniff
{
    const INCORRECT_CLASS_BRACKETS = 'INCORRECT_CLASS_BRACKET';

    public function register()
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $currentTokenIndex)
    {
        $tokens = $phpcsFile->getTokens();
        $variablePointer = $currentTokenIndex;
        $new_line = false;

        // Iterate through the tokens until you find the opening bracket of the class definition
        // Keep track of if we encountered a newline before getting to the opening bracket
        do {
            $variablePointer++;
            $token = $tokens[$variablePointer];
            $token_code = $token['code'];
            if ($token_code == T_WHITESPACE && strpos($token['content'], "\n") !== false) {
                $new_line = true;
            }
        } while ($token_code != T_OPEN_CURLY_BRACKET);

        if (!$new_line && ($tokens[$variablePointer - 1]['code'] != T_WHITESPACE || $tokens[$variablePointer - 1]['content'] != ' ')) {
            $phpcsFile->addError('Must have single space after class declaration before opening bracket', $variablePointer, self::INCORRECT_CLASS_BRACKETS);
            return;
        }

        $i = 1;
        $next_token = $tokens[$variablePointer + $i];

        // If we encountered a newline then make sure that the class has a body
        if ($new_line) {
            do {
                if ($next_token['code'] == T_CLOSE_CURLY_BRACKET) {
                    $phpcsFile->addError('Empty class declaration with braces on newline', $variablePointer, self::INCORRECT_CLASS_BRACKETS);
                    break;
                }
                ++$i;
                $next_token = $tokens[$variablePointer + $i];
            } while ($next_token['code'] != T_WHITESPACE);

        // If we didn't encounter a newline make sure the next character after the opneing bracket is a closing bracket
        } elseif (!$new_line && $next_token['code'] != T_CLOSE_CURLY_BRACKET) {
            $phpcsFile->addError('Starting class brace on same line of non-empty class', $variablePointer, self::INCORRECT_CLASS_BRACKETS);
        }
    }
}
