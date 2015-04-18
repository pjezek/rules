<?php
/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\UserRoleRemove class.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\rules\Core\RulesActionBase;
use Drupal\user\UserInterface;

/**
 * Provides a 'Remove user role' action.
 *
 * @Action(
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
class UserRoleRemove extends RulesActionBase {

  /**
   * Flag that indicates if the entity should be auto-saved later.
   *
   * @var bool
   */
  protected $saveLater = FALSE;

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Remove roles of particular users');
  }

  /**
   * Executes the action with passed in contexts
   * @see ::execute
   * @param \Drupal\user\UserInterface $account
   *   User entity on which roles should be removed.
   * @param \Drupal\user\RoleInterface[] $roles
   *   Roles which should be removed on the user.
   */
  public function doExecute(UserInterface $account, array $roles) {
    foreach ($roles as $role) {
      // Check if user has role.
      if ($account->hasRole($role->id())) {
        $account->removeRole($role->id());
        $this->saveLater = TRUE;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    if ($this->saveLater) {
      return ['user'];
    }
    return [];
  }
}
