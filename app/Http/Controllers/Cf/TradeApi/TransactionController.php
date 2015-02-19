<?php namespace App\Http\Controllers\Cf\TradeApi;

use App\Cf\Model\Trade\Transaction;
use App\Cf\ResponseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Validation\ValidationException;

class TransactionController extends Controller {

    use ResponseTrait;

	/**
	 * Store a new trade transaction
	 *
	 * @return Response
	 */
	public function store()
	{
        $postedData = Request::instance()->getContent();
        if (empty($postedData)) {
            return $this->returnJsonResponse('0', 'Empty input');
        }

        $postedData = json_decode($postedData, true);
        if (is_null($postedData) || empty($postedData)) {
            return $this->returnJsonResponse('0', 'Invalid JSON');
        }

        try {
            $transaction = new Transaction($postedData);
            $transaction->index();
        }
        catch (MassAssignmentException $e) {
            return $this->returnJsonResponse('0', $e->getMessage());
        }
        catch (ValidationException $e) {
            return $this->returnJsonResponse('0', $e->getMessageProvider()->getMessageBag()->first());
        }
        catch (\Exception $e) {
            return $this->returnJsonResponse('0', 'Technical problems');
        }

		return $this->returnJsonResponse('1');
	}

    public function index()
    {
        return 123;
    }
}
