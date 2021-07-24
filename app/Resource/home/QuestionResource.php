<?php

namespace App\Resource\home;

use Hyperf\Resource\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'A' => $this->A,
            'B' => $this->B,
            'C' => $this->C,
            'D' => $this->D,
            'result' => '',
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }
}
