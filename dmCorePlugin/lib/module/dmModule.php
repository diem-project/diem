<?php

class dmModule extends dmMicroCache
{
	protected
	$key,
	$space,
	$options;

	protected static
	$manager;

	public function __construct($key, dmModuleSpace $space, array $options)
	{
		$this->key    = $key;
		$this->space  = $space;

		$this->initialize($options);
	}

	protected function initialize(array $options)
	{
		$this->options = $options;
	}

	public function getSpace()
	{
		return $this->space;
	}

	public function isProject()
	{
		return $this instanceof dmProjectModule;
	}

	public function isPlugin()
	{
		return (bool) $this->options['plugin'];
	}

	public function getPluginName()
	{
		return $this->options['plugin'];
	}

	public function isOverridden()
	{
		return (bool) $this->options['overridden'];
	}

	public function getSfName()
	{
		return $this->options['sf_name'];
	}

	public function hasAdmin()
	{
		return $this->options['has_admin'];
	}

	public function hasFront()
	{
		return $this->options['has_front'];
	}

	public function __toString()
	{
		return $this->key;
	}

	public function toDebug()
	{
		return $this->toArray();
	}

	public function getKey()
	{
		return $this->key;
	}

	public function getOption($key, $default = null)
	{
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}

	public function setOption($key, $value)
	{
		return $this->options[$key] = $value;
	}

	public function getName()
	{
		return $this->options['name'];
	}

	public function getPlural()
	{
		return $this->options['plural'];
	}

	public function getCredentials()
	{
		return $this->options['credentials'];
	}

	public function getModel()
	{
		return $this->options['model'];
	}

	public function hasModel()
	{
		return false !== $this->options['model'];
	}

	public function hasPage()
	{
		return false;
	}

	public function getUnderscore()
	{
		return $this->options['underscore'];
	}


	public function getTable()
	{
		if ($this->hasCache('table'))
		{
			return $this->getCache('table');
		}

		return $this->setCache('table', $this->hasModel() ? dmDb::table($this->options['model']) : false);
	}

	public function getForeign($foreignModuleKey)
	{
		if ($foreignModule = $this->getManager()->getModuleOrNull($foreignModuleKey))
		{
			if ($this->hasForeign($foreignModule))
			{
				return $foreignModule;
			}
		}
		return null;
	}

	public function hasForeign($something)
	{
		if ($foreignModule = $this->getManager()->getModuleOrNull($something))
		{
			return array_key_exists($foreignModule->getKey(), $this->getForeigns());
		}
		return false;
	}

	public function getLocals()
	{
		if ($this->hasCache('locals'))
		{
			return $this->getCache('locals');
		}

		$locals = array();
		foreach($this->getTable()->getRelationHolder()->getLocals() as $relation)
		{
			if($localModule = $this->getManager()->getModuleByModel($relation->getClass()))
			{
				$locals[$localModule->getKey()] = $localModule;
			}
		}

		return $this->setCache('locals', $locals);
	}

	public function getLocal($localModuleKey)
	{
		if ($localModule = $this->getManager()->getModuleOrNull($localModuleKey))
		{
			if ($this->hasLocal($localModule))
			{
				return $localModule;
			}
		}
		return null;
	}

	public function hasLocal($something)
	{
		if ($localModule = $this->getManager()->getModuleOrNull($something))
		{
			return array_key_exists($localModule->getKey(), $this->getLocals());
		}

		return false;
	}

	public function getAssociations()
	{
		if ($this->hasCache('associations'))
		{
			return $this->getCache('associations');
		}

		$associations = array();
		foreach($this->getTable()->getRelationHolder()->getAssociations() as $key => $relation)
		{
			if($associationModule = $this->getManager()->getModuleByModel($relation->getClass()))
			{
				$associations[$associationModule->getKey()] = $associationModule;
			}
		}

		return $this->setCache('associations', $associations);
	}

	public function getAssociation($associationModuleKey)
	{
		if ($associationModule = $this->getManager()->getModuleOrNull($associationModuleKey))
		{
			if ($this->hasAssociation($associationModule))
			{
				return $associationModule;
			}
		}
		return null;
	}

	public function hasAssociation($something)
	{
		if ($associationModule = $this->getManager()->getModule($something))
		{
			return array_key_exists($associationModule->getKey(), $this->getAssociations());
		}

		return false;
	}

	public function toArray()
	{
		return array(
      'key' => $this->key,
      'model' => $this->options['model'],
      'options' => $this->options
		);
	}

	public function is($something)
	{
		if (is_string($something))
		{
			return $this->key == dmString::modulize($something);
		}

		if($something instanceof dmModule)
		{
			return $something->getKey() === $this->key;
		}

		return false;
	}

	public function interactsWithPageTree()
	{
		return $this->isProject();
	}

	/**
	 * @return dmModuleManager
	 */
	public function getManager()
	{
		return self::$manager;
	}

	public static function setManager(dmModuleManager $manager)
	{
		self::$manager = $manager;
	}
	
/*
 * 
 * Adds for security
 * 
 * 
 * 
 */
	
	public function hasSecurityConfiguration($app = null, $actionKind = null, $action = null)
	{
		if(null === $app){
			return $this->getOption('has_security', false);
		}else{
			$security = $this->getOption('security');
			$security = isset($security[$app]) ? $security[$app] : false;
		}
		if(!$security) return false;
			
		if(null === $actionKind){
			return $security;
		}else{
			$security = isset($security[$actionKind]) ? $security[$actionKind] : false;
		}
		if(!$security) return false;
			
		if(null ===$action){
			return $security;
		}else{
			return isset($security[$action]) ? $security[$action] : false;
		}
		return false;
	}

	public function getSecurityConfiguration($app = null, $actionKind = null, $action = null)
	{
		$security = $this->getOption('security');
		if(null === $app) return $security;
		
		$security = isset($security[$app]) ? $security[$app] : false;
		if(!$security) return false;
			
		if(null === $actionKind){
			return $security;
		}else{
			$security = isset($security[$actionKind]) ? $security[$actionKind] : false;
		}
		if(!$security) return false;
			
		if(null ===$action){
			return $security;
		}else{
			return isset($security[$action]) ? $security[$action] : false;
		}
		return false;
	}
}
