<?php

namespace Drupal\node_locker\Service;

use Drupal\node\Entity\Node;

/**
 * Class LockerServices for function of all services.
 *
 * @package node_locker
 */
class LockerServices {

  /**
   * Fucntion for load nodes has spesific nid in your origine field.
   */
  public function getNodeList($target_id, $source_id) {
    $nodes = [];
    $query = \Drupal::entityQuery('node')
      ->condition('origine', $source_id)
      ->condition('type', $target_id)
      ->accessCheck(TRUE);
    $results = $query->execute();
    if (count($results) > 0) {
      foreach ($results as $id) {
        $nodes[] = Node::load($id);
      }
    }
    return $nodes;
  }

}
