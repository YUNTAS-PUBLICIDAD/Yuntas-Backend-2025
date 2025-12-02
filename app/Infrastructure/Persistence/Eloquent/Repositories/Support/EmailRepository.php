<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Support;

use App\Domain\Repositories\Support\EmailRepositoryInterface;
use App\Models\EmailMessage;

use App\Application\DTOs\Support\SendEmailDTO;

use Illuminate\Support\Facades\Mail;


class EmailRepository implements EmailRepositoryInterface
{

    public function save(array $data)
    {
        return EmailMessage::create($data);
    }

    public function findById(int $id)
    {
        return EmailMessage::find($id);
    }


    public function send(SendEmailDTO $dto): bool
    {
        Mail::raw($dto->message, function ($mail) use ($dto) {
            $mail->to($dto->to)
                 ->subject($dto->subject);
        });

        return true;
    }
}
