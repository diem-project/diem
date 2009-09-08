<?php

class dmDataService extends dmService
{

	protected
	  $datas = array(
	    "filesystem",
	    "permissions",
	    "groups",
	    "users",
      "settings",
	    "layouts",
	    "pages",
	    "i18n",
	    "media"
	  );

  public function execute()
  {
    $this->dispatcher->notify(new sfEvent($this, 'dm.data.before'));
    
  	foreach($this->datas as $data)
  	{
  		$this->log("load data $data");
  		$method = "load".dmString::camelize($data);
  		$this->$method();
  	}
  	
  	$this->dispatcher->notify(new sfEvent($this, 'dm.data.after'));

  	$cc = new dmClearCacheService($this->dispatcher, $this->formatter);
  	$cc->execute();
  }

  
  protected function loadFilesystem()
  {
  	dmProject::checkFilesystemPermissions();
  }
  

  protected function loadSettings()
  {
    $array = array(
      'site_name' => array(
        'default_value' => dmString::humanize(dmProject::getKey()),
        'description' => 'The site name',
        'group_name' =>'site'
      ),
      'site_active' => array(
        'type' => 'boolean',
        'default_value' => 1,
        'description' => 'Is the site ready for visitors ?',
        'group_name' =>'site'
      ),
      'site_indexable' => array(
        'type' => 'boolean',
        'default_value' => 0,
        'description' => 'Is the site ready for search engine crawlers ?',
        'group_name' =>'site'
      ),
      'site_working_copy' => array(
        'type' => 'boolean',
        'default_value' => 1,
        'description' => 'Is this site the current working copy ?',
        'group_name' =>'site'
      ),
      'ga_key' => array(
        'description' => 'The google analytics key without javascript stuff ( Ex: UA-9876614-1 )',
        'group_name' =>'tracking'
      ),
      'gwt_key' => array(
        'description' => 'The google webmaster tools filename without google and .html ( Ex: a913b555ba9b4f13 )',
        'group_name' =>'tracking'
      ),
      'gwt_key' => array(
        'description' => 'The google webmaster tools filename without google and .html ( Ex: a913b555ba9b4f13 )',
        'group_name' =>'external services'
      ),
      'xiti_code' => array(
        'type' => 'textarea',
        'description' => 'The xiti html code',
        'group_name' =>'tracking'
      ),
      'gmap_key' => array(
        'description' => 'The google map key ( Ex: ABQIAAAARcvUUsf4RP8fmjHaFYFYQxRhf7uCiJccoEylUqtC2qy_Rw3WKhSEa96 )',
        'group_name' =>'external services'
      ),
      'search_stop_words' => array(
        'type' => 'textarea',
        'description' => 'The words we do not want to search (Ex:  the, a, to )',
        'group_name' =>'search engine'
      ),
      'base_urls' => array(
        'type' => 'textarea',
        'description' => 'Diem base urls for different applications/environments/cultures',
        'group_name' => 'internal',
        'credentials' => 'system'
      )
    );

    $existingSettings = dmDb::query('DmSetting s INDEXBY s.name')
    ->select('s.name')
    ->fetchArray();
    
    foreach($array as $name => $config)
    {
      if (!isset($existingSettings[$name]))
      {
        $setting = new DmSetting;
        $setting->name = $name;
        $setting->fromArray($config);

        $setting->save();
      }
    }
  }

  protected function loadMedia()
  {
  	dmDb::table('DmMediaFolder')->checkRoot();
  }

  protected function loadUsers()
  {
    if (!$superAdmin = dmDb::query('sfGuardUser u')->where('u.is_super_admin = ?', true)->fetchRecord())
    {
      $superAdmin = dmDb::create('sfGuardUser', array(
        'is_super_admin' => true,
        'username' => 'admin',
        'password' => 'admin',
        'email' => 'admin@'.dmProject::getKey().'.com'
      ))->saveGet();
    }

    if(!$superAdminProfile = $superAdmin->Profile->orNull())
    {
      dmDb::create('DmProfile', array(
        'user_id' => $superAdmin->id
      ))->save();
    }
  }

