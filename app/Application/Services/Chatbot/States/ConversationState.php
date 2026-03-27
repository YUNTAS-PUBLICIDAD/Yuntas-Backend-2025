<?php

namespace App\Application\Services\Chatbot\States;

class ConversationState
{
  const ASKING_NAME = 'asking_name';
  const READY = 'ready';
  const ASKING_PROJECT_TYPE = 'asking_project_type';
  const ASKING_CONTACT = 'asking_contact';
  const CLOSING_LEAD = 'closing_lead';
}
