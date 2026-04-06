<?php

namespace App\Application\Services\Chatbot\Actions;

class ConditionEvaluator
{
  public function evaluate($action, $conversation, $message)
  {
    $conditions = $action->conditions;
    if ($conditions->isEmpty()) {
      return true;
    }
    $results = [];

    foreach ($conditions as $condition) {
      $results[] = $this->check($condition, $conversation, $message);
    }
    // Simplificado: AND total
    return !in_array(false, $results, true);
  }

  protected function check($condition, $conversation, $message)
  {
    $fieldValue = $this->resolveField($condition->field, $conversation, $message);

    return match ($condition->operator) {
      '=' => $fieldValue == $condition->value,
      '!=' => $fieldValue != $condition->value,
      'contains' => str_contains($fieldValue, $condition->value),
      default => false
    };
  }

  protected function resolveField($field, $conversation, $message)
  {
    return match (true) {
      $field === 'message' => $message,
      str_starts_with($field, 'context.')=> data_get($conversation->context, str_replace('context.', '', $field)),
      default => null
    };
  }
}
