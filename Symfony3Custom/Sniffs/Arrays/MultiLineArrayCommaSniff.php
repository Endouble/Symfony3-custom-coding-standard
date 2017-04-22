<?php

/**
 * Throws warnings if the last item in a multi line array does not have a trailing comma
 */
class Symfony3Custom_Sniffs_Arrays_MultiLineArrayCommaSniff implements PHP_CodeSniffer_Sniff
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
            T_ARRAY,
            T_OPEN_SHORT_ARRAY,
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
        $open   = $tokens[$stackPtr];

        if ($open['code'] === T_ARRAY) {
            $closePtr = $open['parenthesis_closer'];
        } else {
            $closePtr = $open['bracket_closer'];
        }

        if ($open['line'] <> $tokens[$closePtr]['line']) {
            $arrayIsNotEmpty = $phpcsFile->findPrevious(
                array(
                    T_WHITESPACE,
                    T_COMMENT,
                    T_ARRAY,
                    T_OPEN_PARENTHESIS,
                    T_OPEN_SHORT_ARRAY,
                ),
                $closePtr - 1,
                $stackPtr,
                true
            );

            if ($arrayIsNotEmpty !== false) {
                $lastComma = $phpcsFile->findPrevious(T_COMMA, $closePtr);

                while ($lastComma < $closePtr - 1) {
                    $lastComma++;

                    if ($tokens[$lastComma]['code'] !== T_WHITESPACE
                        && $tokens[$lastComma]['code'] !== T_COMMENT
                    ) {
                        $fix = $phpcsFile->addFixableError(
                            'Add a comma after each item in a multi-line array',
                            $stackPtr,
                            'Invalid'
                        );

                        if ($fix === true) {
                            $ptr = $phpcsFile->findPrevious(
                                array(T_WHITESPACE, T_COMMENT),
                                $closePtr - 1,
                                $stackPtr,
                                true
                            );
                            $phpcsFile->fixer->addContent($ptr, ',');
                            $phpcsFile->fixer->endChangeset();
                        }

                        break;
                    }
                }
            }
        }
    }
}
