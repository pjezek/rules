<?php
/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\UserRoleRemoveTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;
use Drupal\Tests\rules\Integration\RulesUserIntegrationTestTrait;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Action\UserRoleRemove
 * @group rules_actions
 */
class UserRoleRemoveTest extends RulesEntityIntegrationTestBase
{

  use RulesUserIntegrationTestTrait;
  /**
   * The action that is being tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    parent::setUp();
    $this->enableModule('user');
    $this->action = $this->actionManager->createInstance('rules_user_role_remove');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary()
  {
    $this->assertEquals('Remove roles of particular users', $this->action->summary());
  }

  /**
   * Tests removing existing role from user.
   *
   * @covers ::execute
   * @covers ::doExecute
   * @dataProvider rolesProvider
   * @param \Drupal\user\UserInterface $account
   *   Provides mocked user objects for the test.
   * @param array $user
   *   Array of contexts to get saved.
   */
  public function testRemoveExistingRole($account, array $user)
  {
    // Set-up a mock user with role 'editor'.
    $editor = $this->getMockedUserRole('editor');

    // Test removing one role.
    $this->action
      ->setContextValue('user', $account)
      ->setContextValue('roles', [$editor])
      ->execute();

    $this->assertEquals($this->action->autoSaveContext(), $user, 'Action returns nothing for auto saving since the user has been saved already.');
  }

  /**
   * @return array
   */
  public function rolesProvider()
  {

    $account1 = $this->getMockedUser();
    $account1->expects($this->once())
      ->method('hasRole')
      ->with(
        $this->equalTo('editor')
      )
      ->will($this->returnValue(TRUE));

    $account1->expects($this->once())
      ->method('removeRole')
      ->with(
        $this->equalTo('editor')
      );

    $account2 = $this->getMockedUser();
    $account2->expects($this->once())
      ->method('hasRole')
      ->with(
        $this->equalTo('editor')
      )
      ->will($this->returnValue(FALSE));

    $account2->expects($this->never())
      ->method('removeRole')
      ->with(
        $this->equalTo('editor')
      );

    return array(
      'removing of one role' => [$account1, ['user']],
      'removing of none role' => [$account2, []],
    );
  }

}

