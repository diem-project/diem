<?php // coding:utf-8
/**
* JsMinEnh.php (for PHP 5 only)
*
* Based on "PHP adaptation of JSMin"
* <http://gggeek.altervista.org/2006/12/22/shrink-the-size-of-your-javascript-with-js-min-the-php-way/>
* but freely adapted to my own needs.
*
* PHP adaptation of JSMin, published by Douglas Crockford as jsmin.c, also based
* on its Java translation by John Reilly.
*
* Permission is hereby granted to use the PHP version under the same conditions
* as jsmin.c, which has the following notice :
*
* ----------------------------------------------------------------------------
*
* Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of
* this software and associated documentation files (the "Software"), to deal in
* the Software without restriction, including without limitation the rights to
* use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
* of the Software, and to permit persons to whom the Software is furnished to do
* so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* The Software shall be used for Good, not Evil.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*
* ----------------------------------------------------------------------------
*
* @copyright   No new copyright ; please keep above and following information.
* @author      David Holmes <dholmes@cfdsoftware.net> of CFD Labs, France
* @author      Gaetano Giunta
* @author      Rémi Lanvin (aka cgo2) <cgo2@the-asw.com>, France
* @version     $Id: $
*/

/**
 * Generic exception class related to JSMin.
 */
class JsMinEnhException extends Exception
{
  public function __construct($msg)
  {
    parent :: __construct($msg);
  }
}


/**
* Main JSMin application class.
*
* Example of use :
*
* $code = file_get_contents($file);
* $jsMin = new JsMinEnh($code);
* $code = $jsMin->minify();
*/
class JsMinEnh
{
  /**
   * How fgetc() reports an End Of File.
   * N.B. : use === and not == to test the result of fgetc() ! (see manual)
   */
  const EOF = false;

  /**
   * Some ASCII character ordinals.
   */
  const ORD_NL = 10;
  const ORD_space = 32;
  const ORD_cA = 65;
  const ORD_cZ = 90;
  const ORD_a = 97;
  const ORD_z = 122;
  const ORD_0 = 48;
  const ORD_9 = 57;

  /**
   * Constant describing an {@link action()} : Output A. Copy B to A. Get the next B.
   */
  const JSMIN_ACT_FULL = 1;

  /**
   * Constant describing an {@link action()} : Copy B to A. Get the next B. (Delete A).
   */
  const JSMIN_ACT_BUF = 2;

  /**
   * Constant describing an {@link action()} : Get the next B. (Delete B).
   */
  const JSMIN_ACT_IMM = 3;

  /**
   * The input stream, from which to read a JS file to minimize. Obtained by fopen().
   * NB: might be a string instead of a stream
   * @var SplFileObject | string
   */
  private $in;

  /**
   * The output stream, in which to write the minimized JS file. Obtained by fopen().
   * NB: might be a string instead of a stream
   * @var SplFileObject | string
   */
  private $out;

  /**
   * Temporary I/O character (A).
   * @var string
   */
  private $theA;

  /**
   * Temporary I/O character (B).
   * @var string
   */
  private $theB;

  /** variables used for string-based parsing **/
  private $inLength = 0;
  private $inPos = 0;

  /**
   * Indicates whether a character is alphanumeric or _, $, \ or non-ASCII.
   *
   * @param   string      $c  The single character to test.
   * @return  boolean     Whether the char is a letter, digit, underscore, dollar, backslash, or non-ASCII.
   */
  private function isAlphaNum($c) {

    // Get ASCII value of character for C-like comparisons
    $a = ord($c);

    // Compare using defined character ordinals, or between PHP strings
    // Note : === is micro-faster than == when types are known to be the same
    return
      ($a >= self::ORD_a && $a <= self::ORD_z) ||
      ($a >= self::ORD_0 && $a <= self::ORD_9) ||
      ($a >= self::ORD_cA && $a <= self::ORD_cZ) ||
      $c === '_' || $c === '$' || $c === '\\' || $a > 126
    ;
  }

  /**
   * Get the next character from the input stream.
   *
   * If said character is a control character, translate it to a space or linefeed.
   *
   * @return  string      The next character from the specified input stream.
   * @see     $in
   * @see     peek()
   */
  private function get() {

    // Get next input character and advance position in file
    if ($this->inPos < $this->inLength) {
      $c = $this->in[$this->inPos];
      ++$this->inPos;
    }
    else {
      return self::EOF;
    }

    // Test for non-problematic characters
    if ($c === "\n" || $c === self::EOF || ord($c) >= self::ORD_space) {
      return $c;
    }

    // else
    // Make linefeeds into newlines

    if ($c === "\r") {
      return "\n";
    }

    // else
    // Consider space

    return ' ';
  }

