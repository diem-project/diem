<?php

class dmConfigService extends dmService
{

	protected
	  $configs = array(
	    "robots"
,	    "modules"
	  );

  public function execute()
  {
  	foreach($this->datas as $data)
  	{
  		$this->log("load config $data");
  		$method = "load".dmString::camelize($data);
  		$this->$method();
  	}
  }

  protected function loadRobots()
  {
  	$robots = <<<EOF
User-agent: *
Disallow: /dm/
EOF;
    $robots_path = dmOs::join(sfConfig::get("sf_root_dir"), sfConfig::get("sf_web_dir"), "robots.txt");
    if (!file_exists($robots_path))
    {
    	file_put_contents($robots_path, $robots);
    }
    elseif(file_get_contents($robots_path) != $robots)
    {
    	if (is_writable($robots_path))
    	{
    		file_put_contents($robots_path, $robots);
    	}
    	else
    	{
    		$this->alert("Le robots.txt ne peut être mis à jour car $robots_path n'est pas accessible en écriture");
    	}
    }
  }

  protected function loadModules()
  {
    $this->loadAppModules("front", array(
      "default"
    ));
    $this->loadAppModules("admin", array(
      "default"
,     "dmAdmin"
    ));
  }

  protected function loadAppModules($app, $required_modules)
  {
  	if (!in_array($app, sfConfig::get("dm_enabled_parts")))
  	{
  		return;
  	}
    $settings_path = sfConfig::get("sf_root_dir")."/apps/$app/config/settings.yml";
    if (!is_readable($settings_path))
    {
    	return $this->alert("alert", "Les settings de $app ne peuvent être mis à jour car ce fichier n'existe pas : $settings_path");
    }
    $settings_string = file_get_contents($settings_path);
    $settings = sfYaml::load($settings_string);
    $modules = dmArray::get($settings["all"][".settings"], "enabled_modules", array());
    $new_modules = array_values(array_unique(array_merge($modules, $required_modules)));
    if ($new_modules != $modules)
    {
      if (is_writable($settings_path))
      {
        $settings["all"][".settings"]["enabled_modules"] = $new_modules;
        $yaml_settings = sfYaml::dump($settings, 4);
        $yaml_settings = preg_replace(
          "|'<\?(.*)\?>'|", '<?$1?>', $yaml_settings
        );
        file_put_contents($settings_path, $yaml_settings);
      }
      else
      {
        return $this->alert("Les settings de $app ne peuvent être mis à jour car ce fichier n'est pas accessible en écriture : $settings_path");
      }
    }
  }

  protected function loadFactories()
  {
    $front_factories_path = sfConfig::get("sf_root_dir")."/apps/front/config/factories.yml";
    $admin_factories_path = sfConfig::get("sf_root_dir")."/apps/admin/config/factories.yml";
    $factories = <<<EOF
all:
  routing:
    class:                  dmsRouting
  i18n:
    param:
      source:               Propul
  logger:
    param:
      loggers:
        sf_web_debug:
          class:            dmsWebDebugLogger
          param:
            web_debug_class: dmsWebDebug
  user:
    param:
      timeout:              2592000
  storage:
    param:
      session_name:         __SITE_KEY__
  view_cache:
    class:                  dmsMetaCache

prod:
  logger:
    class:   sfNoLogger
    param:
      level:   err
      loggers: ~

cli:
  controller:
    class: sfConsoleController
  request:
    class: sfConsoleRequest
  response:
    class: sfConsoleResponse
EOF;
    $factories = str_replace("__SITE_KEY__", aze::getSiteKey(), $factories);
    if (file_get_contents($front_factories_path) != $factories)
    {
      if (is_writable($front_factories_path))
      {
        file_put_contents($front_factories_path, $factories);
      }
      else
      {
        aze::setFlash("alert", "Les filtres ne peuvent être mis à jour car ce fichier n'est pas accessible en écriture : $front_factories_path");
      }
    }
    if (file_get_contents($admin_factories_path) != $factories)
    {
      if (is_writable($admin_factories_path))
      {
        file_put_contents($admin_factories_path, $factories);
      }
      else
      {
        aze::setFlash("alert", "Les filtres ne peuvent être mis à jour car ce fichier n'est pas accessible en écriture : $admin_factories_path");
      }
    }
  }

  protected function loadFilters()
  {
    $front_filters_path = sfConfig::get("sf_root_dir")."/apps/front/config/filters.yml";
    $front_filters = <<<EOF
rendering:  ~

security:
  class:    sfGuardBasicSecurityFilter

dms_front:
  class:    dmsFrontFilter

cache:      ~

dms_front_html:
  class:    dmsFrontHtmlFilter

common:
  class:    dmsCommonFilter

dms_combine:
  class:    dmsCombineFilter

execution:  ~
EOF;
    $admin_filters_path = sfConfig::get("sf_root_dir")."/apps/admin/config/filters.yml";
    $admin_filters = <<<EOF
rendering:  ~

security:
  class:    sfGuardBasicSecurityFilter

dms_firefox:
  class:    dmsFirefoxFilter

dms_page_generator:
  class:    dmsPageGeneratorFilter

dms_ambigurl:
  class:    dmsAmbigurlFilter

dms_admin:
  class:    dmsAdminFilter

cache:      ~

dms_admin_html:
  class:    dmsAdminHtmlFilter

common:
  class:    dmsCommonFilter

dms_combine:
  class:    dmsCombineFilter

execution:  ~
EOF;

    if (file_get_contents($front_filters_path) != $front_filters)
    {
      if (is_writable($front_filters_path))
      {
        file_put_contents($front_filters_path, $front_filters);
      }
      else
      {
        aze::setFlash("alert", "Les filtres ne peuvent être mis à jour car ce fichier n'est pas accessible en écriture : $front_filters_path");
      }
    }
    if (file_get_contents($admin_filters_path) != $admin_filters)
    {
      if (is_writable($admin_filters_path))
      {
        file_put_contents($admin_filters_path, $admin_filters);
      }
      else
      {
        aze::setFlash("alert", "Les filtres ne peuvent être mis à jour car ce fichier n'est pas accessible en écriture : $admin_filters_path");
      }
    }
  }

}