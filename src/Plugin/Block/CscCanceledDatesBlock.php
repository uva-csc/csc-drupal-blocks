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
 *   id = "csc_canceled_dates_block",
 *   admin_label = @Translation("Canceled Dates Block"),
 * )
 */
class CscCanceledDatesBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the node.
    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node instanceof EntityInterface && $node->hasField('field_date')) {
      // Retrieve the recurring date field.
      $smart_date_field = $node->get('field_date');
      $canceled_dates = [];

      // Get the first item value (in case of multi-value fields).
      $date_field_value = $smart_date_field->first()->getValue();
      if (!empty($date_field_value['rrule'])) {
        $rrule = SmartDateRule::load($date_field_value['rrule']);
        $instances = $rrule->makeRuleInstances();
        $ovrds = $rrule->getRuleOverrides();
        foreach($ovrds as $ind => $ovrd) {
          $instance = $instances[$ind];
          $canceled_dates[] = $instance->getStart()->format('M j');
        }
      }

      // Return the canceled dates if they exist.
      if (!empty($canceled_dates)) {
        return [
          '#theme' => 'csc_canceled_dates_block',
          '#canceled_dates' => implode(", ", $canceled_dates),
        ];
      }
    }

    // Default message if no canceled dates are found.
    return [];
  }
}

