<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use App\Models\Bank;

class ValidQuestionCount implements ValidationRule
{
    protected Bank $bank;

    // Pass the bank ID into the rule constructor
    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!$this->bank){
            $fail("Invalid bank");
        }
        if($this->bank && $value > $this->bank->questions->count()){
            $fail("Not enough questions in the bank");
        }
    }
}
