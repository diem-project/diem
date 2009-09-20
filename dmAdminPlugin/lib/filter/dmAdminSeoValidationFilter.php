<?php

class dmAdminSeoValidationFilter extends dmFilter
{

  protected static
  $validatableAttributes = array('slug', 'title', 'description');

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {

    if ($this->context->isHtmlForHuman() && $this->context->getUser()->can('seo'))
    {
      $validator = new dmSeoValidationService($this->getContext()->getEventDispatcher());

      $attributes = array();

      foreach(sfConfig::get('dm_seo_unique_validation') as $attribute => $validate)
      {
        if ($validate)
        {
          if (in_array($attribute, self::$validatableAttributes))
          {
            $attributes[] = $attribute;
          }
          else
          {
            throw new dmException(sprintf('%s is not a valid seo attribute', $attribute));
          }
        }
      }

      $duplicated = $validator->execute($attributes);

      if (count($duplicated) && dmContext::getInstance()->getModuleName() != "dmSeoValidation")
      {
        if (dmArray::get($duplicated, 'slug'))
        {
          $logMethod = 'logAlert';
          $message = 'Some page have the same url';
        }
        else
        {
          $logMethod = 'logInfo';
          $message = 'Some SEO improvments should be applied';
        }
        $this->getContext()->getUser()->$logMethod(
          dm::getI18n()->__($message).
          dmAdminLinkTag::build('dmSeoValidation/index')->text(dm::getI18n()->__('Click here to see them'))->set('.ml10')
       );

        $this->context->getCacheManager()->getCache('dm/seo/validation')->set('duplicated', $duplicated);
      }
    }

    $filterChain->execute();

  }

}