  private function getCloser($string, $needle, $offset)
  {
    $closer = false;
    foreach ($needle as $n) {
      $pos = strpos($string, $n, $offset);
      if ( $pos !== false && ($closer === false || $pos < $closer) )
        $closer = $pos;
    }
    return $closer;
  }

  /**
   * Get the next character from the input stream, without gettng it.
   *
   * @return  string      The next character from the specified input stream, without advancing the position
   *                      in the underlying file.
   * @see     $in
   * @see     get()
   */
  private function peek()
  {
    return ($this->inPos < $this->inLength) ? $this->in[$this->inPos] : self::EOF;
  }

  /**
   * Adds a char to the output string
   * @see $out
   */
  function put($c)
  {
    $this->out .= $c;
  }

  /**
   * Get the next character from the input stream, excluding comments.
   *
   * {@link peek()} is used to see if a '/' is followed by a '*' or '/'.
   * Multiline comments are actually returned as a single space.
   *
   * @return  string  The next character from the specified input stream, skipping comments.
   * @see     $in
   */
  function next() {

    // Get next char from input, translated if necessary

    $c = $this->get();

    // Check comment possibility

    if ($c == '/') {

      // Look ahead : a comment is two slashes or slashes followed by asterisk (to be closed)

      switch ($this->peek()) {

        case '/' :

          // Comment is up to the end of the line
          $this->inPos = strpos($this->in, "\n", $this->inPos);
          return $this->in[$this->inPos];

        case '*' :

          // Comment is up to comment close.
          // Might not be terminated, if we hit the end of file.

          $this->inPos = strpos($this->in, "*/", $this->inPos);
          if ( $this->inPos === false ) {
            throw new JsMinEnhException('UnterminatedComment');
          }
          $this->inPos += 2;
          return ' ';
        default :

          // Not a comment after all

          return $c;
      }
    }

    // No risk of a comment

    return $c;
  }

  /**
   * Do something !
   *
   * The action to perform is determined by the argument :
   *
   * JSMin::ACT_FULL : Output A. Copy B to A. Get the next B.
   * JSMin::ACT_BUF  : Copy B to A. Get the next B. (Delete A).
   * JSMin::ACT_IMM  : Get the next B. (Delete B).
   *
   * A string is treated as a single character. Also, regular expressions are recognized if preceded
   * by '(', ',' or '='.
   *
   * @param   int     $action     The action to perform : one of the JSMin::ACT_* constants.
   */
  function action($action) {

    // Choice of possible actions
    // Note the frequent fallthroughs : the actions are decrementally "long"
    switch ($action) {

      case self::JSMIN_ACT_FULL :
        // Write A to output, then fall through

        $this->put($this->theA);

      case self::JSMIN_ACT_BUF : // N.B. possible fallthrough from above
        // Copy B to A

        $tmpA = $this->theA = $this->theB;

        // Treating a string as a single char : outputting it whole
        // Note that the string-opening char (" or ') is memorized in B

        if ($tmpA == '\'' || $tmpA == '"') {

          $pos = $this->inPos;
          while (true) {
            // instead of looping char by char, we directly go to the next
            // revelant char, thanks to php strpos function.
            $pos = $this->getCloser($this->in, array($this->theB,'\\',"\n"), $pos);

            if ( $pos === false ) {
              // Whoopsie
              throw new JsMinEnhException('UnterminatedStringLiteral');
            }

            $tmpA = $this->in[$pos];

            if ($tmpA == $this->theB) {
              // String terminated
              break; // from while(true)
            }
            if ($tmpA == "\n") {
              // Whoopsie
              throw new JsMinEnhException('UnterminatedStringLiteral');
            }
            // else
            if ($tmpA == '\\') {
              // Escape next char immediately
              $pos += 2;
            }
          }

          // cool, we got the whole string
          $this->put(substr($this->in, $this->inPos - 1, $pos - $this->inPos + 1));
          $this->inPos = $pos + 1;
          $this->theA = $tmpA;
        }

      case self::JSMIN_ACT_IMM : // N.B. possible fallthrough from above
        // Get the next B

        $this->theB = $this->next();

        // Special case of recognising regular expressions (beginning with /) that are
        // preceded by '(', ',' or '='

        $tmpA = $this->theA;

        if ($this->theB == '/' && ($tmpA == '(' || $tmpA == ',' || $tmpA == '=')) {

          // Output the two successive chars
          $this->put($tmpA);
          $this->put($this->theB);

          // Look for the end of the RE literal, watching out for escaped chars or a control /
          // end of line char (the RE literal then being unterminated !)
          $pos = $this->inPos;
          while (true) {
            // instead of looping char by char, we directly go to the next
            // revelant char, thanks to php strpos function.
            $pos = $this->getCloser($this->in, array('/','\\',"\n"), $pos);

            if ( $pos === false ) {
              // Whoopsie
              throw new JsMinEnhException('UnterminatedRegExpLiteral');
            }

            $tmpA = $this->in[$pos];

            if ($tmpA == '/') {
              // RE literal terminated
              break; // from while(true)
            }
            if ( $tmpA == "\n") {
              // Whoopsie
              throw new JsMinEnhException('UnterminatedRegExpLiteral');
            }
            // else
            if ($tmpA == '\\') {
              // Escape next char immediately
              $pos += 2;
            }
          }
          $this->put(substr($this->in, $this->inPos, $pos - $this->inPos));
          $this->inPos = $pos + 1;
          $this->theA = $tmpA;


          // Move forward after the RE literal
          $this->theB = $this->next();
        }

      break;
      default :
        throw new JsMinEnhException('Expected a JSMin::ACT_* constant in action()');
    }
  }

