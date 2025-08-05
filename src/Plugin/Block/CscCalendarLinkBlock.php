<?php
namespace Drupal\csc_site_blocks\Plugin\Block;

use DateTime;
use DateTimeZone;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\smart_date_recur\Entity\SmartDateRule;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to display an add to calendar link that uses the
 * Twig functions proviced by the Calendar Link Module.
 *
 * @Block(
 *   id = "csc_calendar_link_block",
 *   admin_label = @Translation("CSC Calendar Link Block"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", required = TRUE, label = @Translation("Node"))
 *   }
 * )
 */
class CscCalendarLinkBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $routeMatch;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  public function build() {
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      // csc_log("Node class: " . get_class($node));
      $date_items = $node->get('field_date');
      $item = $date_items[0];
      $start_date = $item->start_time; // \Drupal\Core\Datetime\DrupalDateTime
      $end_date = $item->end_time;     // \Drupal\Core\Datetime\DrupalDateTime
      $duration = $item->get('duration')->getValue();
      $rrid = $item->get('rrule')->getValue();
      $rrule = FALSE;
      if (!empty($rrid)) {
        $rule = SmartDateRule::load($rrid);
        $rrule = $rule->getRule();
        if (str_contains($rrule, 'UNTIL=')) {
          [$rule_bulk, $untilval] = explode('UNTIL=', $rrule);
          if (strlen($untilval) > 1) {
            $date = DateTime::createFromFormat('Y-m-d\THis', $untilval, new DateTimeZone('UTC'));
            $formatted = $date->format('Ymd\THis\Z');
            $rrule = "{$rule_bulk}UNTIL={$formatted}";
          }
        }
      }

      $date = [];

      if ($start_date) {
        $date = [[
          'start' => $start_date,
          'end' => $end_date,
          'rrule' => $rrule,
          'duration' => $duration,
          'all_day' => ($duration === 1440 || $duration === 86400),
        ]];
      }

      return [
        '#theme' => 'calendar_link_block',
        '#message' => 'Add to Calendar',
        '#node' => $node,
        '#dates' => $date,
        '#cache' => ['max-age' => 0],
      ];
    }
    return [];
  }
}
