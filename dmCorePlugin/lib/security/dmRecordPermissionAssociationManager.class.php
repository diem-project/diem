<?php
/*
 * 
 * 
 * 
 */

/**
 * 
 * @author serard
 *
 */
class dmRecordPermissionAssociationManager
{
	/**
	 * @var dmContext
	 */
	protected $context;

	/**
	 * @var sfServiceContainer
	 */
	protected $container;

	/**
	 * @var myUser
	 */
	protected $user;


	/**
	 * @param dmContext $context
	 * @param sfServiceContainer $container
	 * @param myUser $user
	 */
	public function __construct(dmContext $context, sfServiceContainer $container, myUser $user)
	{
		$this->context = $context;
		$this->container = $container;
		$this->user = $user;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getServiceContainer()
	{
		return $this->container;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function manage(DmRecordPermission $permission)
	{
		if(!$this->user->getUser()) return;
		$userId = $this->user->getUser()->get($this->user->getUser()->getTable()->getIdentifier());
		 
		$query = dmDb::table('DmRecordPermissionAssociation')->createQuery('p')
		->select('p.id, p.dm_secure_module, p.dm_secure_action, p.dm_secure_model, g.id, u.id')
		->leftJoin('p.Groups g')
		->leftJoin('p.Users u')
		//->leftJoin('g.Users u1')
		//->addWhere('(u.id = ? OR u1.id = ?)', array($userId, $userId))
		->addWhere('p.dm_secure_module = ?', $permission->get('secure_module'))
		->addWhere('p.dm_secure_action = ?', $permission->get('secure_action'))
		->addWhere('p.dm_secure_model = ?', $permission->get('secure_model'));
		
		$query = $this->context->getEventDispatcher()->filter(new sfEvent($permission, 'dm_record_permission_association_manager.filter_query'), $query)->getReturnValue();

		$associations = $query->execute();
		$this->associate($permission, $associations);
	}

	public static function associate(DmRecordPermission $permission, dmDoctrineCollection $associations)
	{
		foreach($associations as $association)
		{
			foreach($association->get('Groups') as $group)
			{
				/*foreach($group->get('Users') as $user)
				{
					$user->get('Records')->add($permission);
					$user->save();
				}*/
				$group->get('Records')->add($permission);
				$group->save();
			}
			
			foreach($association->get('Users') as $user)
			{
				$user->get('Records')->add($permission);
				$user->save();
			}
		}
	}
}