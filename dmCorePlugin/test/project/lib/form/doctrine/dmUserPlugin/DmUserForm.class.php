<?php

/**
 * DmUser form.
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
class DmUserForm extends PluginDmUserForm
{
  public function configure()
  {
  }

  public function checkFoundUsOther(sfValidatorBase $validator, array $values) {
    // if the field "found_us_where" is set to other, other cannot be empty
    if ($values['found_us_where'] == 'other' && $values['found_us_other'] == '') {
      $error = new sfValidatorError($validator,
              'Required');
      // throw an error schema so the error appears at the field "password"
      throw new sfValidatorErrorSchema($validator, array('found_us_other' => $error));
    }
    return $values;
  }

  public function checkSchoolOther(sfValidatorBase $validator, array $values) {
    // if the field "found_us_where" is set to other, other cannot be empty
    if ($values['school'] == 'other' && $values['school_other'] == '') {
      $error = new sfValidatorError($validator,
              'Required');
      // throw an error schema so the error appears at the field "password"
      throw new sfValidatorErrorSchema($validator, array('school_other' => $error));
    }
    return $values;
  }
}