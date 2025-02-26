<?php

namespace Drupal\csc_site_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\smart_date_recur\Entity\SmartDateRule;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Database\Database;

/**
 * Provides a block to display canceled dates.
 *
 * @Block(
 *   id = "csc_calendar_link_block",
 *   admin_label = @Translation("Csc Calendar Link Block"),
 * )
 */
class CscCalendarLinkBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the node.
    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node instanceof NodeInterface && $node->bundle() == 'event') {
      $smart_date_field = $node->get('field_date');
      $class_name = get_class($smart_date_field);
      return [
        '#markup' => "<p>class is: $class_name</p>",
      ];
      /*
      $links = calendar_links(
        $node->label(),
        $node->get('field_date')->value,
        $node->get('field_end_date')->value,
        $node->get('field_all_day')->value,
        $node->get('field_description')->value,
        $node->get('field_location')->value
      );

      $items = [];
      foreach ($links as $link) {
        $items[] = [
        '#type' => 'link',
        '#title' => $link['type_name'],
        '#url' => $link['url'],
        '#attributes' => ['class' => ['calendar-link-' . $link['type_key']]],
        ];
      }

      return [
        '#theme' => 'item_list',
        '#items' => $items,
        '#cache' => ['contexts' => ['route.node']],
      ];*/
    } else {
      $isnode = $node instanceof NodeInterface ? 'is not a node' : 'is a node';
      $isevent = $node->bundle() == 'event' ? 'bundle does not match' : 'bundle does match';
      return [
        '#markup' => "<p>Not an evnt! It $isnode, $isevent, and it's a [{$node->bundle()}]</p>",
      ];
    }
  }
}
