<?php

namespace Drupal\civicrm_entity_development_demo\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A handler to provide a filter that is completely custom by the administrator.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("civicrm_entity_development_demo_age")
 *
 * @property \Drupal\civicrm_entity\Plugin\views\query\CivicrmSql $query
 */
class Age extends NumericFilter {

  /**
   * The CiviCRM API.
   *
   * @var \Drupal\civicrm\Civicrm
   */
  protected $civicrm;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->civicrm = $container->get('civicrm');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = parent::operators();
    unset($operators['regular_expression']);
    return $operators;
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple($field) {
    $this->query->addWhereExpression($this->options['group'], "TIMESTAMPDIFF(YEAR, $field, CURDATE()) " . $this->operator . " {$this->value['value']}");
  }

  /**
   * {@inheritdoc}
   */
  protected function opBetween($field) {
    if (is_numeric($this->value['min']) && is_numeric($this->value['max'])) {
      $operator = $this->operator == 'between' ? 'BETWEEN' : 'NOT BETWEEN';
      $this->query->addWhereExpression($this->options['group'], "TIMESTAMPDIFF(YEAR, $field, CURDATE()) $operator {$this->value['min']} AND {$this->value['max']}");
    }
    elseif (is_numeric($this->value['min'])) {
      $operator = $this->operator == 'between' ? '>=' : '<';
      $this->query->addWhereExpression($this->options['group'], "TIMESTAMPDIFF(YEAR, $field, CURDATE()) $operator {$this->value['min']}");
    }
    elseif (is_numeric($this->value['max'])) {
      $operator = $this->operator == 'between' ? '<=' : '>';
      $this->query->addWhereExpression($this->options['group'], "TIMESTAMPDIFF(YEAR, $field, CURDATE()) $operator {$this->value['max']}");
    }
  }

}
