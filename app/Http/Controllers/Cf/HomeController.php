<?php namespace App\Http\Controllers\Cf;

use App\Cf\Model\Trade\Transaction;
use App\Http\Controllers\Controller;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Fadion\Bouncy\ElasticCollection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller {

    const GRID_PAGE_SIZE = 15;

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

    /**
     * Create a new controller instance.
     */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        $currentPage = abs(intval($request->get('page')));
        if ($currentPage == 0) {
            $currentPage = 1;
        }

        try {
            $transactions = Transaction::search([
                'from' => ($currentPage - 1) * self::GRID_PAGE_SIZE,
                'size' => self::GRID_PAGE_SIZE,
                'sort' => ['timePlaced' => 'desc']
            ]);
        }
        catch (Missing404Exception $e) {
            $transactions = new ElasticCollection(
                [
                    'hits' => [
                        'hits' => [],
                        'total' => 0
                    ]
                ],
                []
            );
        }

        $paginator = new LengthAwarePaginator($transactions, $transactions->total(), self::GRID_PAGE_SIZE, $currentPage);

        return view(
            'cf.home',
            [
                'transactions' => $transactions,
                'currentPage' => $currentPage,
                'paginatorHtml' => $paginator,
            ]
        );
	}

}
