<?php

if ($validator->isValid())
{
  echo £('div.valid.s16.s16_tick', $doctype);
}
else
{
  echo £("a.show_errors title='View W3C report'",
    £('span.error', $doctype." : ".plural("error", $validator->getNbErrors()))
  );
  echo
    £o("div.html_validation_errors.none").£o("div.html_errors").
    £("p.error", plural("error", $validator->getNbErrors())).
    £o("ul.errors")
  ;
  foreach($validator->getErrors() as $error)
  {
    echo £o("li".toggle(".odd"));
      echo £("p.error_line",
        "l.".$error->getLine()."&nbsp;&nbsp;".$error->getMessage()
      );
      echo £o("ul.code");
        foreach($validator->getHtmlLines($error->getLine(), 3) as $num => $line)
        {
          echo £("li".($num == 1 ? ".current" : ""),
            $line
          );
        }
      echo £c("ul");
    echo £c("li");
  }
  echo £c("ul").£c("div").£c("div");
}