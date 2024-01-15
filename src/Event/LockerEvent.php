<?php

namespace Drupal\node_locker\Event;

use Drupal\node\NodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Defines events for the node_locker module.
 */
class LockerEvent extends Event {

  const NODE_LOCKER = 'node_locker.content_type_locker';

  /**
   * Flood event NODE.
   *
   * @var int
   */
  protected $node;

  /**
   * Constructs a node_locker event object.
   */
  public function __construct(NodeInterface $node) {
    $this->node = $node;
  }

  /**
   * Function for getCurrent node.
   */
  public function getNode() {
    return $this->node;
  }

}
