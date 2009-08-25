<?php

return;

echo £o("div.dm_text.text_".$style);

  echo £o('div.text_head.clearfix');
    echo £('h2.title', $title);
  echo £c('div');

  // wrappers de style
  echo '<div class="blob_content_left"><div class="blob_content_right">';

  echo £o("div.clearfix.columns_".$blob->getColumns()); // contenu en colonnes

    switch($blob->getImagePosition()) // choix de la classe de l'image en fonction de la template choisie
    {
      case "img_left":    $img_css = ".fleft.imgleft"; break;
      case "img_right":   $img_css = ".fright.imgright"; break;
      case "img_top":     $img_css = ".imgtop"; break;
      default:            $img_css = false;
    }
    if ($img_css && $blob->getMedia()) // affichage de l'image
    {
      echo £("div.blob_image".$img_css, $blob->getImageWithLink());
    }

    if($blob->getNom() && $blob->getTitlePosition() == "title_in") // affichage du titre à l'intérieur
    {
      echo £('h2.blob_nom.title_in', $blob->getNom());
    }

    // affichage du texte
    echo £('div.blob_description', aze::markdown($blob->getDescription(), false));

    if($blob->getImagePosition() == 'img_bottom')
    {
      echo £("div.blob_image.imgbottom", $blob->getImageWithLink());
    }

  echo £c("div"); // fin du contenu en colonnes

  // fin des wrappers de style
  echo '</div></div><div class="blob_foot_left"><div class="blob_foot_right"></div></div>';

echo £c("div"); // fin de blob_style