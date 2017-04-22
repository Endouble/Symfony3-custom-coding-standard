<?php

/**
 * Throws warnings if comma isn't followed by a whitespace.
 */
class Symfony3Custom_Sniffs_WhiteSpace_CommaSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_COMMA,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $line   = $tokens[$stackPtr]['line'];

        if ($tokens[$stackPtr + 1]['line'] === $line
            && $tokens[$stackPtr + 1]['code'] !== T_WHITESPACE
        ) {
            $fix = $phpcsFile->addFixableError(
                'Add a single space after each comma delimiter',
                $stackPtr,
                'Invalid'
            );

            if ($fix) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        }
    }
}
