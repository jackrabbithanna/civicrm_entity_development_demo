<?php

namespace Drupal\civicrm_entity_development_demo\Plugin\views\field;

use Drupal\civicrm_entity\CiviCrmApiInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("civicrm_entity_development_demo_age")
 */
class Age extends FieldPluginBase {

  /**
   * The CiviCRM API.
   *
   * @var \Drupal\civicrm_entity\CiviCrmApiInterface
   */
  protected $civicrmApi;

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, CiviCrmApiInterface $civicrm_api) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->civicrmApi = $civicrm_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('civicrm_entity.api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->civicrmApi->civicrmInitialize();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\civicrm_entity\Entity\CivicrmEntity $entity */
    $entity = $this->getEntity($values);

    if ($entity && $entity->hasField('birth_date') && !$entity->get('birth_date')->isEmpty()) {
      /** @var \Drupal\datetime\Plugin\Field\FieldType\DateTimeItem $item */
      $item = $entity->get('birth_date')->first();
      $birth_date = $item->get('value')->getValue();

      $birth_date = \CRM_Utils_Date::customFormat($birth_date, '%Y%m%d');
      $age = \CRM_Utils_Date::calculateAge($birth_date);

      return \CRM_Utils_Array::value('years', $age);
    }

    return NULL;
  }

}
