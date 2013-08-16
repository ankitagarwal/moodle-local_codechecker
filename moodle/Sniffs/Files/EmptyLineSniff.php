<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Checks that there are never two empty lines together.
 *
 * @package    local_codechecker
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_Files_EmptyLineSniff implements PHP_CodeSniffer_Sniff
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
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        // We are only interested if it is a new line char.
        $tokens   = $phpcsFile->getTokens();
        $max = $phpcsFile->numTokens - 1;
        for ($tokencount = 0; $tokencount < $max; $tokencount++) {
            $nexttoken = $tokencount + 1;
            if ($tokens[$tokencount]['column'] === 1 && $tokens[$tokencount]['content'] === $phpcsFile->eolChar &&
                $tokens[$tokencount]['column'] === 1 && $tokens[$nexttoken]['content'] === $phpcsFile->eolChar) {
                $error = 'Consecutive multiple empty lines are not allowed.';

                // We cannot highlight an empty line, so highlight the line before.
                $phpcsFile->addError($error, $tokencount - 1, 'MultipleEmptyLine');
            }
        }
    }//end process()


}//end class

