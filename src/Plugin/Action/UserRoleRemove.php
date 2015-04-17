<?php

namespace Drupal\rules\Plugin\Action;

use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'Remove user role' action.
 *
 * @action(
 *   id = "rules_user_role_remove",
 *   label = @Translation("Remove user role"),
 *   category = @Translation("User"),
 *   context = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User")
 *     ),
 *     "roles" = @ContextDefinition("entity:user_role",
 *       label = @Translation("Roles"),
 *       multiple = TRUE
 *     )
 *   }
 * )
 *
 */
class UserRoleRemove extends RulesActionBase
{

  /**
   * Flag that indicates if the entity should be auto-saved later.
   *
   * @var bool
   */
  protected $saveLater = FALSE;

  /**
   * {@inheritdoc}
   */
  public function summary()
  {
    return $this->t('Remove roles of particular users');
  }

  /**
   * {@inheritdoc}
   */
  public function execute()
  {

    /** @var \Drupal\user\Entity\User $account */
    $account = $this->getContextValue('user');

    /** @var \Drupal\user\RoleInterface $roles */
    $roles = $this->getContextValue('roles');
    foreach ($roles as $role) {
      // Check if user has role.
      if ($account->hasRole($role->id())) {
        // Remove role
        $account->removeRole($role->id());
        // Set flag that indicates if the entity should be auto-saved later.
        $this->saveLater = TRUE;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext()
  {
    if ($this->saveLater) {
      return ['user'];
    }
    return [];
  }
}