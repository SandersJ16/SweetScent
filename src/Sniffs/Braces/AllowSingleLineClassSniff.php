<?php

namespace Sandersj16\SweetScent\Sniffs\Braces;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class AllowSingleLineClassSniff implements Sniff {
    public function register() {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $currentTokenIndex) {
        $tokens = $phpcsFile->getTokens();
        var_dump($tokens[$currentTokenIndex]);
    }
}