  /**
   * Run the JSMin application : minify some JS code.
   *
   * The code is read from the input stream, and its minified version is written to the output one.
   * In case input is a string, minified vesrions is also returned by this function as string.
   * That is : characters which are insignificant to JavaScript are removed, as well as comments ;
   * tabs are replaced with spaces ; carriage returns are replaced with linefeeds, and finally most
   * spaces and linefeeds are deleted.
   *
   * Note : name was changed from jsmin() because PHP identifiers are case-insensitive, and it is already
   * the name of this class.
   *
   * @see     JSMin()
   * @return null | string
   */
  function minify() {

    // Initialize A and run the first (minimal) action

    $this->theA = "\n";
    $this->action(self::JSMIN_ACT_IMM);

    // Proceed all the way to the end of the input file

    while ($this->theA !== self::EOF) {
      switch ($this->theA) {
        case ' ' :

          if ($this->isAlphaNum($this->theB)) {
            $this->action(self::JSMIN_ACT_FULL);
          }
          else {
            $this->action(self::JSMIN_ACT_BUF);
          }

        break;
        case "\n" :

          switch ($this->theB) {

            case '{' : case '[' : case '(' :
            case '+' : case '-' :

              $this->action(self::JSMIN_ACT_FULL);

            break;
            case ' ' :

              $this->action(self::JSMIN_ACT_IMM);

            break;
            default :

              if ($this->isAlphaNum($this->theB)) {
                $this->action(self::JSMIN_ACT_FULL);
              }
              else {
                $this->action(self::JSMIN_ACT_BUF);
              }

            break;
          }

        break;
        default :

          switch ($this->theB) {

            case ' ' :

              if ($this->isAlphaNum($this->theA)) {

                $this->action(self::JSMIN_ACT_FULL);
                break;
              }

              // else

              $this->action(self::JSMIN_ACT_IMM);

            break;
            case "\n" :

              switch ($this->theA) {

                case '}' : case ']' : case ')' : case '+' :
                case '-' : case '"' : case '\'' :

                  $this->action(self::JSMIN_ACT_FULL);

                break;
                default :

                  if ($this->isAlphaNum($this->theA)) {
                    $this->action(self::JSMIN_ACT_FULL);
                  }
                  else {
                    $this->action(self::JSMIN_ACT_IMM);
                  }

                break;
              }

            break;
            default :

              $this->action(self::JSMIN_ACT_FULL);

            break;
          }

        break;
      }
    }

    return $this->out;
  }

  /**
   * Prepare a new JSMin application.
   *
   * The next step is to {@link minify()} the input into the output.
   *
   * @param   string  $inString       The code to minify
   * @param   array   $comments       Optional lines to present as comments at the beginning of the output.
   */
  function __construct($inString, $comments = NULL) {
    $this->in = str_replace("\r\n","\n", $inString);
    $this->out = '';
    $this->inLength = strlen($this->in);
    $this->inPos = 0;

    if (is_array($comments)) {
      foreach ($comments as $comm) {
        $this->out .= '// '.str_replace("\n", " ", $comm)."\n";
      }
    }
  }
}