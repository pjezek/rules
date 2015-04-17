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
  public function setUp() {
    parent::setUp();
    $this->enableModule('user');
    $this->action = $this->actionManager->createInstance('rules_user_role_remove');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('Remove roles of particular users', $this->action->summary());
  }


  /**
   * Tests removing role from user.
   *
   * @covers ::execute
   */
  public function testRemoveExistingRole() {

    // Set-up a mock user with role 'editor'.
    $account = $this->getMockedUser();
    $editor = $this->getMockedUserRole('editor');

    $account->expects($this->once())
      ->method('hasRole')
      ->with(
        $this->equalTo('editor')
      )
      ->will($this->returnValue(TRUE));

    $account->expects($this->once())
      ->method('removeRole')
      ->with(
        $this->equalTo('editor')
      );

    // Test removing one role.
    $this->action
      ->setContextValue('user', $account)
      ->setContextValue('roles', [$editor])
      ->execute();

    $this->assertEquals($this->action->autoSaveContext(), ['user'], 'Action returns nothing for auto saving since the user has been saved already.');
  }


}