  protected function loadLayouts()
  {
  	if (!dmDb::table('DmLayout')->count())
  	{
	    dmDb::create('DmLayout', array('name' => 'Global'))->save();
	    dmDb::create('DmLayout', array('name' => 'Home'))->save();
  	}
  }

  protected function loadPages()
  {
    dmDb::table('DmPage')->checkBasicPages();
  }

  protected function loadI18n()
  {
  	$this->loadI18nCatalogue();
  	$this->loadI18nTransUnit();
  }

  protected function loadI18nCatalogue()
  {
    foreach( dm::getI18n()->getCultures() as $culture)
    {
      /*
       * English to $culture
       */
      if (!Catalogue::retrieveBySourceTargetSpace('en', $culture, 'messages'))
      {
      	dmDb::create('Catalogue', array(
          'source_lang' => 'en',
      	  'target_lang' => $culture,
      	  'name' => 'messages.'.$culture
      	))->save();
      }

      if ($culture != 'en' && !Catalogue::retrieveBySourceTargetSpace('en', $culture, 'dm'))
      {
        dmDb::create('Catalogue', array(
          'source_lang' => 'en',
          'target_lang' => $culture,
          'name' => 'dm.'.$culture
        ))->save();
      }

      /*
       * Default culture to $culture
       */
      if ($culture != dmI18n::getFirstCulture())
      {
	      if (!Catalogue::retrieveBySourceTargetSpace(dmI18n::getFirstCulture(), $culture, 'messages'))
	      {
	        dmDb::create('Catalogue', array(
	          'source_lang' => 'en',
	          'target_lang' => $culture,
	          'name' => 'messages.'.$culture
	        ))->save();
	      }
      }
    }
  }

  protected function loadI18nTransUnit()
  {
    foreach( dm::getI18n()->getCultures() as $culture)
    {
      if ($culture == 'en')
      {
        continue;
      }
      /*
       * English to $culture
       */
      $data_file = dmOs::join(sfConfig::get('dm_core_dir'), 'data/i18n', 'en_'.$culture.'.yml');
      if (is_readable($data_file))
      {
        $data = sfYaml::load(file_get_contents($data_file));
        $catalogue = Catalogue::retrieveBySourceTargetSpace('en', $culture, 'dm');

        $existingTranslations = dmDb::query('TransUnit t INDEXBY t.source')
        ->select('t.source')
        ->where('t.cat_id = ?', $catalogue->cat_id)
        ->fetchArray();

        $addedTranslations = new Doctrine_Collection(dmDb::table('TransUnit'));
        foreach($data as $source => $target)
        {
          if (!isset($existingTranslations[$source]))
          {
          	$addedTranslations->add(dmDb::create('TransUnit', array(
              'cat_id' => $catalogue->cat_id,
          	  'source' => $source,
          	  'target' => $target
          	)));
          }
        }
        $addedTranslations->save();
      }
    }
  }

