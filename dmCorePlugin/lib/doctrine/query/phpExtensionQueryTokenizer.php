<?php

class phpExtensionQueryTokenizer extends Doctrine_Query_Tokenizer
{
  public function tokenizeQuery($query)
  {
    return dql_tokenize_query($query);
  }

  public function bracketTrim($str, $e1 = '(', $e2 = ')')
  {
    return dql_bracket_trim($str, $e1, $e2);
  }

  public function bracketExplode($str, $d = ' ', $e1 = '(', $e2 = ')')
  {
    return dql_bracket_explode($str, $d, $e1, $e2);
  }

  public function quoteExplode($str, $d = ' ')
  {
    return dql_quote_explode($str, $d);
  }

  public function sqlExplode($str, $d = ' ', $e1 = '(', $e2 = ')')
  {
    return dql_sql_explode($str, $d, $e1, $e2);
  }

  public function clauseExplode($str, array $d, $e1 = '(', $e2 = ')')
  {
    return dql_clause_explode($str, $d, $e1, $e2);
  }

  private function getSplitRegExpFromArray(array $d)
  {
    return dql_get_split_regexp_from_array($d);
  }

  private function clauseExplodeRegExp($str, $regexp, $e1 = '(', $e2 = ')')
  {
    return dql_clause_explode_regexp($str, $regexp, $e1, $e2);
  }

  private function clauseExplodeCountBrackets($str, $regexp, $e1 = '(', $e2 = ')')
  {
    return dql_clause_explode_count_brackets($str, $regexp, $e1, $e2);
  }

  private function clauseExplodeNonQuoted($str, $regexp)
  {
    return dql_clause_explode_non_quoted($str, $regexp);
  }

  private function mergeBracketTerms(array $terms)
  {
    return dql_merge_bracket_terms($terms);
  }

  public function quotedStringExplode($str)
  {
    return dql_quoted_string_explode($str);
  }
}
