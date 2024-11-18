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
      // csc_log(json_encode($date_field_value));
      /*
      if (isset($date_field_value['rrule'])) {
        csc_log("The Rule OVerrids");
        $smart_date_rule = SmartDateRule::load($date_field_value['rrule']);
        $start_date = new DrupalDateTime('-1 year'); // Start date (e.g., today)
        $end_date = new DrupalDateTime('+1 year'); // End date (e.g., 1 year from now)
        $instances = $smart_date_rule->makeRuleInstances($start_date, $end_date)->toArray();
        csc_log("instances: " . json_encode($instances));

        $rid = $date_field_value['rrule'];
        $database = Database::getConnection();

        // Perform a query to fetch rows where entity_index = 3.
        $query = $database->select('smart_date_rule', 'd')
          ->fields('d')
          ->condition('rid', $rid, '=');

        // Execute the query.
        $result = $query->execute();

        // Fetch all rows as associative arrays.
        $rows = $result->fetchAllAssoc('rid');
        csc_log("Table rows: " . json_encode($rows));
      }*/

      // Return the canceled dates if they exist.
      if (!empty($canceled_dates)) {
        return [
          '#theme' => 'item_list',
          '#items' => $canceled_dates,
          '#title' => $this->t('Canceled Dates'),
        ];
      }
    }

    // Default message if no canceled dates are found.
    return [];
  }
}