  protected function loadPermissions()
  {
    $array = array(
      "admin" => "Log into administration",
      "log" => "Manage logs",
      "security" => "Manage security",
      "content" => "CRUD dynamic content in admin",
      "tidy_output" => "View tidy output",
      "html_validate_admin" => "View Html validation in admin",
      "html_validate_front" => "View Html validation in front",
      "code_editor" => "Use code editor",
      "console" => "Use linux console",
      "zone_add" => "Add zones",
      "zone_edit" => "Edit zones",
      "zone_delete" => "Delete zones",
      "widget_add" => "Add widgets",
      "widget_edit" => "Edit widgets",
      "widget_delete" => "Delete widgets",
      "page_bar_admin" => "See page bar in admin",
      "media_bar_admin" => "See media bar in admin",
      "media_library" => "Use media library in admin",
      "tool_bar_admin" => "See toolbar in admin",
      "page_bar_front" => "See page bar in front",
      "media_bar_front" => "See media bar in front",
      "tool_bar_front" => "See toolbar in front",
      "site_view" => "See website even if is not public",
      "loremize" => "Create automatic random content",
      "export_table" => "Export table contents",
      "sitemap" => "Regenerate sitemap",
      "automatic_metas" => "Configure automatic pages metas",
      "metas_validation" => "See meta validation",
      "google_analytics" => "Configure google analytics",
      "google_webmaster_tools" => "Configure google webmaster tools",
      "search_engine" => "Update and test internal search engine",
      "user_log" => "See the user log",
      "config_panel" => "Access the configuration panel"
    );

    $existingPermissions = dmDb::query('sfGuardPermission p INDEXBY p.name')
    ->select('p.name')
    ->fetchArray();

    $addedPermissions = new myDoctrineCollection('sfGuardPermission');
    foreach($array as $name => $description)
    {
      if (!isset($existingPermissions[$name]))
      {
        $addedPermissions->add(dmDb::create('sfGuardPermission', array(
          'name' => $name,
          'description' => $description
        )));
      }
    }
    $addedPermissions->save();
  }

  protected function loadGroups()
  {
    $array = array(
      "developper" => array(
        'description' => "Able to read and update source code",
        'permissions' => array(
          'admin',
          'log',
          'content',
          'tidy_output',
          'html_validate_admin',
          'html_validate_front',
          'code_editor',
          'console',
          'media_library',
          'loremize',
          'export_table',
          'sitemap',
          'automatic_metas',
          'metas_validation',
          'google_analytics',
          'google_webmaster_tools',
          'tool_bar_admin',
          'page_bar_admin',
          'media_bar_admin',
          'tool_bar_front',
          'page_bar_front',
          'media_bar_front',
          'search_engine',
          'user_log',
          'config_panel'
        )
      ),
      "seo" => array(
        'description' => "Seo knowledge",
        'permissions' => array(
          'admin',
          'sitemap',
          'automatic_metas',
          'metas_validation',
          'google_analytics',
          'google_webmaster_tools',
          'tool_bar_admin',
          'page_bar_admin',
          'tool_bar_front',
          'page_bar_front',
          'user_log',
          'config_panel'
        )
      ),
      "integrator" => array(
        'description' => "Integrator",
        'permissions' => array(
          'admin',
          'content',
          'tidy_output',
          'html_validate_front',
          'code_editor',
          'media_library',
          'loremize',
          'export_table',
          'tool_bar_admin',
          'page_bar_admin',
          'media_bar_admin',
          'tool_bar_front',
          'page_bar_front',
          'media_bar_front',
          'zone_add',
          'zone_edit',
          'zone_delete',
          'widget_add',
          'widget_edit',
          'widget_delete',
          'config_panel'
        )
      ),
      "webmaster 1" => array(
        'description' => "Webmaster level 1",
        'permissions' => array(
          'admin',
          'content',
          'export_table',
          'tool_bar_admin',
          'page_bar_admin',
          'media_bar_admin',
          'tool_bar_front',
          'page_bar_front',
          'media_bar_front',
          'search_engine',
          'user_log',
          'config_panel'
        )
      )
    );

    $permissions = dmDb::query('sfGuardPermission p INDEXBY name')->select('p.name')->fetchArray();

    $groups = new Doctrine_Collection(dmDb::table('sfGuardGroup'));
    foreach($array as $name => $params)
    {
      if (!$group = dmDb::query('sfGuardGroup g')->where('g.name = ?', $name)->fetchRecord())
      {
      	$group = dmDb::create('sfGuardGroup', array(
          'name' => $name,
      	  'description' => $params['description']
      	))->saveGet();
      }
      $groups->add($group);

      $groupPermissions = array();
      foreach($params['permissions'] as $permissionName)
      {
        if (!isset($permissions[$permissionName]))
        {
          throw new dmException('There is no permission called '.$permissionName);
        }
        $groupPermissions[] = $permissions[$permissionName]['id'];
      }
      $group->link('permissions', $groupPermissions);
    }
    $groups->save();
  }

}