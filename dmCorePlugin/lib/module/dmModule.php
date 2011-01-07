<?php

class dmModule extends dmMicroCache
{
	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var dmModuleSpace
	 */
	protected $space;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var dmModuleSecurityManager
	 */
	protected $securityManager;

	/**
	 * @var dmModuleManager
	 */
	protected static $manager;

	/**
	 * @param string $key
	 * @param dmModuleSpace $space
	 * @param array $options
	 */
	public function __construct($key, dmModuleSpace $space, array $options)
	{
		$this->key    = $key;
		$this->space  = $space;

		$this->initialize($options);
	}

	/**
	 * @param array $options
	 */
	protected function initialize(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @return dmModuleSpace
	 */
	public function getSpace()
	{
		return $this->space;
	}

	/**
	 * @return boolean
	 */
	public function isProject()
	{
		return $this instanceof dmProjectModule;
	}

	/**
	 * @return boolean
	 */
	public function isPlugin()
	{
		return (bool) $this->options['plugin'];
	}

	/**
	 * @return string
	 */
	public function getPluginName()
	{
		return $this->options['plugin'];
	}

	/**
	 * @return boolean
	 */
	public function isOverridden()
	{
		return (bool) $this->options['overridden'];
	}

	/**
	 * @return string
	 */
	public function getSfName()
	{
		return $this->options['sf_name'];
	}

	/**
	 * @return boolean
	 */
	public function hasAdmin()
	{
		return $this->options['has_admin'];
	}

	/**
	 * @return boolean
	 */
	public function hasFront()
	{
		return $this->options['has_front'];
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->key;
	}

	/**
	 * @return array
	 */
	public function toDebug()
	{
		return $this->toArray();
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 * @param $default mixed
	 * @return mixed
	 */
	public function getOption($key, $default = null)
	{
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param mixed
	 */
	public function setOption($key, $value)
	{
		return $this->options[$key] = $value;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->options['name'];
	}

	/**
	 * @return string
	 */
	public function getPlural()
	{
		return $this->options['plural'];
	}

	/**
	 * @return mixed array | string
	 */
	public function getCredentials()
	{
		return $this->options['credentials'];
	}

	/**
	 * @return string
	 */
	public function getModel()
	{
		return $this->options['model'];
	}

	/**
	 * @return boolean
	 */
	public function hasModel()
	{
		return false !== $this->options['model'];
	}

	/**
	 * @return boolean
	 */
	public function hasPage()
	{
		return false;
	}

	/**
	 * @return string
	 */
	public function getUnderscore()
	{
		return $this->options['underscore'];
	}


	/**
	 * @return dmDoctrineTable
	 */
	public function getTable()
	{
		if ($this->hasCache('table'))
		{
			return $this->getCache('table');
		}

		return $this->setCache('table', $this->hasModel() ? dmDb::table($this->options['model']) : false);
	}

	/**
	 * @return mixed dmModule || null
	 */
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

	/**
	 * @return boolean
	 */
	public function hasForeign($something)
	{
		if ($foreignModule = $this->getManager()->getModuleOrNull($something))
		{
			return array_key_exists($foreignModule->getKey(), $this->getForeigns());
		}
		return false;
	}

	/**
	 * @return array
	 */
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

	/**
	 * @return mixed dmModule||null
	 */
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

	/**
	 * @return boolean
	 */
	public function hasLocal($something)
	{
		if ($localModule = $this->getManager()->getModuleOrNull($something))
		{
			return array_key_exists($localModule->getKey(), $this->getLocals());
		}

		return false;
	}

	/**
	 * @return array
	 */
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

	/**
	 * @return dmModule
	 */
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

	/**
	 * @return boolean
	 */
	public function hasAssociation($something)
	{
		if ($associationModule = $this->getManager()->getModule($something))
		{
			return array_key_exists($associationModule->getKey(), $this->getAssociations());
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return array(
      'key' => $this->key,
      'model' => $this->options['model'],
      'options' => $this->options
		);
	}

	/**
	 * @return boolean
	 */
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

	/**
	 * @return boolean
	 */
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

	/**
	 * @param dmModuleManager $manager
	 */
	public static function setManager(dmModuleManager $manager)
	{
		self::$manager = $manager;
	}

	/**
	 * @param dmBaseActions $action
	 * @return dmModuleSecurityManager
	 */
	public function getSecurityManager(dmBaseActions $action=null)
	{
		if(!isset($this->securityManager))
		{
			$this->securityManager = dmContext::getInstance()->getServiceContainer()->getService('module_security_manager');
			$this->securityManager->setModule($this);
			if($action)
			{
				$this->securityManager->setAction($action);
			}
		}
		return $this->securityManager;
	}

	/**
	 * @todo finish implement this for widgets
	 */
	public function getActions()
	{
		$actionsClass = $this->getSfName() . 'Actions';
		try{
			$refl = new ReflectionClass($actionsClass);
		}catch(ReflectionException $e)
		{
			return array(); //no module/action for this dmModule
		}

	}

	/**
	 * @todo finish implement this for widgets in dmRecordPermission/Association/Admin
	 * @param unknown_type $class
	 */
	protected function getActionsOfClass(ReflectionClass $class)
	{
		$actions = array();
		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($methods as $method)
		{
			if(strlen($method->getName()) > 7 && substr($method->getName(), 0, 7) === 'execute')
			{
				$actions[]['name'] = substr($method->getName(), 7, strlen($method->getName())-1);
			}
		}
		return $actions;
	}

	/**
	 * Returns the path of this module, in the filesystem
	 * @return string the path in the filesystem
	 */
	public function getGenerationDir()
	{
		if(!$this->getOption('generate_dir', false))
		{
			if ($pluginName = $this->getPluginName())
			{
				if($this->isOverridden())
				{
					continue;
				}

				$this->setOption('generate_dir', dmOs::join(dmContext::getInstance()->getConfiguration()->getPluginConfiguration($pluginName)->getRootDir(), 'modules', $this->getSfName()));
			}
			else
			{
				$this->setOption('generate_dir', dmOs::join(sfConfig::get('sf_apps_dir'), DmContext::getInstance()->getConfiguration()->getApplication() . '/modules', $this->getSfName()));
			}
		}
		return $this->getOption('generate_dir');
	}
}
