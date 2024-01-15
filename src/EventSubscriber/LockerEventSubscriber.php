<?php

namespace Drupal\node_locker\EventSubscriber;

use Drupal\node\Entity\NodeType;
use Drupal\node_locker\Event\LockerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Support for cloning address data.
 *
 * Provides an event subscriber to add initial values to address fields when
 * cloning. This method is needed because of the way address handles its fields,
 * otherwise we would be doing this sort of thing inside the form builder when
 * cloning.
 */
class LockerEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  protected $services;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->services = \Drupal::service('node_locker.services');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LockerEvent::NODE_LOCKER][] = ['onNodeCreate'];
    return $events;
  }

  /**
   * Alters the initial values.
   *
   * @param \Drupal\address\Event\InitialValuesEvent $event
   *   The initial values event.
   */
  public function onNodeCreate(LockerEvent $event) {
    $entity = $event->getNode();
    $origine = \Drupal::routeMatch()->getParameter('node');
    $node_type = NodeType::load($entity->type->target_id);
    $route_name = \Drupal::routeMatch()->getRouteName();
    $node_locker = $node_type->getThirdPartySetting('node_locker', 'available_locker');

    switch ($entity->isNew()) {
      case TRUE:
        if (isset($node_locker) && $node_locker && $route_name == "quick_node_clone.node.quick_clone") {
          $entity->set('node_locker', 1);
          $entity->set('origine', $origine->id());
        }
        break;

      case FALSE;
        if (isset($node_locker) && $node_locker && !$entity->node_locker->value) {
          $nodes = $this->services->getNodeList($entity->type->target_id, $entity->id());
          $fields_to_exclude = [
            'nid',
            'uuid',
            'vid',
            'langcode',
            'type',
            'revision_timestamp',
            'revision_uid',
            'revision_log',
            'uid',
            'created',
            'origine',
            'node_locker',
          ];
          foreach ($nodes as $node) {
            foreach ($node->getFields() as $field_name => $field) {
              if (in_array($field_name, $fields_to_exclude)) {
                continue;
              }
              $node->set($field_name, $entity->{$field_name}->getValue());
            }
            $node->save();
          }
        }
        break;
    }

  }

}
