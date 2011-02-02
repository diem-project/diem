<?php

class dmAdminModelTreeView extends dmModelTreeView
{

  protected function renderModelLink(myDoctrineRecord $model)
  {
    return '<a data-model-id="'.$model->id.'"><ins></ins>'.$model.'</a>';
  }

}