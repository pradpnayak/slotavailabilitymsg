<?php
/**
 * This api exposes CiviCRM slotavailabilitymsg.
 *
 * @package CiviCRM_APIv3
 */

/**
 * Save a slotavailabilitymsg.
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_slotavailabilitymsg_getpartcount($params) {
  $optionsCount = [];
  $allStatusIds = [];
  $countedStatuses = CRM_Event_PseudoConstant::participantStatus(NULL, 'is_counted = 1');
  $allStatusIds = array_merge($allStatusIds, array_keys($countedStatuses));
  $waitingStatuses = CRM_Event_PseudoConstant::participantStatus(NULL, "class = 'Waiting'");
  $allStatusIds = array_merge($allStatusIds, array_keys($waitingStatuses));
  $maxCount = 0;

  try {
    $maxCount = civicrm_api3('PriceFieldValue', 'getvalue', [
      'return' => 'max_value',
      'id' => $params['price_id'],
    ]);

    $statusIdClause = NULL;
    if (!empty($allStatusIds)) {
      $statusIdClause = ' AND participant.status_id IN ( ' . implode(', ', array_values($allStatusIds)) . ')';
    }

    $sql = "
      SELECT
        line.qty, value.id as valueId,
        value.count, field.html_type
      FROM civicrm_line_item line
        INNER JOIN civicrm_participant participant
          ON (line.entity_table  = 'civicrm_participant'
            AND participant.id = line.entity_id)
        INNER JOIN  civicrm_price_field_value value
          ON (value.id = line.price_field_value_id)
        INNER JOIN  civicrm_price_field field
          ON (value.price_field_id = field.id)
      WHERE participant.event_id = %1
        AND line.qty > 0
        AND value.id = %2 {$statusIdClause}
    ";

    $lineItem = CRM_Core_DAO::executeQuery($sql, [
      1 => [$params['event_id'], 'String'],
      2 => [$params['price_id'], 'String'],
    ]);

    while ($lineItem->fetch()) {
      $count = $lineItem->count;
      if (!$count) {
        $count = 1;
      }
      if ($lineItem->html_type == 'Text') {
        $count *= $lineItem->qty;
      }

      $optionsCount[$lineItem->valueId] = $count + CRM_Utils_Array::value($lineItem->valueId, $optionsCount, 0);
    }
    $maxCount -= CRM_Utils_Array::value($params['price_id'], $optionsCount, 0);
  }
  catch (Exception $e) {
  }

  return $maxCount;
}
