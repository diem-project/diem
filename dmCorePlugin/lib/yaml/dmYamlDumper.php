<?php

class dmYamlDumper extends sfYamlDumper
{
  public function dump($input, $inline = 0, $indent = 0, $separators = 0)
  {
    if($separators <= 0)
    {
      return parent::dump($input, $inline, $indent);
    }


    $output = '';
    $prefix = $indent ? str_repeat(' ', $indent) : '';

    if ($inline <= 0 || !is_array($input) || empty($input))
    {
      $output .= $prefix.sfYamlInline::dump($input);
    }
    else
    {
      $isAHash = array_keys($input) !== range(0, count($input) - 1);


      foreach ($input as $key => $value)
      {
        $spacing = max(0 , $separators -($indent + strlen($key) + 1));

        $willBeInlined = $inline - 1 <= 0 || !is_array($value) || empty($value);

        $output .= sprintf('%s%s%s%s',
          $prefix,
          $isAHash ? sfYamlInline::dump($key).':'.str_repeat(' ', $spacing) : '-',
          $willBeInlined ? ' ' : "\n",
          $this->dump($value, $inline - 1, $willBeInlined ? 0 : $indent + 2, $separators )
        ).($willBeInlined ? "\n" : '');
      }
    }

    return $output;
  }
}