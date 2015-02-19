<?php namespace App\Cf\Model\Trade;

use Fadion\Bouncy\BouncyTrait;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class Transaction extends Model {
    use BouncyTrait {
        index as traitIndex;
    }

    /**
     * @var string Elasticsearch type
     */
    protected $typeName = 'transaction';

    protected $fillable = [
        'userId',
        'currencyFrom',
        'currencyTo',
        'amountSell',
        'amountBuy',
        'rate',
        'timePlaced',
        'originatingCountry'
    ];

    protected $attributeValidationRules = [
        'userId' => ['required', 'regex:/^\d+$/'],
        'currencyFrom' => ['required', 'currency'],
        'currencyTo' => ['required', 'currency'],
        'amountSell' => ['required', 'regex:/^(([1-9]\d*(\.\d{1,2})?)|(0\.\d{1,2}))$/'],
        'amountBuy' => ['required', 'regex:/^(([1-9]\d*(\.\d{1,2})?)|(0\.\d{1,2}))$/'],
        'rate' => ['required', 'regex:/^(([1-9]\d*(\.\d{1,4})?)|(0\.\d{1,4}))$/'],
        'timePlaced' => ['required', 'date'],
        'originatingCountry' => ['required', 'country_iso2'],
    ];
    protected $attributeValidationMessages = [
        'userId.regex' => 'Wrong user ID format',
        'userId.required' => 'Wrong user ID format',
        'currencyFrom.required' => 'Wrong currency code',
        'currencyFrom.currency' => 'Wrong currency code',
        'currencyTo.required' => 'Wrong currency code',
        'currencyTo.currency' => 'Wrong currency code',
        'amountSell.required' => 'Wrong amount format',
        'amountBuy.required' => 'Wrong amount format',
        'amountSell.regex' => 'Wrong amount format',
        'amountBuy.regex' => 'Wrong amount format',
        'rate.required' => 'Wrong currency rate',
        'rate.regex' => 'Wrong currency rate',
        'timePlaced.required' => 'Wrong date/time format',
        'timePlaced.date' => 'Wrong date/time format',
        'originatingCountry.required' => 'Wrong country ISO2 code',
        'originatingCountry.country_iso2' => 'Wrong country ISO2 code',
    ];

    private function validate()
    {
        $mandatoryAttributesDiff = array_diff($this->fillable, array_keys($this->attributes));
        if (count($mandatoryAttributesDiff) > 0) {
            throw new ValidationException(new MessageBag(array('Wrong parameters')));
        }

        $validationResult = Validator::make(
            $this->attributes,
            $this->attributeValidationRules,
            $this->attributeValidationMessages
        );

        if ($validationResult->fails()) {
            throw new ValidationException($validationResult->messages());
        }

        if (
            $this->attributes['amountBuy'] == 0
            || $this->attributes['amountSell'] == 0
            || $this->attributes['amountBuy'] / $this->attributes['amountSell'] != $this->attributes['rate']
            )
        {
            throw new ValidationException(new MessageBag(array('Wrong currency rate')));
        }

        if (!is_numeric($this->attributes['timePlaced'])) {
            $this->attributes['timePlaced'] = strtotime($this->attributes['timePlaced']);
        }
    }

    /**
     * Indexes the model in Elasticsearch. Validate first
     *
     * @return array
     */
    public function index()
    {
        $this->validate();
        return $this->traitIndex();
    }
}
